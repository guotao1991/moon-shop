<?php

namespace App\Services\V1\WeChat;

use App\Services\V1\BaseService;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\OfficialAccount\Application as OfficialAccount;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WeChatService
 * @package App\Services\V1\WeChat
 *
 */
class WeChatService extends BaseService
{
    /**
     * 回应微信服务器请求
     *
     * @return Response
     * @throws Exception
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        /** @var OfficialAccount $app */
        $app = app('wechat.official_account');
            $app->server->push(
                function ($message) use ($app) {
                    Log::info("wx user message:" . json_encode($message));
                    switch ($message['MsgType']) {
                        case 'event':
                            if ($message['Event'] == "subscribe") {
                                //关注公众号
                                //$this->subscribe($message);

                                $user = $app->user->get($message['FromUserName']);
                                Log::info("wx user info:" . json_encode($user));
                                return "欢迎关注";
                            }
                            break;
                    }
                    return "";
                }
            );

        return $app->server->serve()->send();
    }

    /**
     * 关注公众号
     * @param array $message
     */
    public function subscribe(array $message)
    {
        $openid = $message['FromUserName'];
        try {
            $app = Factory::officialAccount(config('wechat'));
            $user = $app->user->get($openid);
            Log::info("wx user info:" . json_encode($user));
        } catch (Exception $e) {
            Log::info("报错了");
        }
    }
}
