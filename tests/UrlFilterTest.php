<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * UrlFilter 测试
 *
 * 覆盖：
 *   - URL 包含匹配
 *   - 无匹配通过
 *   - 空黑名单通过
 *   - 大小写不敏感
 *   - UTM 参数等常见过滤场景
 */
final class UrlFilterTest extends TestCase
{
    public function testUrlContainsMatch(): void
    {
        $filter = new UrlFilter([
            'url_blacklist' => ['utm_', 'tracking'],
        ]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/article?utm_source=twitter',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertSame('utm_', $result->matchedRule);
    }

    public function testUrlNoMatch(): void
    {
        $filter = new UrlFilter([
            'url_blacklist' => ['utm_', 'tracking'],
        ]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/normal-article',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testEmptyBlacklist(): void
    {
        $filter = new UrlFilter(['url_blacklist' => []]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/?utm_source=twitter',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testCaseInsensitive(): void
    {
        $filter = new UrlFilter([
            'url_blacklist' => ['UTM_'],
        ]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/?utm_source=test',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testShareParam(): void
    {
        $filter = new UrlFilter([
            'url_blacklist' => ['share='],
        ]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/article?share=1',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testFromParam(): void
    {
        $filter = new UrlFilter([
            'url_blacklist' => ['from='],
        ]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/?from=feed',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testMultipleKeywordsFirstMatch(): void
    {
        $filter = new UrlFilter([
            'url_blacklist' => ['share=', 'from=', 'utm_'],
        ]);
        $entry = new FreshRSS_Entry([
            'link' => 'https://example.com/?from=app&share=1',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertSame('share=', $result->matchedRule); // 数组顺序，share= 在 from= 之前
    }
}
