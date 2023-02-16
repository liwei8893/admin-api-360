<?php

declare(strict_types=1);

namespace Mine;

use App\Setting\Service\SettingConfigService;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\Snowflake\IdGeneratorInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

class BasisUpload
{
    protected FilesystemFactory $factory;

    protected Filesystem $filesystem;

    protected ContainerInterface $container;

    #[Inject]
    protected ConfigInterface $config;

    /**
     * MineUpload constructor.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    public function __construct(ContainerInterface $container, FilesystemFactory $factory)
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->filesystem = $this->factory->get($this->getMappingMode());
    }

    /**
     * 获取文件操作处理系统
     */
    public function getFileSystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 获取适配器实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function getAdapter(): FilesystemAdapter
    {
        $option = $this->config->get('file');
        return $this->factory->getAdapter($option, $this->getMappingMode());
    }

    /**
     * 组装url.
     */
    public function assembleUrl(?string $path, string $filename, bool $isContainRoot = true): string
    {
        return $this->getPath($path, $isContainRoot) . '/' . $filename;
    }

    /**
     * 获取存储方式,默认七牛云.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    public function getStorageMode(): string
    {
        return $this->container->get(SettingConfigService::class)->getConfigByKey('upload_mode')['value'] ?? '3';
    }

    /**
     * 获取编码后的文件名.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getNewName(): string
    {
        return (string) container()->get(IdGeneratorInterface::class)->generate();
    }

    /**
     * @param false $isContainRoot
     */
    protected function getPath(?string $path = null, bool $isContainRoot = false): string
    {
        $uploadfile = $isContainRoot ? '/' . env('UPLOAD_PATH', 'uploadfile') . '/' : '';
        return empty($path) ? $uploadfile . date('Ymd') : $uploadfile . $path;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|RedisException
     */
    protected function getMappingMode(): string
    {
        return match ($this->getStorageMode()) {
            '2' => 'oss',
            '3' => 'qiniu',
            '4' => 'cos',
            default => 'local',
        };
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getProtocol(): string
    {
        return $this->container->get(MineRequest::class)->getScheme();
    }
}
