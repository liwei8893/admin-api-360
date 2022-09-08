<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SystemUploadFileMapper;
use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Utils\Collection;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Mine\MineUpload;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * 文件上传业务
 * Class SystemLoginLogService
 * @package App\System\Service
 */
class SystemUploadFileService extends AbstractService
{
    /**
     * @var ConfigInterface
     */
    #[Inject]
    protected $config;

    /**
     * @var SystemUploadFileMapper
     */
    public $mapper;

    /**
     * @var MineUpload
     */
    protected MineUpload $mineUpload;


    public function __construct(SystemUploadFileMapper $mapper, MineUpload $mineUpload)
    {
        $this->mapper = $mapper;
        $this->mineUpload = $mineUpload;
    }

    /**
     * @param $params
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface|\JsonException
     * author:ZQ
     * time:2022-09-07 18:00
     */
    public function getUploadToken($params): array
    {
        if (!isset($params['fileExt'])) {
            throw new NormalStatusException('fileExt is null');
        }

        $fileExt = Str::lower($params['fileExt']);
        $filename = $this->mineUpload->getNewName() . '.' . $fileExt;
        $storagePath = 'uploadfile/' . date('Ymd');
        if ($this->mineUpload->getStorageMode() !== 'qiniu') {
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
        $token = $this->mineUpload->getFileSystem()->getAdapter()->getUploadToken($key, 3600, $config);
        return ['token' => $token, 'key' => $key];
    }

    /**
     * 存入七牛云前端上传文件
     * @param $params
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * author:ZQ
     * time:2022-09-08 10:01
     */
    public function saveUploadInfo($params): array
    {
        if ($this->mineUpload->getStorageMode() !== 'qiniu') {
            throw new NormalStatusException('请更改上传模式为七牛云');
        }

        if ($model = $this->mapper->getFileInfoByHash($params['hash'])) {
            return $model->toArray();
        }

        $params['storage_mode'] = 3;
        $data = $this->getUploadFileInfo($params);
        if ($this->save($data)) {
            return $data;
        }
        return [];
    }

    /**
     * 组装保存数据
     * @param $params
     * @return array
     * author:ZQ
     * time:2022-09-08 10:01
     */
    public function getUploadFileInfo($params): array
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
            'url' => $this->mineUpload->assembleUrl($params['storage_path'], $params['object_name']),
        ];
    }

    /**
     * 上传文件
     * @param UploadedFile $uploadedFile
     * @param array $config
     * @return array
     * @throws \League\Flysystem\FileExistsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function upload(UploadedFile $uploadedFile, array $config = []): array
    {
        try {
            $hash = md5_file($uploadedFile->getPath() . '/' . $uploadedFile->getFilename());
            if ($model = $this->mapper->getFileInfoByHash($hash)) {
                return $model->toArray();
            }
        } catch (Exception $e) {
            throw new NormalStatusException('获取文件Hash失败', 500);
        }
        $data = $this->mineUpload->upload($uploadedFile, $config);
        if ($this->save($data)) {
            return $data;
        } else {
            return [];
        }
    }

    public function chunkUpload(array $data): array
    {
        if ($model = $this->mapper->getFileInfoByHash($data['hash'])) {
            return $model->toArray();
        }
        $result = $this->mineUpload->handleChunkUpload($data);
        if (isset($result['hash'])) {
            $this->save($result);
        }
        return $result;
    }

    /**
     * 获取当前目录下所有文件（包含目录）
     * @param array $params
     * @return array
     */
    public function getAllFile(array $params = []): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 数组数据搜索器
     * @param Collection $collect
     * @param array $params
     * @return Collection
     */
    protected function handleArraySearch(Collection $collect, array $params): Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row['name'], $params['name']);
            });
        }

        if ($params['label'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return \Mine\Helper\Str::contains($row['label'], $params['label']);
            });
        }
        return $collect;
    }

    /**
     * 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        $directory = $this->getDirectory($params['storage_path'] ?? '');

        $params['select'] = [
            'id',
            'origin_name',
            'object_name',
            'mime_type',
            'url',
            'size_info',
            'storage_path',
            'created_at'
        ];

        $params['select'] = implode(',', $params['select']);

        return array_merge($directory, $this->getList($params));
    }

    /**
     * 保存网络图片
     * @param array $data ['url', 'path']
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function saveNetworkImage(array $data): array
    {
        $data = $this->mineUpload->handleSaveNetworkImage($data);
        if (!isset($data['id']) && $this->save($data)) {
            return $data;
        } else {
            return $data;
        }
    }

    /**
     * 通过hash获取文件信息
     * @param string $hash
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function readByHash(string $hash)
    {
        return $this->mapper->getFileInfoByHash($hash);
    }
}
