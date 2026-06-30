<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * KeywordFilter 测试
 *
 * 覆盖：
 *   - 标题包含匹配
 *   - 正文包含匹配
 *   - 完全匹配模式
 *   - 中文关键字
 *   - 空关键字列表
 *   - 大小写不敏感
 */
final class KeywordFilterTest extends TestCase
{
    public function testTitleContainsMatch(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['快讯', '直播', '广告'],
            'keyword_match_mode' => 'contains',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '今日新闻快讯：重要消息',
            'content' => 'Normal content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('快讯', $result->matchedRule);
    }

    public function testTitleNoMatch(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['快讯', '直播', '广告'],
            'keyword_match_mode' => 'contains',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '今日重要新闻',
            'content' => 'Normal content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testContentContainsMatch(): void
    {
        $filter = new KeywordFilter([
            'content_keywords' => ['阅读全文', '继续阅读', '点击查看'],
            'keyword_match_mode' => 'contains',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Normal Title',
            'content' => '这是一篇文章的内容，请点击阅读全文查看详情。',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('阅读全文', $result->matchedRule);
    }

    public function testContentNoMatch(): void
    {
        $filter = new KeywordFilter([
            'content_keywords' => ['阅读全文', '继续阅读'],
            'keyword_match_mode' => 'contains',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Normal Title',
            'content' => '这是一篇完整独立的文章。',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testExactMatchTitle(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['快讯', '直播'],
            'keyword_match_mode' => 'exact',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '快讯',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testExactMatchTitleFailOnContains(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['快讯'],
            'keyword_match_mode' => 'exact',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '今日快讯新闻',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed); // 不完全相等，不匹配
    }

    public function testExactMatchCaseInsensitive(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['Breaking News'],
            'keyword_match_mode' => 'exact',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'breaking news',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testContainsCaseInsensitive(): void
    {
        $filter = new KeywordFilter([
            'content_keywords' => ['sponsored'],
            'keyword_match_mode' => 'contains',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'This is SPONSORED content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testEmptyKeywordsPasses(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => [],
            'content_keywords' => [],
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Any Title',
            'content' => 'Any Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testDefaultMatchModeIsContains(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['广告'],
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '这是一条广告推送',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testEmptyKeywordFilteredOut(): void
    {
        $filter = new KeywordFilter([
            'title_keywords' => ['', '快讯', '', '直播'],
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '快讯',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertSame('快讯', $result->matchedRule);
    }

    public function testTitleCheckedBeforeContent(): void
    {
        // 标题先被检查，即使正文也有关键字，应返回标题的匹配
        $filter = new KeywordFilter([
            'title_keywords' => ['快讯'],
            'content_keywords' => ['广告'],
            'keyword_match_mode' => 'contains',
        ]);
        $entry = new FreshRSS_Entry([
            'title' => '今日快讯',
            'content' => '广告内容',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertSame('快讯', $result->matchedRule);
    }
}
