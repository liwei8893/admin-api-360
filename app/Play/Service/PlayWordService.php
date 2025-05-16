<?php
declare(strict_types=1);


namespace App\Play\Service;

use App\Play\Mapper\PlayWordMapper;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\File\File;
use JsonException;
use Mine\Abstracts\AbstractService;
use Mine\Exception\NormalStatusException;
use Mine\MineUpload;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 单词游戏服务类
 */
class PlayWordService extends AbstractService
{
    /**
     * @var PlayWordMapper
     */
    public $mapper;

    #[Inject]
    protected MineUpload $upload;

    public function __construct(PlayWordMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMaxId(): int
    {
        $level = $this->mapper->getMaxId();
        return $level ?? 0;
    }

    /**
     * 获取词典内容
     * @param array $params
     * @return array
     */
    public function getWordDict(array $params): array
    {
        if (empty($params['word'])) {
            throw new NormalStatusException("请输入单词！");
        }
        try {
            $word = $params['word'];
            // 有道词典api
            $url = "https://dict.youdao.com/jsonapi_s?doctype=json&jsonversion=4&le=en&q={$word}";
            $clientFactory = container()->get(ClientFactory::class);
            $client = $clientFactory->create();
            $response = $client->get($url);
            $apiData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            if (empty($apiData['ec']['word'])) {
                throw new NormalStatusException("未找到该单词！");
            }
        } catch (GuzzleException|JsonException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new NormalStatusException($e->getMessage());
        }
        $res = $apiData['ec']['word'];
        $word = $res['return-phrase'];
        // 有道发音api
        $speechUrl = 'https://dict.youdao.com/dictvoice?audio=';
        $ukSpeechUrl = $speechUrl . $res['ukspeech'];
        $usSpeechUrl = $speechUrl . $res['usspeech'];
        $ukFileInfo = $this->upload->handleSaveNetworkFile($ukSpeechUrl, "{$word}_uk.mp3");
        $usFileInfo = $this->upload->handleSaveNetworkFile($usSpeechUrl, "{$word}_us.mp3");
        return [
            'word' => $word,
            'uk' => $res['ukphone'],
            'uk_speech' => $ukFileInfo['url'],
            'us' => $res['usphone'],
            'us_speech' => $usFileInfo['url'],
            'trs' => $res['trs'],
        ];
    }
}

