<?php

declare(strict_types=1);

/**
 * 过滤结果值对象
 *
 * 封装单个过滤器对一篇文章的判断结果。
 * 采用 readonly 类（PHP 8.2+）确保不可变性。
 */
readonly class FilterResult
{
    /**
     * @param bool    $passed      文章是否通过过滤
     * @param string  $reason      过滤原因描述（用于日志）
     * @param ?string $matchedRule 匹配到的具体规则（用于日志）
     * @param int     $score       质量评分（预留，供未来使用）
     */
    public function __construct(
        public bool $passed,
        public string $reason = '',
        public ?string $matchedRule = null,
        public int $score = 0,
    ) {}

    /**
     * 创建"已通过"结果的工厂方法
     */
    public static function passed(string $reason = ''): self
    {
        return new self(passed: true, reason: $reason);
    }

    /**
     * 创建"未通过"结果的工厂方法
     */
    public static function failed(string $reason = '', ?string $matchedRule = null, int $score = 0): self
    {
        return new self(passed: false, reason: $reason, matchedRule: $matchedRule, score: $score);
    }
}
