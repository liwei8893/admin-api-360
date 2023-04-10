<?php

declare(strict_types=1);

namespace App\Users\Service;

use App\Users\Mapper\UserScoreMapper;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;

class UserScoreService extends AbstractService
{
    /**
     * @var UserScoreMapper
     */
    #[Inject]
    public $mapper;

    /**
     * 变更积分.
     */
    #[Transaction]
    public function changeScore(array $data): bool
    {
        $userId = $data['user_id'];
        $originId = $data['origin_id'];
        $channel = $data['channel'];
        $channelType = $data['channel_type'];
        $score = $data['score'];
        $type = $data['type'];

        // 插入积分详情表
        $data = [
            'user_id' => $userId,
            'origin_id' => $originId,
            'channel' => $channel,
            'channel_type' => $channelType,
            'score' => $score,
        ];
        if ($type === 1) {
            // 保存增加积分详情
            $this->mapper->saveInUserScore($data);
            // 增加用户积分
            $states = $this->mapper->incrementUserScore((int) $userId, (int) $score);
        } else {
            // 保存减少积分详情
            $this->mapper->saveUnUserScore($data);
            // 减少用户积分
            $states = $this->mapper->decrementUserScore((int) $userId, (int) $score);
        }

        if (! $states) {
            throw new NormalStatusException('系统错误!');
        }
        return true;
    }

    /**
     * 获取用户积分详情分页.
     */
    public function getUserScorePage(array $params): array
    {
        $params['orderBy'] = ['id'];
        $params['orderType'] = ['desc'];
        $params['not_channel_type'] = 0;
        return $this->getPageList($params);
    }
}
