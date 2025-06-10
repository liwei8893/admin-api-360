<?php

declare(strict_types=1);

namespace App\Course\Service;

use App\Course\Mapper\CourseChapterMapper;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Guzzle\ClientFactory;
use JsonException;
use Mine\Abstracts\AbstractService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 课程大纲服务类.
 */
class CourseChapterService extends AbstractService
{
    /**
     * @var CourseChapterMapper
     */
    public $mapper;

    public function __construct(CourseChapterMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 从回收站获取树列表.
     */
    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getTreeList($params, true, 'id', 'parent_id');
    }

    /**
     * 获取树列表.
     */
    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getTreeList($params, true, 'id', 'parent_id');
    }

    /**
     * 获取前端选择树.
     */
    public function getSelectTree(): array
    {
        return $this->mapper->getSelectTree();
    }

    /**
     * 新增数据.
     */
    public function save(array $data): int
    {
        // 新建章
        if ($data['parent_id'] === 0) {
            return $this->mapper->save($data);
        }
        // 新建节
        return $this->mapper->saveChapter($this->handlePeriodData($data));
    }

    /**
     * 测一测数据加上type1,练一练加上type2.
     */
    public function handleQuestionPeriodData(array $data): array
    {
        $questionPeriodData = [];
        foreach ($data as $item) {
            $questionPeriodData[$item] = ['type' => 1];
        }
        return $questionPeriodData;
    }

    /**
     * 更新.
     */
    public function update(int $id, array $data): bool
    {
        // 更新章
        if ($data['parent_id'] === 0) {
            return $this->mapper->update($id, $data);
        }
        // 更新节
        return $this->mapper->updateChapter($id, $this->handlePeriodData($data));
    }

    public function getChapter(int $id): array
    {
        $params['course_basis_id'] = $id;
        $params['withAppCoursePeriod'] = true;
        $params['orderBy'] = ['serial_num', 'id'];
        $params['orderType'] = ['asc', 'asc'];
        $data = $this->getTreeList($params);
        // 测一测题目总和
        foreach ($data as &$subject) {
            foreach ($subject['children'] as &$chapter) {
                // 如果测一测是空的,跳出不用相加
                $chapter['course_period']['question_practice_count'] = 0;
                if (empty($chapter['course_period']['qurstion_str'])) {
                    continue;
                }
                $testQuesCount = explode(',', $chapter['course_period']['qurstion_str']);
                $chapter['course_period']['question_practice_count'] = count($testQuesCount);
            }
        }
        return $data;
    }

    /**
     * 处理节数据.
     */
    protected function handlePeriodData(array $data): array
    {
        if (isset($data['qurstion_str']) && is_array($data['qurstion_str'])) {
            $data['qurstion_str'] = implode(',', $data['qurstion_str']);
        }
        $data['course_period'] = $data['course_period'] ?? [];
        $initPeriodData = [
            'title' => $data['title'],
            'course_basis_id' => $data['course_basis_id'],
            'start_play' => $data['course_period']['start_play'] ?? 0,
            'end_play' => $data['course_period']['end_play'] ?? 0,
            'qiniu_url' => $data['course_period']['qiniu_url'] ?? '',
            'qurstion_str' => $data['qurstion_str'] ?? '',
        ];
        // 处理视频时长
        if (!empty($data['course_period']['qiniu_url'])) {
            $initPeriodData['duration'] = $this->getVideoDuration($data['course_period']['qiniu_url']);
        }
        $data['course_period'] = array_merge($data['course_period'], $initPeriodData);
        // 处理测一测数据
        $data['question_period'] = $this->handleQuestionPeriodData($data['question_period'] ?? []);
        // 处理标签
        $data['tag'] = $data['tag'] ?? [];
        return $data;
    }

    /**
     * 获取七牛云视频时间.
     */
    protected function getVideoDuration(string $url): int
    {
        if (!str_contains($url, 'http')) {
            return 0;
        }
        try {
            // 去除 url 两端空格
            $url = trim($url);
            if (str_contains($url, '?')) {
                $url .= '&avinfo';
            } else {
                $url .= '?avinfo';
            }
            $clientFactory = container()->get(ClientFactory::class);
            $client = $clientFactory->create();
            $response = $client->get($url);
            $apiData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $duration = explode('.', $apiData['streams'][0]['duration'])[0];
            return (int)$duration;
        } catch (GuzzleException|JsonException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return 0;
        }
    }
}
