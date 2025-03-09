<?php

declare(strict_types=1);

namespace App\System\Controller;

use App\System\Service\SystemQueueMessageService;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\WebSocketServer\Context;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class ServerController.
 */
class ServerController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    /**
     * 成功连接到 ws 回调.
     * @param Response|Server $server
     * @param Request $request
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onOpen($server, $request): void
    {
        $uid = user()->getUserInfo(
            container()->get(ServerRequestInterface::class)->getQueryParams()['token']
        )['id'];
        Context::set('uid', $uid);

        //        console()->info(
        //            "WebSocket [ user connection to message server: id > {$uid}, " .
        //            "fd > {$request->fd}, time > " . date('Y-m-d H:i:s') . ' ]'
        //        );
    }

    /**
     * 消息回调.
     * @param Response|Server $server
     * @param Frame $frame
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|JsonException
     */
    public function onMessage($server, $frame): void
    {
        $data = json_decode($frame->data, true, 512, JSON_THROW_ON_ERROR);
        switch ($data['event']) {
            case 'get_unread_message':
                $service = container()->get(SystemQueueMessageService::class);
                $server->push($frame->fd, json_encode([
                    'event' => 'ev_new_message',
                    'message' => 'success',
                    'data' => $service->getUnreadMessage(Context::get('uid'))['items'],
                ], JSON_THROW_ON_ERROR));
                break;
        }
    }

    /**
     * 关闭 ws 连接回调.
     * @param Response|\Swoole\Server $server
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        //        console()->info(
        //            'WebSocket [ user close connect for message server: id > ' . Context::get('uid') . ', ' .
        //            "fd > {$fd}, time > " . date('Y-m-d H:i:s') . ' ]'
        //        );
    }
}
