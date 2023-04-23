<?php

declare(strict_types=1);

namespace App\Course\Mapper;

use App\Course\Model\CourseShop;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * shopMapper类.
 */
class CourseShopMapper extends AbstractMapper
{
    /**
     * @var CourseShop
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CourseShop::class;
    }

    #[Transaction]
    public function save(array $data): int
    {
        $courseId = $data['course_id'] ?? [];
        $this->filterExecuteAttributes($data, $this->getModel()->incrementing);
        $model = $this->model::create($data);
        $model->courseBasis()->sync($courseId);
        return $model->id;
    }

    #[Transaction]
    public function update(int $id, array $data): bool
    {
        $courseId = $data['course_id'] ?? [];
        $this->filterExecuteAttributes($data, true);
        $model = $this->model::find($id);
        if (! $model) {
            return false;
        }
        $state = $model->update($data);
        $model->courseBasis()->sync($courseId);
        return $state;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 标题
        if (isset($params['title']) && $params['title'] !== '') {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }

        // 副标题
        if (isset($params['title_desc']) && $params['title_desc'] !== '') {
            $query->where('title_desc', 'like', '%' . $params['title_desc'] . '%');
        }

        // 0:标题,副标题全部显示,1:只显示标题,2:只显示副标题
        if (isset($params['title_rule']) && $params['title_rule'] !== '') {
            $query->where('title_rule', '=', $params['title_rule']);
        }

        // 0:不显示,购买之后才显示,1:总是显示,2:会员认证显示
        if (isset($params['show_rule']) && $params['show_rule'] !== '') {
            $query->where('show_rule', '=', $params['show_rule']);
        }

        // show_rule=2时进行会员认证,2超级会员,3至尊会员
        if (isset($params['vip_auth']) && $params['vip_auth'] !== '') {
            $query->where('vip_auth', '=', $params['vip_auth']);
        }

        if (! empty($params['withCourse'])) {
            $query->with(['courseBasis']);
        }

        return $query;
    }
}
