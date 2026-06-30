<?php

declare(strict_types=1);

/**
 * 作者黑名单过滤器
 *
 * 检查文章作者是否在黑名单中。
 * 使用包含匹配（不区分大小写）。
 *
 * 配置项：
 *   - author_blacklist: string[] 作者黑名单（一行一个）
 *
 * 示例关键字：
 *   - 机器人
 *   - 自动发布
 *   - 新华社快讯
 */
final class AuthorFilter implements FilterInterface
{
    /** @var string[] 作者黑名单 */
    private array $blacklist;

    public function __construct(array $config)
    {
        $this->blacklist = $config['author_blacklist'] ?? [];

        // 过滤空字符串
        $this->blacklist = array_filter($this->blacklist, fn(string $k) => $k !== '');
    }

    public function getName(): string
    {
        return 'AuthorFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        if (count($this->blacklist) === 0) {
            return FilterResult::passed('No author blacklist configured');
        }

        $authorName = $this->getAuthorName($entry);

        if ($authorName === '') {
            return FilterResult::passed('No author information available');
        }

        $matched = Utils::containsAny($authorName, $this->blacklist);
        if ($matched !== null) {
            return FilterResult::failed(
                reason: "Author \"{$authorName}\" is in blacklist (matched: \"{$matched}\")",
                matchedRule: $matched,
            );
        }

        return FilterResult::passed('Author check passed');
    }

    /**
     * 安全获取作者名称
     *
     * FreshRSS_Entry 的 authors() 方法可能返回:
     *   - 字符串（单个作者名）
     *   - 数组（多个作者）
     *   - null
     */
    private function getAuthorName(FreshRSS_Entry $entry): string
    {
        try {
            if (method_exists($entry, 'authors')) {
                $authors = $entry->authors();
                if (is_string($authors)) {
                    return $authors;
                }
                if (is_array($authors) && count($authors) > 0) {
                    // 将多个作者用逗号连接
                    return implode(', ', $authors);
                }
                return '';
            }
        } catch (\Throwable) {
            // 忽略异常
        }

        return '';
    }
}
