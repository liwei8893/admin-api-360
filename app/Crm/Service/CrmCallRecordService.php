<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Crm\Service;

use App\CRM\Mapper\CrmCallRecordMapper;
use App\Crm\Model\CrmCallRecord;
use App\Setting\Model\SettingConfig;
use App\System\Model\SystemUser;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Guzzle\ClientFactory;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 话单记录服务类
 */
class CrmCallRecordService extends AbstractService
{
    /**
     * @var CrmCallRecordMapper
     */
    public $mapper;

    public function __construct(CrmCallRecordMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 呼叫中心点拨
     * @param array $params
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws GuzzleException
     * @throws JsonException
     */
    #[Transaction]
    public function call(array $params): array
    {
        // 获取呼叫中心配置
        $config = SettingConfig::query()->where('group_id', 3)->get();
        $CID = $config->where('key', 'cid')->first()?->value;
        $APIKey = $config->where('key', 'APIKey')->first()?->value;
        $ReturnURL = $config->where('key', 'ReturnURL')->first()?->value;
        if (!$CID || !$APIKey || !$ReturnURL) {
            throw new NormalStatusException("呼叫中心配置未设置");
        }
        // 获取坐席号码
        $adminInfo = SystemUser::query()->where('id', user()->getId())->first();
        $caller = $adminInfo?->call_number;
        if (!$caller) {
            throw new NormalStatusException("坐席号码未设置");
        }
        // 调用api
        $clientFactory = container()->get(ClientFactory::class);
        $client = $clientFactory->create();
        $response = $client->post("http://ai.paiyuns.com:8787/api/callcenter", [
            'json' => [
                'Caller' => $caller,
                'Callee' => $params['callee'],
                'CID' => $CID,
                'ReturnURL' => $ReturnURL,
                'APIKey' => $APIKey
            ],
        ]);
        $apiData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        if ($apiData['Status'] !== 1) {
            throw new NormalStatusException("呼叫中心调用失败");
        }
        // 保存记录
        $mod = new CrmCallRecord();
        $mod->caller = $caller;
        $mod->callee = $params['callee'];
        $mod->return_uuid = $apiData['ReturnUUID'];
        $mod->api_date = $apiData['APIDate'];
        $mod->save();
        return $mod->refresh()->toArray();
    }

    /**
     * 呼叫中心话单记录回调
     * @param array $params
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function notify(array $params): bool
    {
        if (!$params['ReturnUUID']) {
            logger('call_center')->error('回调参数错误', $params);
            return false;
        }
        $mod = CrmCallRecord::query()->where('return_uuid', $params['ReturnUUID'])->first();
        if (!$mod) {
            logger('call_center')->error('回调UUID未查询到记录', $params['ReturnUUID']);
            return false;
        }
        $mod->task_id = $params['TaskID'];
        $mod->status = $params['Status'];
        $mod->status_info = $params['StatusInfo'];
        $mod->duration = $params['Duration'];
        $mod->record_url = $params['RecordURL'];
        $mod->create_time = $params['CreateTime'];
        $mod->api_date = $params['APIDate'];
        $mod->save();
        return true;
    }
}
