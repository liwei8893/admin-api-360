<?php

declare(strict_types=1);

namespace Mine\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    protected Logger $logger;

    protected StdoutLoggerInterface $console;

    public function __construct()
    {
        $this->console = console();
        $this->logger = container()->get(LoggerFactory::class)->get('mineAdmin');
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->console->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->console->error($throwable->getTraceAsString());
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $format = [
            'success' => false,
            'code' => 500,
            'message' => $throwable->getMessage(),
        ];
        return $response->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(500)->withBody(new SwooleStream(\Hyperf\Codec\Json::encode($format)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
