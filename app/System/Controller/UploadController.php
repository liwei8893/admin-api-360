<?php

declare(strict_types=1);

namespace App\System\Controller;

use App\System\Request\UploadRequest;
use App\System\Service\SystemUploadFileService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UploadController
 * @package App\System\Controller
 */
#[Controller(prefix: "system")]
class UploadController extends MineController
{
    #[Inject]
    protected SystemUploadFileService $service;

    /**
     * 获取七牛云认证
     * @param UploadRequest $request
     * @return ResponseInterface
     * @throws \JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * author:ZQ
     * time:2022-09-08 10:52
     */
    #[GetMapping("getUploadToken"), Auth]
    public function getUploadToken(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->service->getUploadToken($request->all()));
    }

    #[PostMapping("saveUploadInfo"), Auth]
    public function saveUploadInfo(UploadRequest $request)
    {
        return $this->success($this->service->saveUploadInfo($request->all()));
    }

    /**
     * 上传文件
     * @param UploadRequest $request
     * @return ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("uploadFile"), Auth]
    public function uploadFile(UploadRequest $request): ResponseInterface
    {
        if ($request->validated() && $request->file('file')->isValid()) {
            $data = $this->service->upload(
                $request->file('file'), $request->all()
            );
            return empty($data) ? $this->error() : $this->success($data);
        } else {
            return $this->error(t('system.upload_file_verification_fail'));
        }
    }

    /**
     * 上传图片
     * @param UploadRequest $request
     * @return ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("uploadImage"), Auth]
    public function uploadImage(UploadRequest $request): ResponseInterface
    {
        if ($request->validated() && $request->file('image')->isValid()) {
            $data = $this->service->upload(
                $request->file('image'), $request->all()
            );
            return empty($data) ? $this->error() : $this->success($data);
        }

        return $this->error(t('system.upload_image_verification_fail'));
    }

    /**
     * 分块上传
     * @param UploadRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("chunkUpload"), Auth]
    public function chunkUpload(UploadRequest $request): ResponseInterface
    {
        return ($data = $this->service->chunkUpload($request->validated())) ? $this->success($data) : $this->error();
    }

    /**
     * 保存网络图片
     * @param UploadRequest $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    #[PostMapping("saveNetworkImage"), Auth]
    public function saveNetworkImage(UploadRequest $request): ResponseInterface
    {
        return $this->success($this->service->saveNetworkImage($request->validated()));
    }

    /**
     * 获取当前目录所有文件和目录
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getAllFiles"), Auth]
    public function getAllFile(): ResponseInterface
    {
        return $this->success(
            $this->service->getAllFile($this->request->all())
        );
    }

    /**
     * 获取文件信息
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getFileInfo")]
    public function getFileInfo(): ResponseInterface
    {
        return $this->success($this->service->read($this->request->input('id', null)));
    }

    /**
     * 根据id下载文件
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("downloadById")]
    public function downloadById(): ResponseInterface
    {
        $id = $this->request->input('id');
        if (empty($id)) {
            return $this->error("附件ID必填");
        }
        $model = $this->service->read((int)$id);
        if (!$model) {
            throw new \Mine\Exception\MineException('附件不存在', 500);
        }
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }

    /**
     * 根据hash下载文件
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("downloadByHash")]
    public function downloadByHash(): ResponseInterface
    {
        $hash = $this->request->input('hash');
        if (empty($hash)) {
            return $this->error("附件hash必填");
        }
        $model = $this->service->readByHash($hash);
        if (!$model) {
            throw new \Mine\Exception\MineException('附件不存在', 500);
        }
        return $this->_download(BASE_PATH . '/public' . $model->url, $model->origin_name);
    }
}