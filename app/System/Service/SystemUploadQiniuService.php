<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SystemUploadFileMapper;
use App\System\Model\SystemUploadfile;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\BasisUpload;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use function Hyperf\Config\config;

class SystemUploadQiniuService extends AbstractService
{
    /**
     * @var SystemUploadFileMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected BasisUpload $basisUpload;

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException|NotFoundExceptionInterface|RedisException
     */
    public function getUploadToken(array $params): array
    {
        if (!isset($params['fileExt'])) {
            throw new NormalStatusException('fileExt is null');
        }

        $fileExt = Str::lower($params['fileExt']);
        $filename = $this->basisUpload->getNewName() . '.' . $fileExt;
        $storagePath = 'uploadfile/' . date('Ymd');
        // svg图标放入专用文件夹
        if ($fileExt === 'svg') {
            $storagePath = 'icons/svg';
        }
        if ($this->basisUpload->getStorageMode() !== '3') {
            throw new NormalStatusException('请更改上传模式为七牛云');
        }
        $key = $storagePath . '/' . $filename;
        $config = [
            'returnBody' => json_encode([
                'key' => '$(key)',
                'hash' => '$(etag)',
                'storage_path' => '/' . $storagePath,
                'suffix' => $fileExt,
                'object_name' => $filename,
                'url' => config('file.storage.qiniu.domain') . '/' . $key,
            ], JSON_THROW_ON_ERROR),
        ];
        /* @var QiniuAdapter $adapter */
        $adapter = $this->basisUpload->getAdapter();
        $token = $adapter->getUploadToken($key, 3600, $config);
        return ['token' => $token, 'key' => $key];
    }

    /**
     * 存入七牛云前端上传文件.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    public function saveUploadInfo(array $params): array
    {
        if ($this->basisUpload->getStorageMode() !== '3') {
            throw new NormalStatusException('请更改上传模式为七牛云');
        }

        if ($model = $this->mapper->getFileInfoByHash($params['hash'])) {
            return $model->toArray();
        }

        $params['storage_mode'] = 3;
        $data = $this->getUploadFileInfo($params);
        $model = SystemUploadfile::query()->create($data);
        if ($model) {
            return $model->toArray();
        }
        return [];
    }

    /**
     * 组装保存数据.
     */
    public function getUploadFileInfo(array $params): array
    {
        return [
            'storage_mode' => $params['storage_mode'],
            'origin_name' => $params['origin_name'],
            'object_name' => $params['object_name'],
            'mime_type' => $params['fileType'],
            'storage_path' => $params['storage_path'],
            'hash' => $params['hash'],
            'suffix' => Str::lower($params['suffix']),
            'size_byte' => $params['fileSize'],
            'size_info' => format_size($params['fileSize'] * 1024),
            'url' => $this->basisUpload->assembleUrl($params['storage_path'], $params['object_name'], false),
        ];
    }
}
