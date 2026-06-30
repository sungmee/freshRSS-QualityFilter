<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * RegexFilter 测试
 *
 * 覆盖：
 *   - 标题正则匹配
 *   - 正文正则匹配
 *   - URL 正则匹配
 *   - scope 作用范围
 *   - UTF-8 正则
 *   - 无效正则处理
 *   - 空规则列表
 */
final class RegexFilterTest extends TestCase
{
    public function testTitleRegexMatch(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/广告/u', '/Sponsored/i'],
            'regex_scope' => 'title',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '这是一条广告推送',
            'content' => 'Content',
            'link' => 'https://example.com/article',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('/广告/u', $result->matchedRule);
    }

    public function testTitleRegexNoMatch(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/广告/u'],
            'regex_scope' => 'title',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '这是一篇正常文章',
            'content' => 'Content',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testContentRegexMatch(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/Sponsored/i'],
            'regex_scope' => 'content',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'This is a sponsored post',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testUrlRegexMatch(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/utm_/i'],
            'regex_scope' => 'url',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'Content',
            'link' => 'https://example.com/?utm_source=twitter',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testUrlRegexNoMatchOnTitle(): void
    {
        // scope 为 url，不应匹配标题
        $filter = new RegexFilter([
            'regex_rules' => ['/广告/u'],
            'regex_scope' => 'url',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '这是一条广告',
            'content' => 'Content',
            'link' => 'https://example.com/normal',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testScopeAll(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/\[video\]/i'],
            'regex_scope' => 'all',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'Watch our [Video] tutorial',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testInvalidRegexSkipped(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/(invalid[unclosed/i', '/valid/i'],
            'regex_scope' => 'title',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Valid match here',
            'content' => 'Content',
            'link' => 'https://example.com',
        ]);

        // 无效正则被跳过，第二个正则匹配
        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('/valid/i', $result->matchedRule);
    }

    public function testAllInvalidRegexPasses(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/(invalid unclosed'],
            'regex_scope' => 'title',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Any Title',
            'content' => 'Content',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testEmptyRulesPasses(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => [],
            'regex_scope' => 'all',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'Content',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testUtf8Regex(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/[\x{4e00}-\x{9fff}]+快讯/u'],
            'regex_scope' => 'all',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '新华社快讯',
            'content' => 'Content',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testMultipleRulesFirstMatch(): void
    {
        $filter = new RegexFilter([
            'regex_rules' => ['/foo/i', '/bar/i', '/baz/i'],
            'regex_scope' => 'content',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'This has bar in it',
            'link' => 'https://example.com',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('/bar/i', $result->matchedRule);
    }
}
