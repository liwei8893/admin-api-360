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
     * @param array $attributes
     * @param string $type
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
     * Return the template id of message.
     */
    public function getTemplate(GatewayInterface $gateway = null): string
    {
        return 'SMS_179670041';
    }
}
