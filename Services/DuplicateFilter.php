<?php

declare(strict_types=1);

/**
 * 去重过滤器（第二阶段预留）
 *
 * 当前版本仅作为占位实现，始终返回 passed=true。
 * Phase 2 将实现：
 *   - Title Hash 去重
 *   - SimHash 近似去重
 *   - URL 精确去重
 *   - Body Hash 去重
 *   - 保留第一篇策略
 */
final class DuplicateFilter implements FilterInterface
{
    public function getName(): string
    {
        return 'DuplicateFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        // Phase 2 占位：始终通过
        return FilterResult::passed('Duplicate check not yet implemented (Phase 2)');
    }
}
