<?php

declare(strict_types=1);

namespace App\System\Service;

use App\System\Mapper\AreaMapper;
use App\System\Queue\Producer\SetUserAreaProducer;
use App\Users\Model\User;
use App\Users\Service\UsersService;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Amqp\Producer;
use Hyperf\Database\Model\Builder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use function Hyperf\Config\config;

/**
 * 区域字典服务类.
 */
class AreaService extends AbstractService
{
    /**
     * @var AreaMapper
     */
    #[Inject]
    public $mapper;

    #[Inject]
    protected UsersService $usersService;

    #[Inject]
    protected Producer $producer;

    public function setUserAreaByMobile(string $mobile): bool
    {
        try {
            /** @var User $userModel */
            $userModel = $this->usersService->readByMobile($mobile);
            // 获取api数据
            $clientFactory = container()->get(ClientFactory::class);
            $client = $clientFactory->create();
            $appCode = config('hxt-app.mobileArea.AppCode');
            $response = $client->get('http://plocn.market.alicloudapi.com/plocn', [
                'headers' => [
                    'Authorization' => 'APPCODE ' . $appCode,
                ],
                'query' => [
                    'n' => $mobile,
                ],
            ]);
            $apiData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $provinceModel = $this->mapper->getAreaByAreaName($apiData['province']);
            $cityModel = $this->mapper->getAreaByAreaName($apiData['city']);
            $province = $provinceModel ? $provinceModel['id'] : 0;
            $city = $cityModel ? $cityModel['id'] : 0;
            // 保存数据
            $userModel->province_id = $province;
            $userModel->city_id = $city;
            $userModel->area_id = 0;
            return $userModel->save();
        } catch (NormalStatusException|GuzzleException|JsonException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return false;
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function setAllAreaNullUser(): void
    {
        $mobiles = User::query()->whereHas('orders', function (Builder $query) {
            $query->where('status', '!=', 2)->where('pay_states', 7)
                ->vipOrder()
                ->where('deleted_at', 0);
        })->where('user_type', 1)
            ->where(static function ($query) {
                $query->orWhere('province_id', 0)
                    ->orWhereNull('province_id');
            })
            ->pluck('mobile');
        foreach ($mobiles as $mobile) {
            $message = new SetUserAreaProducer($mobile);
            $this->producer->produce($message);
        }
    }
}
