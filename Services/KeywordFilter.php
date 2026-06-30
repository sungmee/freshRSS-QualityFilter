<?php

declare(strict_types=1);

/**
 * 关键字过滤器
 *
 * 检查文章标题和正文是否包含黑名单关键字。
 * 支持两种匹配模式：
 *   - exact:    完全匹配（不区分大小写）
 *   - contains: 包含匹配（不区分大小写）
 *
 * 配置项：
 *   - title_keywords:    string[] 标题黑名单关键字（一行一个）
 *   - content_keywords:  string[] 正文黑名单关键字（一行一个）
 *   - keyword_match_mode: string 匹配模式（exact / contains，默认 contains）
 */
final class KeywordFilter implements FilterInterface
{
    /** @var string[] 标题黑名单 */
    private array $titleKeywords;

    /** @var string[] 正文黑名单 */
    private array $contentKeywords;

    /** 匹配模式：exact 或 contains */
    private string $matchMode;

    public function __construct(array $config)
    {
        $this->titleKeywords = $config['title_keywords'] ?? [];
        $this->contentKeywords = $config['content_keywords'] ?? [];
        $this->matchMode = $config['keyword_match_mode'] ?? 'contains';

        // 过滤空字符串
        $this->titleKeywords = array_filter($this->titleKeywords, fn(string $k) => $k !== '');
        $this->contentKeywords = array_filter($this->contentKeywords, fn(string $k) => $k !== '');
    }

    public function getName(): string
    {
        return 'KeywordFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        // 检查标题关键字
        if (count($this->titleKeywords) > 0) {
            $title = $entry->title();
            $matched = $this->matchKeywords($title, $this->titleKeywords);

            if ($matched !== null) {
                return FilterResult::failed(
                    reason: "Title matched blacklist keyword: \"{$matched}\"",
                    matchedRule: $matched,
                );
            }
        }

        // 检查正文关键字
        if (count($this->contentKeywords) > 0) {
            $content = $entry->content();
            $matched = $this->matchKeywords($content, $this->contentKeywords);

            if ($matched !== null) {
                return FilterResult::failed(
                    reason: "Content matched blacklist keyword: \"{$matched}\"",
                    matchedRule: $matched,
                );
            }
        }

        return FilterResult::passed('Keyword check passed');
    }

    /**
     * 在文本中匹配关键字
     *
     * @param string   $text    待检查的文本
     * @param string[] $needles 关键字列表
     * @return string|null 匹配到的关键字，未匹配返回 null
     */
    private function matchKeywords(string $text, array $needles): ?string
    {
        if ($this->matchMode === 'exact') {
            return Utils::equalsAny($text, $needles);
        }

        // 默认使用包含匹配
        return Utils::containsAny($text, $needles);
    }
}
