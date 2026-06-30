<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * LengthFilter 测试
 *
 * 覆盖：
 *   - 正文长度正常通过
 *   - 正文过短被过滤
 *   - 标题过短被过滤
 *   - 边界值
 *   - 中文、UTF-8、Emoji
 *   - HTML 内容统计
 */
final class LengthFilterTest extends TestCase
{
    public function testContentPassesDefault(): void
    {
        $filter = new LengthFilter([]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('A', 200),
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testContentTooShortDefault(): void
    {
        $filter = new LengthFilter([]);
        $entry = new FreshRSS_Entry([
            'content' => 'Short content',
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('Content too short', $result->reason);
    }

    public function testContentCustomThreshold(): void
    {
        $filter = new LengthFilter(['min_content_length' => 50]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('X', 60),
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testContentCustomThresholdFail(): void
    {
        $filter = new LengthFilter(['min_content_length' => 50]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('X', 40),
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testContentExactlyThreshold(): void
    {
        $filter = new LengthFilter(['min_content_length' => 100]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('A', 100),
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testContentOneBelowThreshold(): void
    {
        $filter = new LengthFilter(['min_content_length' => 100]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('A', 99),
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testContentZeroDisabled(): void
    {
        $filter = new LengthFilter(['min_content_length' => 0]);
        $entry = new FreshRSS_Entry([
            'content' => '',
            'title' => 'Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testTitleTooShort(): void
    {
        $filter = new LengthFilter([
            'min_content_length' => 0,
            'min_title_length' => 10,
        ]);
        $entry = new FreshRSS_Entry([
            'content' => 'Some content',
            'title' => 'Hi',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('Title too short', $result->reason);
    }

    public function testTitlePasses(): void
    {
        $filter = new LengthFilter([
            'min_content_length' => 0,
            'min_title_length' => 10,
        ]);
        $entry = new FreshRSS_Entry([
            'content' => 'Some content',
            'title' => 'A Perfectly Normal Title',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testTitleDefaultZeroNotChecked(): void
    {
        $filter = new LengthFilter(['min_title_length' => 0]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('A', 300),
            'title' => 'X',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testChineseContent(): void
    {
        $filter = new LengthFilter(['min_content_length' => 100]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('中文', 60), // 120 个汉字
            'title' => '测试标题',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testChineseContentTooShort(): void
    {
        $filter = new LengthFilter(['min_content_length' => 100]);
        $entry = new FreshRSS_Entry([
            'content' => str_repeat('中', 50), // 50 个汉字，不足 100
            'title' => '测试标题',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testHtmlContentLength(): void
    {
        // HTML 标签不计入字符数
        $filter = new LengthFilter(['min_content_length' => 10]);
        $entry = new FreshRSS_Entry([
            'content' => '<div class="article"><p><strong>Hello</strong></p></div>', // 只有 "Hello" 5个字符
            'title' => 'Test',
        ]);

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testNegativeThresholdClampedToZero(): void
    {
        $filter = new LengthFilter(['min_content_length' => -5]);
        $entry = new FreshRSS_Entry([
            'content' => '',
            'title' => 'Test',
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }
}
