<?php

declare(strict_types=1);

/**
 * 过滤器接口
 *
 * 所有质量过滤器必须实现此接口。
 * 每个过滤器接收一个 FreshRSS_Entry 对象，返回 FilterResult。
 * 过滤器不得直接终止流程——该职责由 FilterPipeline 统一管理。
 */
interface FilterInterface
{
    /**
     * 对文章执行质量过滤
     *
     * @param FreshRSS_Entry $entry RSS 文章对象
     * @return FilterResult 过滤结果（通过/拒绝 + 原因 + 匹配规则 + 分数）
     */
    public function filter(FreshRSS_Entry $entry): FilterResult;

    /**
     * 返回过滤器名称（用于日志记录和调试）
     */
    public function getName(): string;
}
