<?php

namespace App\Http\Controllers\V1\Member;

use App\Exceptions\NotesException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Member\Index\CreateUserRequest;
use App\Http\Requests\V1\Member\Index\EditRemarkRequest;
use App\Http\Requests\V1\Member\Index\EditTagRequest;
use App\Http\Requests\V1\Member\Index\EditUserRequest;
use App\Http\Requests\V1\Member\Index\SystemAnalysisRequest;
use App\Http\Requests\V1\Member\Index\UserInfoRequest;
use App\Http\Requests\V1\Member\Index\UserListRequest;
use App\Http\Resources\V1\User\UserResource;
use App\Services\V1\TagService;
use App\Services\V1\User\UserService;
use App\Utils\Helper;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Throwable;

class IndexController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 商家手动添加用户
     * @param CreateUserRequest $request
     * @return mixed
     * @throws Throwable
     */
    public function createUser(CreateUserRequest $request)
    {
        $data = $request->validated();
        $this->userService->addClient($data);

        return $this->success([], "操作成功");
    }

    /**
     * 商家手动修改用户
     * @param EditUserRequest $request
     * @return mixed
     * @throws Exception
     */
    public function editUser(EditUserRequest $request)
    {
        $data = $request->validated();

        $hqUser = $this->userService->edit($data);
        return $this->success(new UserResource($hqUser));
    }

    /**
     * 商家修改用户备注
     *
     * @param EditRemarkRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function editRemark(EditRemarkRequest $request)
    {
        $data = $request->validated();

        $hqUser = $this->userService->editRemark($data);
        return $this->success(new UserResource($hqUser));
    }

    /**
     * 商家修改用户标签
     *
     * @param EditTagRequest $request
     * @return array|mixed
     * @throws Exception
     */
    public function editTag(EditTagRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['mobile'])) {
            if (!Helper::checkMobile($data['mobile'])) {
                throw new NotesException("请输入正确的手机号");
            }
        }

        $this->userService->editTag($data);
        return $this->success([], "操作成功");
    }

    /**
     * 获取店铺的客户列表
     * @param UserListRequest $request
     * @return array|mixed
     * @throws Exception|Throwable
     */
    public function userList(UserListRequest $request)
    {
        $data = $request->validated();
        $userList = $this->userService->getListByAdmin($data);

        return $this->success(["list" => UserResource::collection($userList)]);
    }

    /**
     * 获取用户信息
     * @param UserInfoRequest $request
     * @return mixed
     * @throws Exception
     */
    public function userInfo(UserInfoRequest $request)
    {
        $data = $request->validated();

        $userId = (int)$data['user_id'];
        $info = $this->userService->getUserInfoById($userId);
        return $this->success(new UserResource($info));
    }

    /**
     * 获取用户标签列表
     * @throws Exception
     */
    public function tagList()
    {
        /** @var TagService $tagService */
        $tagService = app(TagService::class);

        $list = $tagService->getTagListByAdmin();

        return $this->success(["list" => $list]);
    }

    /**
     * 获取用户的分析
     *
     * @param SystemAnalysisRequest $request
     * @return array|mixed
     * @throws NotFound
     */
    public function systemAnalysis(SystemAnalysisRequest $request)
    {
        $data = $request->validated();
        $userId = (int)$data['user_id'];
        $systemAnalysis = $this->userService->getUserAnalysis($userId);

        return $this->success(['list' => $systemAnalysis]);
    }
}
