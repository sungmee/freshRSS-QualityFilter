<?php

declare(strict_types=1);

/**
 * Feed 过滤器
 *
 * 支持 Feed 白名单和黑名单。
 * 优先级：黑名单 > 白名单（黑名单中的 Feed 始终被过滤）。
 * 白名单为空时，表示允许所有 Feed（除非被黑名单拦截）。
 *
 * 匹配方式：Feed 名称包含匹配（不区分大小写）。
 *
 * 配置项：
 *   - feed_whitelist: string[] Feed 白名单（一行一个）
 *   - feed_blacklist: string[] Feed 黑名单（一行一个）
 */
final class FeedFilter implements FilterInterface
{
    /** @var string[] Feed 白名单 */
    private array $whitelist;

    /** @var string[] Feed 黑名单 */
    private array $blacklist;

    public function __construct(array $config)
    {
        $this->whitelist = $config['feed_whitelist'] ?? [];
        $this->blacklist = $config['feed_blacklist'] ?? [];

        // 过滤空字符串
        $this->whitelist = array_filter($this->whitelist, fn(string $k) => $k !== '');
        $this->blacklist = array_filter($this->blacklist, fn(string $k) => $k !== '');
    }

    public function getName(): string
    {
        return 'FeedFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        // 获取 Feed 名称
        $feedName = $this->getFeedName($entry);

        // Feed 名称为空时无法判断，直接放行
        if ($feedName === '') {
            return FilterResult::passed('Feed name is empty, cannot determine feed source');
        }

        // 黑名单优先：检查黑名单
        if (count($this->blacklist) > 0) {
            $matched = Utils::containsAny($feedName, $this->blacklist);
            if ($matched !== null) {
                return FilterResult::failed(
                    reason: "Feed \"{$feedName}\" is in blacklist (matched: \"{$matched}\")",
                    matchedRule: $matched,
                );
            }
        }

        // 白名单为空 → 允许所有
        if (count($this->whitelist) === 0) {
            return FilterResult::passed('No feed whitelist configured, all feeds allowed');
        }

        // 检查白名单
        $matched = Utils::containsAny($feedName, $this->whitelist);
        if ($matched === null) {
            return FilterResult::failed(
                reason: "Feed \"{$feedName}\" is not in whitelist",
                matchedRule: 'whitelist',
            );
        }

        return FilterResult::passed("Feed \"{$feedName}\" passed whitelist check");
    }

    /**
     * 安全获取 Feed 名称
     */
    private function getFeedName(FreshRSS_Entry $entry): string
    {
        try {
            $feed = $entry->feed();
            if ($feed !== null && method_exists($feed, 'name')) {
                return $feed->name();
            }
        } catch (\Throwable) {
            // 忽略获取 Feed 时的异常
        }

        return '';
    }
}
