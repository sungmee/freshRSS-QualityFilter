<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * FilterPipeline 测试
 *
 * 覆盖：
 *   - 责任链依次执行
 *   - 短路行为（首个失败即停止）
 *   - 全部通过返回 passed
 *   - 过滤器注册
 *   - 日志记录（Debug 模式）
 */
final class FilterPipelineTest extends TestCase
{
    public function testPipelineAllPass(): void
    {
        $pipeline = new FilterPipeline();

        // 添加总是通过的过滤器
        $pipeline->addFilter(new class implements FilterInterface {
            public function filter(FreshRSS_Entry $entry): FilterResult
            {
                return FilterResult::passed('OK');
            }
            public function getName(): string
            {
                return 'AlwaysPass';
            }
        });

        $entry = new FreshRSS_Entry(['title' => 'Test']);
        $result = $pipeline->process($entry);

        $this->assertTrue($result->passed);
    }

    public function testPipelineShortCircuit(): void
    {
        $pipeline = new FilterPipeline();
        $executionLog = [];

        // 第一个过滤器：通过
        $pipeline->addFilter(new class($executionLog) implements FilterInterface {
            private array $log;
            public function __construct(array &$log) { $this->log = &$log; }
            public function filter(FreshRSS_Entry $entry): FilterResult
            {
                $this->log[] = 'filter1';
                return FilterResult::passed('OK');
            }
            public function getName(): string { return 'Filter1'; }
        });

        // 第二个过滤器：拒绝
        $pipeline->addFilter(new class($executionLog) implements FilterInterface {
            private array $log;
            public function __construct(array &$log) { $this->log = &$log; }
            public function filter(FreshRSS_Entry $entry): FilterResult
            {
                $this->log[] = 'filter2';
                return FilterResult::failed('Blocked by filter2');
            }
            public function getName(): string { return 'Filter2'; }
        });

        // 第三个过滤器：不应被执行
        $pipeline->addFilter(new class($executionLog) implements FilterInterface {
            private array $log;
            public function __construct(array &$log) { $this->log = &$log; }
            public function filter(FreshRSS_Entry $entry): FilterResult
            {
                $this->log[] = 'filter3';
                return FilterResult::passed('OK');
            }
            public function getName(): string { return 'Filter3'; }
        });

        $entry = new FreshRSS_Entry(['title' => 'Test']);
        $result = $pipeline->process($entry);

        $this->assertFalse($result->passed);
        $this->assertSame('Blocked by filter2', $result->reason);
        $this->assertSame(['filter1', 'filter2'], $executionLog);
        $this->assertNotContains('filter3', $executionLog);
    }

    public function testPipelineAddFilters(): void
    {
        $pipeline = new FilterPipeline();

        $filters = [
            new class implements FilterInterface {
                public function filter(FreshRSS_Entry $entry): FilterResult
                {
                    return FilterResult::passed('OK');
                }
                public function getName(): string { return 'F1'; }
            },
            new class implements FilterInterface {
                public function filter(FreshRSS_Entry $entry): FilterResult
                {
                    return FilterResult::passed('OK');
                }
                public function getName(): string { return 'F2'; }
            },
        ];

        $pipeline->addFilters($filters);
        $this->assertSame(2, $pipeline->getFilterCount());
    }

    public function testPipelineEmptyPasses(): void
    {
        $pipeline = new FilterPipeline();
        $entry = new FreshRSS_Entry(['title' => 'Test']);

        $result = $pipeline->process($entry);
        $this->assertTrue($result->passed);
    }

    public function testPipelineWithLogger(): void
    {
        $logDir = sys_get_temp_dir() . '/quality_filter_test_' . uniqid();
        mkdir($logDir, 0755, true);

        $logger = new Logger($logDir, true);
        $pipeline = new FilterPipeline($logger);

        // 添加一个会拒绝的过滤器
        $pipeline->addFilter(new class implements FilterInterface {
            public function filter(FreshRSS_Entry $entry): FilterResult
            {
                return FilterResult::failed('Test rejection', 'test_rule');
            }
            public function getName(): string { return 'TestFilter'; }
        });

        $feed = new FreshRSS_Feed('测试源', 1);
        $entry = new FreshRSS_Entry([
            'title' => '测试标题',
            'content' => '测试内容',
            'feed' => $feed,
        ]);

        $result = $pipeline->process($entry);
        $this->assertFalse($result->passed);

        // 检查日志文件是否生成
        $logFile = $logDir . '/filter.log';
        $this->assertFileExists($logFile);

        $logContent = file_get_contents($logFile);
        $this->assertStringContainsString('TestFilter', $logContent);
        $this->assertStringContainsString('test_rule', $logContent);

        // 清理
        unlink($logFile);
        rmdir($logDir);
    }

    public function testPipelineLoggerDisabled(): void
    {
        $logDir = sys_get_temp_dir() . '/quality_filter_test_' . uniqid();
        $logger = new Logger($logDir, false); // 日志禁用
        $pipeline = new FilterPipeline($logger);

        $pipeline->addFilter(new class implements FilterInterface {
            public function filter(FreshRSS_Entry $entry): FilterResult
            {
                return FilterResult::failed('Test');
            }
            public function getName(): string { return 'TestFilter'; }
        });

        $entry = new FreshRSS_Entry(['title' => 'Test']);
        $pipeline->process($entry);

        // 不应创建日志目录
        $this->assertDirectoryDoesNotExist($logDir);
    }

    public function testPipelineOrderIsRespected(): void
    {
        $pipeline = new FilterPipeline();
        $executionOrder = [];

        for ($i = 0; $i < 5; $i++) {
            $index = $i;
            $pipeline->addFilter(new class($index, $executionOrder) implements FilterInterface {
                private int $idx;
                private array $order;
                public function __construct(int $idx, array &$order)
                {
                    $this->idx = $idx;
                    $this->order = &$order;
                }
                public function filter(FreshRSS_Entry $entry): FilterResult
                {
                    $this->order[] = $this->idx;
                    return FilterResult::passed('OK');
                }
                public function getName(): string { return "Filter{$this->idx}"; }
            });
        }

        $entry = new FreshRSS_Entry(['title' => 'Test']);
        $pipeline->process($entry);

        $this->assertSame([0, 1, 2, 3, 4], $executionOrder);
    }
}
