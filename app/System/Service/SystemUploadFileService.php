<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\SystemUploadFileMapper;
use Exception;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Utils\Collection;
use League\Flysystem\FileExistsException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\Helper\Str;
use Mine\MineUpload;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 文件上传业务
 * Class SystemLoginLogService.
 */
class SystemUploadFileService extends AbstractService
{
    /**
     * @var SystemUploadFileMapper
     */
    public $mapper;

    /**
     * @var ConfigInterface
     */
    #[Inject]
    protected $config;

    protected MineUpload $mineUpload;

    public function __construct(SystemUploadFileMapper $mapper, MineUpload $mineUpload)
    {
        $this->mapper = $mapper;
        $this->mineUpload = $mineUpload;
    }

    /**
     * 上传文件.
     * @throws FileExistsException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
        }

        return [];
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
     * 获取当前目录下所有文件（包含目录）.
     */
    public function getAllFile(array $params = []): array
    {
        return $this->getArrayToPageList($params);
    }

    /**
     * 保存网络图片.
     * @param array $data ['url', 'path']
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function saveNetworkImage(array $data): array
    {
        $data = $this->mineUpload->handleSaveNetworkImage($data);
        if (!isset($data['id']) && $this->save($data)) {
            return $data;
        }
        return $data;
    }

    /**
     * 通过hash获取文件信息.
     * @return null|Builder|Model|object
     */
    public function readByHash(string $hash)
    {
        return $this->mapper->getFileInfoByHash($hash);
    }

    /**
     * 数组数据搜索器.
     */
    protected function handleArraySearch(Collection $collect, array $params): Collection
    {
        if ($params['name'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return Str::contains($row['name'], $params['name']);
            });
        }

        if ($params['label'] ?? false) {
            $collect = $collect->filter(function ($row) use ($params) {
                return Str::contains($row['label'], $params['label']);
            });
        }
        return $collect;
    }
}
