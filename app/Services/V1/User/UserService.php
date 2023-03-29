<?php

namespace App\Services\V1\User;

use App\Exceptions\DbErrorException;
use App\Exceptions\NotesException;
use App\Models\Store\StoreUserModel;
use App\Models\System\SmsCodeModel;
use App\Models\User\UserModel;
use App\Repositories\V1\UserRepository;
use App\Services\V1\BaseService;
use App\Services\V1\SystemService;
use App\Utils\Helper;
use EasyWeChat\Factory;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService extends BaseService
{
    protected $userRepo;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    /**
     * 用户注册
     *
     * @param array $data 用户数据
     * @return UserModel
     * @throws Exception
     * @throws Throwable
     */
    public function mobileLogin(array $data): UserModel
    {
        //判断验证码是否有误
        /** @var SystemService $systemService */
        $systemService = app(SystemService::class);
        $systemService->verifySmsCode($data['mobile'], $data["sms_code"], SmsCodeModel::TYPE_CLIENT_LOGIN);

        //判断手机号是否已经注册
        $user = null;
        try {
            $user = $this->userRepo->getUserByMobile($data['mobile']);
        } catch (NotFound $nf) {
            //未注册，进行注册
            $admin = Helper::admin();
            $data['hq_id'] = $admin->last_hq_id;
            return $this->userRepo->addClient($data);
        }

        $user->checkStatus();

        //已经注册，直接登录
        $refreshToken = true;
        if ((time() - strtotime($user->token_time)) < (config("auth.token_timeout") * 60)) {
            $refreshToken = false;
        }

        //生成token
        if ($refreshToken) {
            $user->token = $this->userRepo->generateToken();
            $res = $user->save();
            if (!$res) {
                throw new Exception("登录失败，请检查网络");
            }
        }
        return $user;
    }

    /**
     * 忘记密码，修改密码
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function forgetPass(array $data): bool
    {
        /** @var SystemService $systemService */
        $systemService = app(SystemService::class);
        $systemService->verifySmsCode($data['mobile'], $data["sms_code"], SmsCodeModel::TYPE_MODIFY_PASS);

        $updateData = array(
            "password" => $data["password"]
        );

        $user = null;
        $user = $this->userRepo->getUserByMobile($data['mobile']);

        if (!empty($user->deleted_at)) {
            throw new NotesException("手机号未注册");
        }

        $flag = $this->userRepo->updateUserInfo($user, $updateData);
        if (!$flag) {
            return false;
        }

        return true;
    }

    /**
     * 添加客户
     *
     * @param array $data
     * @return UserModel
     * @throws Throwable
     */
    public function addClient(array $data)
    {
        $tags = json_decode($data['tags'] ?? "[]", 1);
        $mobile = $data['mobile'];

        $user = null;
        DB::beginTransaction();
        try {
            //判断用户是否已存在
            $user = $this->userRepo->getUserByMobile($mobile);
        } catch (NotFound $nf) {
            //账号不存在，新增用户
            $admin = Helper::admin();
            $data['hq_id'] = $admin->last_hq_id;
            $user = $this->userRepo->addClient($data);
        }
        //用户绑定标签
        $this->userRepo->addUserTag($user->id, $tags);

        DB::commit();
        return $user;
    }

    /**
     * 编辑用户信息
     *
     * @param array $data
     * @return UserModel
     * @throws NotFound
     * @throws Exception
     */
    public function edit(array $data): UserModel
    {
        $mobile = $data["mobile"];
        $tags = json_decode($data['tags'] ?? "[]", 1);

        if (!empty($mobile)) {
            $use = $this->userRepo->checkMobileUse($data['user_id'], $mobile);
            if ($use) {
                throw new NotesException("手机号已被注册");
            }
        }

        $admin = Helper::admin();
        $hqUser = $this->userRepo->getHqUserById($admin, $data['user_id']);

        DB::beginTransaction();
        //修改用户基础信息
        $hqUser = $this->userRepo->editUser($hqUser, $data);

        //修改用户标签
        $this->userRepo->addUserTag($hqUser->id, $tags);

        DB::commit();

        return $hqUser;
    }

    /**
     * 获取用户列表
     *
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function getListByAdmin(array $data)
    {
        $admin = Helper::admin();
        return $this->userRepo->getUserList($admin, $data);
    }

    /**
     * 根据TOKEN获取用户信息
     *
     * @param string $token
     * @return UserModel
     * @throws NotFound
     */
    public function getUserByToken(string $token): UserModel
    {
        return $this->userRepo->getUserByToken($token);
    }

    /**
     * 根据用户Id获取用户信息
     *
     * @param int $userId 用户ID
     * @return UserModel
     * @throws Exception
     */
    public function getUserInfoById(int $userId): UserModel
    {
        $admin = Helper::admin();
        return $this->userRepo->getHqUserById($admin, $userId);
    }

    /**
     * 修改用户备注
     * @param array $data
     * @return UserModel
     * @throws Exception
     */
    public function editRemark(array $data): UserModel
    {
        $admin = Helper::admin();
        $hqUser = $this->userRepo->getHqUserById($admin, $data['user_id']);

        $hqUser->remark = $data['remark'] ?? "";
        $hqUser->save();

        return $hqUser;
    }

    /**
     * 修改标签
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function editTag(array $data): bool
    {
        $userId = $data['user_id'];
        $tags = json_decode($data['tags'], 1);
        $admin = Helper::admin();
        $this->userRepo->getHqUserById($admin, $data['user_id']);

        DB::beginTransaction();
        //先删除标签
        $this->userRepo->delTags($userId);
        //添加标签
        $this->userRepo->addUserTag($userId, $tags);
        DB::commit();

        return true;
    }

    /**
     * 用户分析
     *
     * @param int $userId 用户ID
     * @return array
     * @throws NotFound
     * @throws Exception
     */
    public function getUserAnalysis(int $userId)
    {
        $list = [];
        $admin = Helper::admin();
        //查询用户多久没来消费了
        $userInfo = $this->userRepo->getHqUserById($admin, $userId);
        if (empty($userInfo->last_consumption_time)) {
            $list[] = [
                "type" => "unconsumed",
                "title" => "去通知TA",
                "message" => "客户未在店内消费过"
            ];
        } else {
            $day = (time() - $userInfo->last_consumption_time->timestamp) / (24 * 3600);
            if ($day > 30) {
                $list[] = [
                    "type" => "timeout-unconsumed",
                    "title" => "去通知TA",
                    "message" => "客户已经超过" . intval($day) . "天未来店里消费了"
                ];
            }

            //查询用户喜欢什么标签的商品
            $tags = $this->userRepo->getUserOrderTags($userId, 10);
            if (count($tags) > 0) {
                $tagStr = $ext = "";
                foreach ($tags as $tag) {
                    $tagStr .= $ext . $tag->tag_name . "(" . $tag->num . ")";
                    $ext = ",";
                }

                $list[] = [
                    "type" => "new-product",
                    "title" => "去告诉TA，有新品哦",
                    "message" => "客户最喜欢的商品标签和消费次数：{$tagStr}"
                ];
            }
        }

        //@todo 分析生日

        //@todo 赠送商品对用户的吸引力
        return $list;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function editUser(array $data)
    {
        $user = Helper::user();

        $this->userRepo->updateUserInfo($user, $data);
    }

    /**
     * 获取用户资料
     * @return StoreUserModel
     * @throws Exception
     */
    public function storeUserInfo()
    {
        $user = Helper::user();

        if ($user->login_type != UserModel::LOGIN_TYPE_USER) {
            $storeUserInfo = new StoreUserModel();
            $storeUserInfo->level_id = 0;
            $storeUserInfo->user = $user;

            return $storeUserInfo;
        }

        if ($user->login_store_id) {
            return $user->storeUser;
        }

        if ($user->login_hq_id) {
            return $user->hqUser;
        }

        $storeUserInfo = new StoreUserModel();
        $storeUserInfo->level_id = 0;
        $storeUserInfo->user = $user;

        return $storeUserInfo;
    }

    /**
     * 授权手机号
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function authorizationMobile(array $data)
    {
        $iv = $data['iv'];
        $code = $data['code'];
        $encryptedData = $data['encryptedData'];

        $app = Factory::miniProgram(config("wechat.miniprogram"));
        $session = $app->auth->session($code);
        if (empty($session['session_key'])) {
            throw new NotesException("授权失败，请重新授权");
        }

        $data = [];
        try {
            $data = $app->encryptor->decryptData($session['session_key'], $iv, $encryptedData);
        } catch (Exception $e) {
            throw new NotesException("授权失败，请重新授权");
        }

        if (empty($data['phoneNumber'])) {
            throw new NotesException("授权失败，请重新授权");
        }

        $userInfo = Helper::user();
        $this->userRepo->updateUserInfo($userInfo, ['mobile' => $data['phoneNumber']]);

        return ['mobile' => $data['phoneNumber']];
    }
}
