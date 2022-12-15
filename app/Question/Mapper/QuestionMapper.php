<?php

declare(strict_types=1);

namespace App\Question\Mapper;

use App\Question\Model\Question;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 题库管理Mapper类.
 */
class QuestionMapper extends AbstractMapper
{
    /**
     * @var Question
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = Question::class;
    }

    /**
     * 单个或批量软删除数据.
     */
    public function delete(array $ids): bool
    {
        Question::query()->whereIn('id', $ids)->update(['deleted_at' => time(), 'states' => 1]);
        return true;
    }

    /**
     * 新增数据.
     */
    #[Transaction]
    public function save(array $data): int
    {
        $model = $this->model::create($data);
        if (isset($data['tag'])) {
            $model->tags()->sync($data['tag']);
        }
        return $model->{$model->getKeyName()};
    }

    /**
     * 更新一条数据.
     */
    #[Transaction]
    public function update(int $id, array $data): bool
    {
        $model = $this->model::find($id);
        if (isset($data['tag'])) {
            $model->tags()->sync($data['tag']);
        }
        $this->filterExecuteAttributes($data, true);
        return $model->update($data) > 0;
    }

    /**
     * 获取课程对应的题目.
     */
    public function getCourseQuestion(array $params): array
    {
        $courseBasisId = $params['course_basis_id'];
        $channel = $params['channel'] ?? null;
        $perPage = $params['pageSize'] ?? $this->model::PAGE_SIZE;
        $page = $params['page'] ?? 1;
        $model = $this->listQuerySetting($params, false);
        $paginate = $model->whereHas('knows', function (Builder $query) use ($courseBasisId, $channel) {
            $query->whereRaw('FIND_IN_SET(?,shop_id)', [$courseBasisId])
                ->when(! empty($channel), function ($query) use ($channel) {
                    $query->where('channel', $channel);
                });
        })->paginate((int) $perPage, ['*'], 'page', (int) $page);
        return $this->setPaginate($paginate);
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['id']) && ! is_array($params['id'])) {
            $query->where('id', $params['id']);
        }

        if (isset($params['id']) && is_array($params['id'])) {
            $query->whereIn('id', $params['id']);
        }
        // 知识点ID
        if (isset($params['knows_id']) && $params['knows_id'] !== '') {
            $query->where('knows_id', '=', $params['knows_id']);
        }

        // 分类ID
        if (isset($params['classify_id']) && $params['classify_id'] !== '') {
            $query->where('classify_id', '=', $params['classify_id']);
        }

        // 试题来源 默认1：每日一题 ，2：入学测试，3：视频解析
        if (isset($params['channel']) && $params['channel'] !== '') {
            $query->where('channel', '=', $params['channel']);
        }

        // 学期 1 春季 2秋季
        if (isset($params['semester']) && $params['semester'] !== '') {
            $query->where('semester', '=', $params['semester']);
        }

        // 试题类型:1:单选题 2:多选题 3:不定项选择 4:判断题 5:问答题 6:填空题 7:组合题
        if (isset($params['ques_type']) && $params['ques_type'] !== '') {
            $query->where('ques_type', '=', $params['ques_type']);
        }

        if (isset($params['ques_title']) && $params['ques_title'] !== '') {
            $query->where('ques_title', '=', $params['ques_title']);
        }

        // 试题题干
        if (isset($params['ques_stem']) && $params['ques_stem'] !== '') {
            $query->where('ques_stem', '=', $params['ques_stem']);
        }

        // 文本题干
        if (isset($params['ques_stem_text']) && $params['ques_stem_text'] !== '') {
            $query->where('ques_stem_text', '=', $params['ques_stem_text']);
        }

        // 选项/问题参考答案/填空题：参考答案
        if (isset($params['ques_option']) && $params['ques_option'] !== '') {
            $query->where('ques_option', '=', $params['ques_option']);
        }

        // 正确答案/填空题：答案验证规则1完全一致2仅顺序一致3仅供参考4未设置
        if (isset($params['right_answer']) && $params['right_answer'] !== '') {
            $query->where('right_answer', '=', $params['right_answer']);
        }

        // 试题解析
        if (isset($params['ques_analysis']) && $params['ques_analysis'] !== '') {
            $query->where('ques_analysis', '=', $params['ques_analysis']);
        }

        // 试题难度:1:易 2:中 3:难
        if (isset($params['ques_difficulty']) && $params['ques_difficulty'] !== '') {
            $query->where('ques_difficulty', '=', $params['ques_difficulty']);
        }

        // 排序
        if (isset($params['sort']) && $params['sort'] !== '') {
            $query->where('sort', '=', $params['sort']);
        }

        // 状态:0:显示 1:隐藏
        if (isset($params['states']) && $params['states'] !== '') {
            $query->where('states', '=', $params['states']);
        }

        // 创建人
        if (isset($params['created_id']) && $params['created_id'] !== '') {
            $query->where('created_id', '=', $params['created_id']);
        }

        // 修改人
        if (isset($params['updated_id']) && $params['updated_id'] !== '') {
            $query->where('updated_id', '=', $params['updated_id']);
        }

        // 日期
        if (isset($params['form_at']) && $params['form_at'] !== '') {
            $query->where('form_at', '=', $params['form_at']);
        }

        if (! empty($params['withTags'])) {
            $query->with('tags');
        }

        $query->where('deleted_at', 0);
        return $query;
    }
}
