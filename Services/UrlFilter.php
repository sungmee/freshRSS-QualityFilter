<?php

declare(strict_types=1);

/**
 * URL 黑名单过滤器
 *
 * 检查文章 URL 是否包含黑名单中的关键字。
 * 使用包含匹配（不区分大小写）。
 *
 * 配置项：
 *   - url_blacklist: string[] URL 黑名单关键字（一行一个）
 *
 * 示例关键字：
 *   - utm_
 *   - tracking
 *   - share=
 *   - from=
 */
final class UrlFilter implements FilterInterface
{
    /** @var string[] URL 黑名单 */
    private array $blacklist;

    public function __construct(array $config)
    {
        $this->blacklist = $config['url_blacklist'] ?? [];

        // 过滤空字符串
        $this->blacklist = array_filter($this->blacklist, fn(string $k) => $k !== '');
    }

    public function getName(): string
    {
        return 'UrlFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        if (count($this->blacklist) === 0) {
            return FilterResult::passed('No URL blacklist configured');
        }

        $url = $entry->link();
        $matched = Utils::containsAny($url, $this->blacklist);

        if ($matched !== null) {
            return FilterResult::failed(
                reason: "URL matched blacklist keyword: \"{$matched}\"",
                matchedRule: $matched,
            );
        }

        return FilterResult::passed('URL check passed');
    }
}
