<?php


declare(strict_types=1);

namespace Api;

use App\System\Service\SystemApiService;
use App\System\Service\SystemAppService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Helper\MineCode;
use Mine\MineApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiDocController.
 */
#[Controller(prefix: 'apiDoc')]
class ApiDocController extends MineApi
{
    #[Inject]
    protected SystemAppService $systemAppService;

    #[Inject]
    protected SystemApiService $systemApiService;

    /**
     * 登录文档.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping('login')]
    public function login(): ResponseInterface
    {
        $app_id = $this->request->input('app_id', '');
        $app_secret = $this->request->input('app_secret', '');

        if (empty($app_id) && empty($app_secret)) {
            return $this->error(t('mineadmin.api_auth_fail'), MineCode::API_PARAMS_ERROR);
        }

        if (($code = $this->systemAppService->loginDoc($app_id, $app_secret)) !== MineCode::API_VERIFY_PASS) {
            return $this->error(t('mineadmin.api_auth_fail'), $code);
        }

        return $this->success();
    }

    /**
     * 通过app id获取接口数据.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('getAppAndInterfaceList/{id}')]
    public function getAppAndInterfaceList(string $id): ResponseInterface
    {
        return $this->success($this->systemAppService->getAppAndInterfaceList($id));
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping('getColumnList/{id}')]
    public function getColumnList(string $id): ResponseInterface
    {
        return $this->success($this->systemApiService->getColumnListByApiId($id));
    }
}
