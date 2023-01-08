<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\Auth;
use Mine\Exception\TokenException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AuthAspect.
 */
#[Aspect]
class AuthAspect extends AbstractAspect
{

    public array $annotations = [
        Auth::class
    ];

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        /* @var Auth $auth */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class])) {
            $auth = $proceedingJoinPoint->getAnnotationMetadata()->method[Auth::class];
        }

        $scene = $auth->scene ?? 'default';

        $loginUser = user($scene);

        if (! $loginUser->check(null, $scene)) {
            throw new TokenException(t('jwt.validate_fail'));
        }

        return $proceedingJoinPoint->process();
    }
}
