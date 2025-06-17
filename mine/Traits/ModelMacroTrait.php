<?php

declare(strict_types=1);

namespace Mine\Traits;

use App\System\Model\SystemDept;
use App\System\Model\SystemRole;
use App\System\Model\SystemUser;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Exception\MineException;
use function Hyperf\Collection\collect;
use function Hyperf\Config\config;
use function Hyperf\Support\env;

trait ModelMacroTrait
{
    /**
     * 注册自定义方法.
     */
    private function registerUserDataScope(): void
    {
        // 数据权限方法
        $model = $this;
        Builder::macro('userDataScope', function (?int $userid = null, string $field = 'created_by') use ($model) {
            if (!config('mineadmin.data_scope_enabled')) {
                return $this;
            }

            $userid = is_null($userid) ? (int)user()->getId() : $userid;

            if (empty($userid)) {
                throw new MineException('Data Scope missing user_id');
            }

            /* @var Builder $this */
            if ($userid == env('SUPER_ADMIN')) {
                return $this;
            }

            if (!in_array('created_by', $model->getFillable())) {
                return $this;
            }

            $dataScope = new class($userid, $field, $this) {
                // 用户ID
                protected int $userid;

                protected string $field;

                // 查询构造器
                protected Builder $builder;

                // 数据范围用户ID列表
                protected array $userIds = [];

                public function __construct(int $userid, string $field, Builder $builder)
                {
                    $this->field = $field;
                    $this->userid = $userid;
                    $this->builder = $builder;
                }

                public function execute(): Builder
                {
                    $this->getUserDataScope();
                    console()->info('Data Scope userIds: ' . json_encode(array_unique($this->userIds)));
                    return empty($this->userIds)
                        ? $this->builder
                        : $this->builder->whereIn($this->field, array_unique($this->userIds));
                }

                protected function getUserDataScope(): void
                {
                    $userModel = SystemUser::find($this->userid, ['id']);
                    $roles = $userModel->roles()->get(['id', 'data_scope']);

                    foreach ($roles as $role) {
                        switch ($role->data_scope) {
                            case SystemRole::ALL_SCOPE:
                                // 如果是所有权限，跳出所有循环
                                break 2;
                            case SystemRole::CUSTOM_SCOPE:
                                // 自定义数据权限
                                $deptIds = $role->depts()->pluck('id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('system_user_dept')->whereIn('dept_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case SystemRole::SELF_DEPT_SCOPE:
                                // 本部门数据权限
                                $deptIds = Db::table('system_user_dept')->where('user_id', $userModel->id)->pluck('dept_id')->toArray();
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('system_user_dept')->whereIn('dept_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case SystemRole::DEPT_BELOW_SCOPE:
                                // 本部门及子部门数据权限
                                $parentDepts = Db::table('system_user_dept')->where('user_id', $userModel->id)->pluck('dept_id')->toArray();
                                $ids = [];
                                foreach ($parentDepts as $deptId) {
                                    $ids[] = SystemDept::query()
                                        ->where(function ($query) use ($deptId) {
                                            $query->where('id', '=', $deptId)
                                                ->orWhere('level', 'like', $deptId . ',%')
                                                ->orWhere('level', 'like', '%,' . $deptId);
                                        })
                                        ->pluck('id')
                                        ->toArray();
                                }
                                $deptIds = array_merge($parentDepts, ...$ids);
                                $this->userIds = array_merge(
                                    $this->userIds,
                                    Db::table('system_user_dept')->whereIn('dept_id', $deptIds)->pluck('user_id')->toArray()
                                );
                                $this->userIds[] = $this->userid;
                                break;
                            case SystemRole::SELF_SCOPE:
                                $this->userIds[] = $this->userid;
                                break;
                            default:
                                break;
                        }
                    }
                }
            };

            return $dataScope->execute();
        });
    }

    private function registerPlatformDataScope(): void
    {
        Builder::macro('platformDataScope', function ($platformField = 'platform') {
            $userid = user()->getId();
            /* @var Builder $this */
            if ($userid === (int)env('SUPER_ADMIN')) {
                return $this;
            }
            $platformCodes = [];

            /* @var SystemUser $userModel */
            $userModel = SystemUser::find($userid, ['id']);
            $roles = $userModel->roles()->get(['id', 'data_scope']);
            $deptModel = $userModel->depts;
            $curPlatform = $deptModel->pluck('platform')->toArray();
            foreach ($roles as $role) {
                switch ($role->data_scope) {
                    case SystemRole::ALL_SCOPE:
                        // 如果是所有权限，跳出所有循环
                        break 2;
                    case SystemRole::CUSTOM_SCOPE:
                        // 自定义数据权限
                        $platformCodes = array_merge(
                            $platformCodes,
                            $role->depts()->pluck('platform')->toArray(),
                            $curPlatform
                        );
                        break;
                    case SystemRole::SELF_DEPT_SCOPE:
                        // 本部门数据权限
                        $platformCodes = array_merge($platformCodes, $curPlatform);
                        break;
                    case SystemRole::DEPT_BELOW_SCOPE:
                        // 本部门及子部门数据权限
                        $parentDepts = Db::table('system_user_dept')->where('user_id', $userModel->id)->pluck('dept_id')->toArray();
                        $ids = collect();
                        foreach ($parentDepts as $deptId) {
                            $ids->push(SystemDept::query()
                                ->where(function ($query) use ($deptId) {
                                    $query->where('id', '=', $deptId)
                                        ->orWhere('level', 'like', $deptId . ',%')
                                        ->orWhere('level', 'like', '%,' . $deptId);
                                })
                                ->pluck('platform'));
                        }
                        $platformCodes = array_merge($platformCodes, $ids->flatten()->toArray(), $curPlatform);
                        break;
                    case SystemRole::SELF_SCOPE:
                        $platformCodes = array_merge($platformCodes, $curPlatform);
                        break;
                    default:
                        break;
                }
            }

            return empty($platformCodes)
                ? $this
                : $this->whereIn($platformField, array_unique($platformCodes));
        });
    }

    /**
     * Description:注册常用自定义方法
     * User:mike.
     */
    private function registerBase(): void
    {
        // 添加andFilterWhere()方法
        Builder::macro('andFilterWhere', function ($key, $operator, $value = null) {
            if ($value === '' || $value === '%%' || $value === '%') {
                return $this;
            }
            if ($operator === '' || $operator === '%%' || $operator === '%') {
                return $this;
            }
            if ($value === null) {
                return $this->where($key, $operator);
            }
            return $this->where($key, $operator, $value);
        });

        // 添加orFilterWhere()方法
        Builder::macro('orFilterWhere', function ($key, $operator, $value = null) {
            if ($value === '' || $value === '%%' || $value === '%') {
                return $this;
            }
            if ($operator === '' || $operator === '%%' || $operator === '%') {
                return $this;
            }
            if ($value === null) {
                return $this->orWhere($key, $operator);
            }
            return $this->orWhere($key, $operator, $value);
        });
    }
}
