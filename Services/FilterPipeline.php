<?php

declare(strict_types=1);

/**
 * 过滤器管道（责任链模式）
 *
 * 管理所有过滤器的注册和执行。
 * 按注册顺序依次运行过滤器，任一返回 passed=false 则短路终止。
 *
 * 使用方式：
 *   $pipeline = new FilterPipeline($logger);
 *   $pipeline->addFilter(new LengthFilter($config));
 *   $pipeline->addFilter(new KeywordFilter($config));
 *   $result = $pipeline->process($entry);
 */
final class FilterPipeline
{
    /** @var FilterInterface[] 已注册的过滤器列表 */
    private array $filters = [];

    /** 日志服务（Debug 模式下记录详细信息） */
    private ?Logger $logger;

    /**
     * @param Logger|null $logger 日志服务实例
     */
    public function __construct(?Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * 注册一个过滤器
     *
     * 过滤器按注册顺序执行。
     *
     * @param FilterInterface $filter 过滤器实例
     */
    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * 批量注册过滤器
     *
     * @param FilterInterface[] $filters 过滤器实例数组
     */
    public function addFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * 对文章执行完整的过滤链
     *
     * 按注册顺序依次运行每个过滤器。
     * 任一过滤器返回 passed=false，立即停止后续过滤器并返回失败结果。
     * 全部通过则返回 passed=true。
     *
     * @param FreshRSS_Entry $entry RSS 文章对象
     * @return FilterResult 最终的过滤结果
     */
    public function process(FreshRSS_Entry $entry): FilterResult
    {
        foreach ($this->filters as $filter) {
            $result = $filter->filter($entry);

            if (!$result->passed) {
                // 记录拒绝日志
                $this->logRejection($entry, $filter, $result);
                return $result;
            }
        }

        // 所有过滤器均已通过
        return FilterResult::passed('All filters passed');
    }

    /**
     * 获取已注册的过滤器数量
     */
    public function getFilterCount(): int
    {
        return count($this->filters);
    }

    /**
     * 记录过滤拒绝事件
     */
    private function logRejection(
        FreshRSS_Entry $entry,
        FilterInterface $filter,
        FilterResult $result,
    ): void {
        if ($this->logger === null) {
            return;
        }

        $feedName = '';
        try {
            $feed = $entry->feed();
            if ($feed !== null) {
                $feedName = $feed->name();
            }
        } catch (\Throwable) {
            $feedName = 'Unknown';
        }

        $title = '';
        try {
            $title = $entry->title();
        } catch (\Throwable) {
            $title = 'Unknown';
        }

        $contentLength = 0;
        try {
            $html = $entry->content();
            if ($html !== '') {
                $contentLength = Utils::getCharCount($html);
            }
        } catch (\Throwable) {
            $contentLength = 0;
        }

        $reason = sprintf(
            'Filter: %s | %s',
            $filter->getName(),
            $result->reason
        );

        $this->logger->log(
            feedName: $feedName,
            title: $title,
            reason: $reason,
            matchedRule: $result->matchedRule,
            contentLength: $contentLength,
        );
    }
}
