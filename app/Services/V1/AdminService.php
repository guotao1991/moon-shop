<?php

namespace App\Services\V1;

use App\Exceptions\NotesException;
use App\Models\Admin\AdminModel;
use App\Models\Store\HqModel;
use App\Models\Store\StoreUserModel;
use App\Models\System\SmsCodeModel;
use App\Models\User\UserModel;
use App\Repositories\V1\AdminRepository;
use App\Repositories\V1\RoleRepository;
use App\Repositories\V1\UserRepository;
use App\Utils\Helper;
use EasyWeChat\Factory;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdminService extends BaseService
{
    protected $adminRepo;

    /** @var int 去店铺详情界面 */
    public const GO_STORE_INFO = 1;
    /** @var int 去店铺列表选择界面（多店铺用户） */
    public const GO_STORE_SELECT = 2;
    /** @var int 去店铺管理界面 */
    public const GO_ADMIN_INFO = 3;
    /** @var int 去管理员选择界面（多店铺管理员） */
    public const GO_ADMIN_SELECT = 4;

    /** @var int 不是新用户 */
    public const NEW_USER_FALSE = 1;
    /** @var int 是新用户 */
    public const NEW_USER_TRUE = 2;

    /**
     * @param AdminRepository $adminRepo
     */
    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    /**
     * 用户注册
     *
     * @param array $data 用户数据
     * @return AdminModel
     * @throws Exception
     * @throws Throwable
     */
    public function mobileLogin(array $data): AdminModel
    {
        //判断验证码是否有误
        $this->systemService->verifySmsCode($data['mobile'], $data["sms_code"], SmsCodeModel::TYPE_ADMIN_LOGIN);

        //判断手机号是否已经注册
        $admin = null;
        try {
            $admin = $this->adminRepo->getAdminByMobile($data['mobile']);
        } catch (NotFound $nf) {
            //未注册，进行注册
            return $this->adminRepo->addAdmin($data);
        }

        $admin->checkStatus();

        //已经注册，直接登录
        $refreshToken = true;
        if ((time() - strtotime($admin->token_time)) < (config("auth.token_timeout") * 60)) {
            $refreshToken = false;
        }

        //生成token
        if ($refreshToken) {
            $admin->token = $this->adminRepo->generateToken();
            $admin->token_time = date("Y-m-d H:i:s");
            $res = $admin->save();
            if (!$res) {
                throw new Exception("登录失败，请检查网络");
            }
        }
        return $admin;
    }

    /**
     * 密码登录
     *
     * @param array $data
     * @return AdminModel
     *
     * @throws Exception
     */
    public function passLogin(array $data): AdminModel
    {
        $admin = $this->adminRepo->getAdminByMobile($data['mobile']);

        if ($admin->pass_error_num > 3 && time() - strtotime($admin->updated_at) < 300) {
            $admin->increment("pass_error_num");
            throw new NotesException("密码错误次数过多，请5分钟后重试");
        }

        if ($admin->password !== MD5($data['password'])) {
            $admin->increment("pass_error_num");
            throw new NotesException("密码错误");
        }

        $admin->checkStatus();

        //已经注册，直接登录
        $refreshToken = true;
        if ((time() - strtotime($admin->token_time)) < (config("auth.token_timeout") * 60)) {
            $refreshToken = false;
        }

        //生成token
        if ($refreshToken) {
            $admin->token = $this->adminRepo->generateToken();
            $admin->token_time = date("Y-m-d H:i:s");
        }

        $admin->last_login_time = date("Y-m-d H:i:s");

        $res = $admin->save();
        if (!$res) {
            throw new Exception("登录失败，请检查网络");
        }

        return $admin;
    }

    /**
     * 微信登录
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function wxLogin(array $data)
    {
        //根据Code 获取用户信息
        $code = $data['code'];
        $app = Factory::miniProgram(config("wechat.miniprogram"));
        // 通过 code 获取 session 对象
        $session = $app->auth->session($code);

        if (empty($session['unionid'])) {
            throw new NotesException("授权过期，请重新授权");
        }

        //查询用户是否已经存在
        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $userInfo = null;
        $isNew = self::NEW_USER_FALSE;
        try {
            $userInfo = $this->adminRepo->getUserByUnionId($session['unionid']);
        } catch (NotFound $e) {
            //新增用户
            $userInfo = $userRepo->addClient($session);
            $isNew = self::NEW_USER_TRUE;
        }

        $goPage = self::GO_STORE_INFO;
        //如果是老用户，需要判断是普通用户还是管理员账号
        if ($isNew === self::NEW_USER_FALSE) {
            //没有授权，只能去用户界面
            //判断用户是否是管理员
            if (
                $userInfo->is_authorized == UserModel::AUTHORIZED_TRUE &&
                !empty($userInfo->admin) &&
                $userInfo->admin->status == AdminModel::STATUS_NORMAL
            ) {
                //用户是管理员，查询用户是否是多个店铺管理员
                $count = $userInfo->admin->hqList->count();
                if ($count > 1) {
                    $goPage = AdminService::GO_ADMIN_SELECT;
                } elseif ($count == 1) {
                    $goPage = AdminService::GO_ADMIN_INFO;
                    /** @var HqModel $hq */
                    $hq = $userInfo->admin->hqList->first();
                    $userInfo->login_hq_id = $hq->id;
                    $userInfo->login_store_id = 0;
                }
                //管理员登录
                $userInfo->login_type = UserModel::LOGIN_TYPE_ADMIN;
            } else {
                //是普通用户，需要获取用户的会员卡列表，注册了多个店铺的情况
                $count = $userInfo->storeUsers->count();
                if ($count > 1) {
                    //多家店铺会员
                    $goPage = AdminService::GO_STORE_SELECT;
                } elseif ($count == 1) {
                    /** @var StoreUserModel $storeUserInfo */
                    $storeUserInfo = $userInfo->storeUsers->first();
                    $userInfo->login_store_id = $storeUserInfo->store_id;
                    $userInfo->login_hq_id = $storeUserInfo->hq_id;
                }
                $userInfo->login_type = UserModel::LOGIN_TYPE_USER;
            }
        }
        $userInfo = $this->adminRepo->login($userInfo);

        return [$isNew, $goPage, $userInfo];
    }

    /**
     * 开发登录
     *
     * @return array
     * @throws Exception
     */
    public function devLogin()
    {
        $session = [
            "unionid" => 'oePUq6-wMToi2FA5iAndysSI0qF0',
            "openid" => 'ornsU5ITP-JEgdNN1uz6xH-O7pyU'
        ];

        //查询用户是否已经存在
        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $userInfo = null;
        $isNew = self::NEW_USER_FALSE;
        try {
            $userInfo = $this->adminRepo->getUserByUnionId($session['unionid']);
        } catch (NotFound $e) {
            //新增用户
            $userInfo = $userRepo->addClient($session);
            $isNew = self::NEW_USER_TRUE;
        }

        $goPage = self::GO_STORE_INFO;
        //如果是老用户，需要判断是普通用户还是管理员账号
        if ($isNew === self::NEW_USER_FALSE) {
            //没有授权，只能去用户界面
            //判断用户是否是管理员
            if (
                $userInfo->is_authorized == UserModel::AUTHORIZED_TRUE &&
                !empty($userInfo->admin) &&
                $userInfo->admin->status == AdminModel::STATUS_NORMAL
            ) {
                //用户是管理员，查询用户是否是多个店铺管理员
                $count = $userInfo->admin->hqList->count();
                if ($count > 1) {
                    $goPage = AdminService::GO_ADMIN_SELECT;
                } elseif ($count == 1) {
                    $goPage = AdminService::GO_ADMIN_INFO;
                    /** @var HqModel $hq */
                    $hq = $userInfo->admin->hqList->first();
                    $userInfo->login_hq_id = $hq->id;
                    $userInfo->login_store_id = 0;
                }
                //管理员登录
                $userInfo->login_type = UserModel::LOGIN_TYPE_ADMIN;
                $userInfo->save();
            } else {
                //是普通用户，需要获取用户的会员卡列表，注册了多个店铺的情况
                $count = $userInfo->storeUsers->count();
                if ($count > 1) {
                    //多家店铺会员
                    $goPage = AdminService::GO_STORE_SELECT;
                } elseif ($count == 1) {
                    /** @var StoreUserModel $storeUserInfo */
                    $storeUserInfo = $userInfo->storeUsers->first();
                    $userInfo->login_store_id = $storeUserInfo->store_id;
                    $userInfo->login_hq_id = $storeUserInfo->hq_id;
                }
                $userInfo->login_type = UserModel::LOGIN_TYPE_USER;
                $userInfo->save();
            }
        }

        return [$isNew, $goPage, $userInfo];
    }

    /**
     * 忘记密码，修改密码
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function forgetPass(array $data): bool
    {
        $this->systemService->verifySmsCode($data['mobile'], $data["sms_code"], SmsCodeModel::TYPE_MODIFY_PASS);

        $updateData = array(
            "password" => $data["password"]
        );

        $admin = null;
        $admin = $this->adminRepo->getAdminByMobile($data['mobile']);

        if (!empty($admin->deleted_at)) {
            throw new NotesException("手机号未注册");
        }

        $flag = $this->adminRepo->updateAdminInfo($admin, $updateData);
        if (!$flag) {
            return false;
        }

        return true;
    }

    /**
     * 根据token获取用户信息
     *
     * @param string $token token
     * @return AdminModel
     * @throws NotFound
     */
    public function getAdminByToken(string $token): AdminModel
    {
        return $this->adminRepo->getAdminByToken($token);
    }

    /**
     * 修改密码
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updatePass(array $data): bool
    {
        $admin = Helper::admin();
        $this->systemService->verifySmsCode($admin->mobile, $data["sms_code"], SmsCodeModel::TYPE_MODIFY_PASS);

        $admin->password = MD5($data['password']);
        $res = $admin->save();
        if ($res === false) {
            throw new NotesException("修改失败");
        }
        return true;
    }

    /**
     * 获取管理员针对店铺的类型
     *
     * @param int $storeId 店铺ID
     * @return int 1：店铺管理员，2：HQ管理员
     * @throws Exception
     */
    public function adminTypeByStoreId(int $storeId)
    {
        $admin = Helper::admin();
        //查询是否是店员
        $res = $this->adminRepo->getAdminStoreCount($admin->id, $storeId);

        if ($res) {
            return 1;
        }
        //查询是否是管理员
        $storeInfo = $this->storeService->getStoreById($storeId);
        $res = $this->adminRepo->getAdminHqCount($admin->id, $storeInfo->hq_id);

        if ($res) {
            return 2;
        }

        throw new Exception("您不是店铺管理员");
    }

    /**
     * 新增管理员
     * @param array $data
     * @return AdminModel
     * @throws Throwable
     */
    public function addManager(array $data): AdminModel
    {
        $admin = Helper::admin();

        //查询角色信息
        $this->roleRepo->getRoleInfoById($admin->last_hq_id, $data['role_id']);

        $storeIds = json_decode($data['store_ids'], 1);

        $storeList = $this->storeService->getAdminStoreByIds($admin->last_hq_id, $storeIds);

        if (count($storeList) <= 0) {
            throw new NotesException("请选择店铺");
        }

        DB::beginTransaction();
        //添加管理员
        $manager = $this->adminRepo->addManger($admin, $data);

        foreach ($storeList as $store) {
            //添加管理员绑定的店铺
            $this->adminRepo->relationAdminStore($manager->id, $store->id, $data['role_id']);
        }
        DB::commit();
        return $manager;
    }

    /**
     * @param UserModel $user
     * @return int
     */
    public function getHqCountByUser(UserModel $user)
    {
        return $this->adminRepo->getHqCountByUser($user->id);
    }

    /**
     * 根据用户获取管理员详情
     * @param UserModel $user
     * @return AdminModel
     * @throws NotFound
     */
    public function getAdminByUser(UserModel $user)
    {
        return $this->adminRepo->getAdminByUserId($user->id);
    }

    /**
     * 添加管理员账号
     * @return AdminModel
     * @throws Exception
     */
    public function addAdmin()
    {
        $user = Helper::user();
        return $this->adminRepo->addAdmin(['user_id' => $user->id]);
    }
}
