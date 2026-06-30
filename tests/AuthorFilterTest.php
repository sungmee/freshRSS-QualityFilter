<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * AuthorFilter 测试
 *
 * 覆盖：
 *   - 作者名包含匹配
 *   - 无匹配通过
 *   - 空作者通过
 *   - 多作者处理
 *   - 空黑名单通过
 *   - 大小写不敏感
 */
final class AuthorFilterTest extends TestCase
{
    public function testAuthorContainsMatch(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['机器人', '自动发布', '新华社快讯'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => '新闻机器人',
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertSame('机器人', $result->matchedRule);
    }

    public function testAuthorNoMatch(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['机器人', '自动发布'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => '张三',
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testEmptyBlacklist(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => [],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => '任何作者',
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testEmptyAuthorPasses(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['机器人'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => '',
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testMultipleAuthors(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['机器人'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => ['张三', '新闻机器人'],
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testCaseInsensitive(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['Robot'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => 'auto robot system',
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testXinhuaQuickNews(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['新华社快讯'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => '新华社快讯',
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testNullAuthorsPasses(): void
    {
        $filter = new AuthorFilter([
            'author_blacklist' => ['机器人'],
        ]);
        $entry = new FreshRSS_Entry([
            'authors' => null,
            'title' => 'Title',
            'content' => 'Content',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }
}
