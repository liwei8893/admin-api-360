<?php

declare(strict_types=1);

namespace App\System\Service\Dependencies\Sms;

use Exception;
use Mine\Helper\Tool;
use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Message;

class ForgotPwdMessage extends Message
{
    /**
     * @throws Exception
     */
    public function __construct(array $attributes = [], string $type = MessageInterface::TEXT_MESSAGE)
    {
        parent::__construct($attributes, $type);
        $this->data = [
            'code' => Tool::salt(),
        ];
    }

    /**
     * 定义使用模板发送方式平台所需要的模板 ID
     * 验证码${code}，您正在尝试修改登录密码，请妥善保管账户信息。
     */
    public function getTemplate(GatewayInterface $gateway = null): string
    {
        return 'SMS_465322090';
    }
}
