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

namespace App\Crm\Controller;

use App\Crm\Request\CrmCallRecordRequest;
use App\Crm\Service\CrmCallRecordService;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use JsonException;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 话单记录控制器
 * Class CrmCallRecordController
 */
#[Controller(prefix: "crm/callRecord")]
class CrmCallRecordController extends MineController
{
    /**
     * 业务处理服务
     * CrmCallRecordService
     */
    #[Inject]
    protected CrmCallRecordService $service;


    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index"), Auth, Permission("crm:callRecord, crm:callRecord:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 新增
     * @param CrmCallRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Auth, Permission("crm:callRecord:save"), OperationLog]
    public function save(CrmCallRecordRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新
     * @param int $id
     * @param CrmCallRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Auth, Permission("crm:callRecord:update"), OperationLog]
    public function update(int $id, CrmCallRecordRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Auth, Permission("crm:callRecord:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 呼叫中心点拨
     * @param CrmCallRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws GuzzleException
     * @throws JsonException
     */
    #[PostMapping("call"), Auth]
    public function call(CrmCallRecordRequest $request): ResponseInterface
    {
        return $this->success($this->service->call($request->all()));
    }

    /**
     * 呼叫中心话单记录回调
     * 文档：URL_ADDRESS     * 文档：http://doc.paiyuns.com:8183/docs/callcenter/callcenter-1fllghflsdo49
     * @param CrmCallRecordRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('notify')]
    public function notify(CrmCallRecordRequest $request): ResponseInterface
    {
        return $this->service->notify($request->all()) ? $this->success() : $this->error();
    }

}
