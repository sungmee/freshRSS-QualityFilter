<?php

declare(strict_types=1);

/**
 * 长度过滤器
 *
 * 检查文章标题和正文是否满足最小长度要求。
 *
 * 配置项：
 *   - min_content_length: int  正文最小字符数（默认 200）
 *   - min_title_length:  int  标题最小字符数（默认 0，即不限制）
 *
 * 统计方法（按 ADR 规范）：
 *   strip_tags() → html_entity_decode() → preg_replace('/\s+/u','') → mb_strlen()
 */
final class LengthFilter implements FilterInterface
{
    /** 默认最小正文字符数 */
    private const DEFAULT_MIN_CONTENT = 200;

    /** 默认最小标题字符数 */
    private const DEFAULT_MIN_TITLE = 0;

    private int $minContentLength;
    private int $minTitleLength;

    /**
     * @param array $config 配置数组
     */
    public function __construct(array $config)
    {
        $this->minContentLength = (int) ($config['min_content_length'] ?? self::DEFAULT_MIN_CONTENT);
        $this->minTitleLength = (int) ($config['min_title_length'] ?? self::DEFAULT_MIN_TITLE);

        // 确保最小值为 0
        if ($this->minContentLength < 0) {
            $this->minContentLength = 0;
        }
        if ($this->minTitleLength < 0) {
            $this->minTitleLength = 0;
        }
    }

    public function getName(): string
    {
        return 'LengthFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        // 检查标题长度
        if ($this->minTitleLength > 0) {
            $title = $entry->title();
            $titleLength = Utils::getCharCount($title);

            if ($titleLength < $this->minTitleLength) {
                return FilterResult::failed(
                    reason: "Title too short ({$titleLength} < {$this->minTitleLength} chars)",
                    matchedRule: "min_title_length: {$this->minTitleLength}",
                );
            }
        }

        // 检查正文长度
        if ($this->minContentLength > 0) {
            $content = $entry->content();
            $contentLength = Utils::getCharCount($content);

            if ($contentLength < $this->minContentLength) {
                return FilterResult::failed(
                    reason: "Content too short ({$contentLength} < {$this->minContentLength} chars)",
                    matchedRule: "min_content_length: {$this->minContentLength}",
                );
            }
        }

        return FilterResult::passed('Length check passed');
    }
}
