<?php

declare(strict_types=1);

namespace App\System\Controller;

use App\System\Request\UploadRequest;
use App\System\Service\SystemUploadFileService;
use App\System\Service\SystemUploadQiniuService;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use JsonException;
use Mine\Annotation\Auth;
use Mine\Exception\MineException;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RedisException;

/**
 * Class UploadController.
 */
#[Controller(prefix: 'system')]
class UploadController extends MineController
{
    #[Inject]
    protected SystemUploadFileService $service;

    #[Inject]
    protected SystemUploadQiniuService $qiniuService;

    /**
     * 获取七牛云认证
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    #[GetMapping('getUploadToken'), Auth]
    public function getUploadToken(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->qiniuService->getUploadToken($request->all()));
    }

    /**
     * 获取七牛云认证
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    #[GetMapping('app/getAppUploadToken'), Auth('app')]
    public function getAppUploadToken(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->qiniuService->getUploadToken($request->all()));
    }

    /**
     * 保存七牛上传文件信息到数据库.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    #[PostMapping('saveUploadInfo'), Auth]
    public function saveUploadInfo(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->qiniuService->saveUploadInfo($request->all()));
    }

    /**
     * 保存七牛上传文件信息到数据库.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    #[PostMapping('app/saveAppUploadInfo'), Auth('app')]
    public function saveAppUploadInfo(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->qiniuService->saveUploadInfo($request->all()));
    }

    /**
     * 上传文件.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('uploadFile'), Auth]
    public function uploadFile(UploadRequest $request): ResponseInterface
    {
        if ($request->validated() && $request->file('file')->isValid()) {
            $data = $this->service->upload(
                $request->file('file'),
                $request->all()
            );
            return empty($data) ? $this->error() : $this->success($data);
        }
        return $this->error(t('system.upload_file_verification_fail'));
    }

    /**
     * 上传图片.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('uploadImage'), Auth]
    public function uploadImage(UploadRequest $request): ResponseInterface
    {
        if ($request->validated() && $request->file('image')->isValid()) {
            $data = $this->service->upload(
                $request->file('image'),
                $request->all()
            );
            return empty($data) ? $this->error() : $this->success($data);
        }

        return $this->error(t('system.upload_image_verification_fail'));
    }

    /**
     * 分块上传.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('chunkUpload'), Auth]
    public function chunkUpload(UploadRequest $request): ResponseInterface
    {
        return ($data = $this->service->chunkUpload($request->validated())) ? $this->success($data) : $this->error();
    }

    /**
     * 保存网络图片.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[PostMapping('saveNetworkImage'), Auth]
    public function saveNetworkImage(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->service->saveNetworkImage($request->validated()));
    }

    /**
     * 获取当前目录所有文件和目录.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getAllFiles'), Auth]
    public function getAllFile(): ResponseInterface
    {
        return $this->success(
            $this->service->getAllFile($this->request->all())
        );
    }

    /**
     * 通过ID获取文件信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getFileInfoById')]
    public function getFileInfoByid(): ResponseInterface
    {
        return $this->success($this->service->read((int) $this->request->input('id', null)));
    }

    /**
     * 通过HASH获取文件信息.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getFileInfoByHash')]
    public function getFileInfoByHash(): ResponseInterface
    {
        return $this->success($this->service->readByHash($this->request->input('hash', null)));
    }

    /**
     * 根据id下载文件.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('downloadById')]
    public function downloadById(): ResponseInterface
    {
        $id = $this->request->input('id');
        if (empty($id)) {
            return $this->error('附件ID必填');
        }
        $model = $this->service->read((int) $id);
        if (! $model) {
            throw new MineException('附件不存在', 500);
        }
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }

    /**
     * 根据hash下载文件.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('downloadByHash')]
    public function downloadByHash(): ResponseInterface
    {
        $hash = $this->request->input('hash');
        if (empty($hash)) {
            return $this->error('附件hash必填');
        }
        $model = $this->service->readByHash($hash);
        if (! $model) {
            throw new MineException('附件不存在', 500);
        }
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }
}
