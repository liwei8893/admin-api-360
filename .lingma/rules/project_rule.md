# Hyperf框架PHP项目代码风格指南

## 一、文件结构规范

所有业务代码位于`app`目录下，根据业务领域划分为多个模块，每个模块包含以下子目录：

- Controller：控制器，处理HTTP请求
- Dto：数据传输对象，用于导入导出数据格式定义
- Listener：事件监听器
- Mapper：模型数据访问层
- Model：数据模型
- Request：请求验证类
- Service：业务逻辑服务

## 二、命名规范

1. 类名使用大驼峰命名法，如`UserService`
2. 方法名使用小驼峰命名法，如`getUserInfo`
3. 常量使用全大写加下划线，如`MAX_PAGE_SIZE`
4. 文件名与类名保持一致
5. 控制器名称为模型名加上Controller后缀，如`UserController`
6. Request类使用`Request`后缀，如`UserRequest`
7. Request类里的验证方法名称为控制器方法名加上Rules, 如`saveRules`
8. Service类使用`Service`后缀，如`UserService`
9. Mapper类使用`Mapper`后缀，如`UserMapper`

## 三、代码风格

1. 使用PSR-12代码风格
2. 每行代码不超过120个字符
3. 方法长度控制在50行以内
4. 类的属性和方法按照以下顺序排列：
    - 常量
    - 属性
    - 构造函数
    - 公有方法
    - 受保护方法
    - 私有方法

## 四、注释规范

1. 类注释：说明类的功能和用途
2. 方法注释：说明方法功能、参数和返回值
3. 复杂业务逻辑添加行注释

## 五、依赖注入

1. 使用Hyperf的依赖注入容器
2. 构造函数注入优先
3. 避免在控制器中编写业务逻辑

## 六、数据库操作

1. 数据库查询使用Mapper层
2. 复杂查询使用查询构建器或原生SQL
3. 避免在控制器或服务中直接操作数据库

## 七、异常处理

1. 业务异常统一使用throw new NormalStatusException(异常信息)

## 八、业务规则

1. 控制器统一返回ResponseInterface

## 九、示例代码

Controller控制器示例代码：

```php
<?php
namespace App\Controller;
use App\Mapper\UserMapper;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
<?php
declare(strict_types=1);

namespace App\Crm\Controller;

use App\Crm\Request\CrmUserCommTimelineRequest;
use App\Crm\Service\CrmUserCommTimelineService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户沟通时间控制器
 * Class CrmUserCommTimelineController
 */
#[Controller(prefix: "crm/userCommTimeline"), Auth]
class CrmUserCommTimelineController extends MineController
{
    /**
     * 业务处理服务
     * CrmUserCommTimelineService
     */
    #[Inject]
    protected CrmUserCommTimelineService $service;


    /**
     * 列表
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("crm:userCommTimeline, crm:userCommTimeline:index")]
    public function index(): ResponseInterface
    {
        return $this->success($this->service->getPageList($this->request->all()));
    }

    /**
     * 读取数据
     * @param int $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping("read/{id}"), Permission("crm:userCommTimeline:read")]
    public function read(int $id): ResponseInterface
    {
        return $this->success($this->service->read($id));
    }

    /**
     * 新增
     * @param CrmUserCommTimelineRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("crm:userCommTimeline:save"), OperationLog]
    public function save(CrmUserCommTimelineRequest $request): ResponseInterface
    {
        return $this->success(['id' => $this->service->save($request->all())]);
    }

    /**
     * 更新
     * @param int $id
     * @param CrmUserCommTimelineRequest $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PutMapping("update/{id}"), Permission("crm:userCommTimeline:update"), OperationLog]
    public function update(int $id, CrmUserCommTimelineRequest $request): ResponseInterface
    {
        return $this->service->update($id, $request->all()) ? $this->success() : $this->error();
    }

    /**
     * 单个或批量删除数据到回收站
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[DeleteMapping("delete"), Permission("crm:userCommTimeline:delete"), OperationLog]
    public function delete(): ResponseInterface
    {
        return $this->service->delete((array)$this->request->input('ids', [])) ? $this->success() : $this->error();
    }

}
```

Request示例代码：

```php
<?php
declare(strict_types=1);

namespace App\Crm\Request;

use Mine\MineFormRequest;

/**
 * 用户沟通时间验证数据类
 */
class CrmUserCommTimelineRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function saveRules(): array
    {
        return [
            //用户ID 验证
            'user_id' => 'required',

        ];
    }

    /**
     * 更新数据验证规则
     * return array
     */
    public function updateRules(): array
    {
        return [
            //用户ID 验证
            'user_id' => 'required',

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '主键ID',
            'user_id' => '用户ID',

        ];
    }

}
```

Service示例代码：

```php
<?php
declare(strict_types=1);

namespace App\Crm\Service;

use App\Crm\Mapper\CrmUserCommTimelineMapper;
use Mine\Abstracts\AbstractService;

/**
 * 用户沟通时间服务类
 */
class CrmUserCommTimelineService extends AbstractService
{
    /**
     * @var CrmUserCommTimelineMapper
     */
    public $mapper;

    public function __construct(CrmUserCommTimelineMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}

```

Mapper示例代码：

```php
<?php
declare(strict_types=1);

namespace App\Crm\Mapper;

use App\Crm\Model\CrmUserCommTimeline;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 用户沟通时间Mapper类
 */
class CrmUserCommTimelineMapper extends AbstractMapper
{
    /**
     * @var CrmUserCommTimeline
     */
    public $model;

    public function assignModel(): void
    {
        $this->model = CrmUserCommTimeline::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {

        // 主键ID
        if (isset($params['id']) && $params['id'] !== '') {
            $query->where('id', '=', $params['id']);
        }

        // 用户ID
        if (isset($params['user_id']) && $params['user_id'] !== '') {
            $query->where('user_id', '=', $params['user_id']);
        }

        // 沟通时间
        if (isset($params['comm_time']) && is_array($params['comm_time']) && count($params['comm_time']) === 2) {
            $query->whereBetween(
                'comm_time',
                [$params['comm_time'][0], $params['comm_time'][1]]
            );
        }

        // 沟通内容摘要
        if (isset($params['content']) && $params['content'] !== '') {
            $query->where('content', 'like', '%' . $params['content'] . '%');
        }

        return $query;
    }
}

```
