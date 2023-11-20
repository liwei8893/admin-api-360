<?php

declare(strict_types=1);

namespace App\Operation\Service;

use App\Operation\Mapper\WxMsgMapper;
use App\Operation\Model\WxMsg;
use App\Users\Model\User;
use App\Users\Service\UsersService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Hyperf\Amqp\Producer;
use Hyperf\Coroutine\Exception\ParallelExecutionException;
use Hyperf\Coroutine\Parallel;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Pengxuxu\HyperfWechat\EasyWechat;

/**
 * 微信消息服务类.
 */
class WxMsgService extends AbstractService
{
    /**
     * @var WxMsgMapper
     */
    public $mapper;

    #[Inject]
    protected UsersService $usersService;

    #[Inject]
    protected Producer $producer;

    public function __construct(WxMsgMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        $this->handleData($data);
        return parent::save($data);
    }

    /**
     * 更新一条数据.
     */
    public function update(int $id, array $data): bool
    {
        $this->handleData($data);
        return parent::update($id, $data);
    }

    /**
     * 微信定时消息.
     */
    public function addQueueWxMsg(): bool
    {
        // 查询未发送的一条消息
        $dataMsg = $this->mapper->getFirstUnsentMsg();
        if (! $dataMsg) {
            throw new NormalStatusException('暂无消息发送！');
        }
        $userData = $this->mapper->getSendUsers();
        if ($userData->isEmpty()) {
            throw new NormalStatusException('未查询到发送人员！');
        }
        // 循环推送到队列
        $setData = [];
        /* @var User $user */
        foreach ($userData as $user) {
            $setData[] = [
                'url' => 'https://h5.hxt360.com',
                'touser' => $user->wxgzh_openid,
                'template_id' => $dataMsg->tmp_id,
                'client_msg_id' => $dataMsg->id,
                'data' => [
                    'first' => ['value' => $dataMsg->first],
                    'keyword1' => ['value' => $dataMsg->keyword1],
                    'keyword2' => ['value' => $dataMsg->keyword2],
                    'keyword3' => ['value' => $dataMsg->keyword3],
                    'remark' => ['value' => $dataMsg->remark],
                ],
            ];
            //            $message = new SendWxMsgProducer($data);
            //            $this->producer->produce($message);
        }
        $dataMsg->status = WxMsg::SENT;
        $dataMsg->save();
        return $this->sendWxMsg($setData);
    }

    public function sendLearningReportWxMsg(): bool
    {
        $userData = $this->mapper->getSendUsers(['id' => [83775, 133690, 8683, 3984, 3935, 3969, 16755, 19285, 18524, 19363, 61674, 85721, 90130, 157389]]);
        //        $userData = $this->mapper->getSendUsers(['grade_id' => [1, 2, 5, 7, 9, 11, 14, 13, 12]]);
        if ($userData->isEmpty()) {
            throw new NormalStatusException('未查询到发送人员！');
        }
        $setData = [];
        foreach ($userData as $user) {
            $setData[] = [
                'url' => 'https://h5.hxt360.com/spa/learning-report',
                'touser' => $user->wxgzh_openid,
                'template_id' => 'VygHZ1pOK8czJkS7Xc1uJi4au3nI25W0NXM9-Z2dwCk',
                'data' => [
                    'keyword1' => ['value' => '学情报告'],
                    'keyword2' => ['value' => '点击查看上周学情报告!'],
                ],
            ];
        }
        return $this->sendWxMsg($setData);
    }

    /**
     * 发送微信消息.
     */
    public function sendWxMsg(array $setData): bool
    {
        console()->info('开始发送微信消息');
        $app = EasyWechat::officialAccount();
        $accessToken = $app->getAccessToken()->getToken();
        // 创建带连接池的客户端
        $client = \Hyperf\Support\make(Client::class);
        // 控制并发数
        $parallel = new Parallel(30);
        foreach ($setData as $data) {
            $parallel->add(function () use ($client, $accessToken, $data) {
                $response = $client->post('https://api.weixin.qq.com/cgi-bin/message/template/send', [
                    'query' => ['access_token' => $accessToken],
                    'json' => $data,
                ]);
                $contents = $response->getBody()->getContents();
                logger('QueueLog')->info('微信消息response:' . $contents);
                return $contents;
            });
        }
        // 等待协程
        try {
            $parallel->wait();
        } catch (ParallelExecutionException $e) {
            foreach ($e->getThrowables() as $result) {
                if (method_exists($result, 'getMessage')) {
                    logger('QueueLog')->error($result->getMessage());
                }
            }
        }
        return true;
    }

    protected function handleData(array &$data): void
    {
        // 默认模版 ID
        if (empty($data['tmp_id'])) {
            $data['tmp_id'] = 'kkK3xAv-Zk3PhcRa6JwlDsITGOF0zLmHs80mM6awdc0';
        }
        if (! empty($data['keyword2'])) {
            $data['keyword2'] = Carbon::parse($data['keyword2'])->format('Y-m-d H:i');
        }
        if (! empty($data['send_time'])) {
            $data['send_time'] = Carbon::parse($data['send_time'])->format('Y-m-d H:i');
        }
    }
}
