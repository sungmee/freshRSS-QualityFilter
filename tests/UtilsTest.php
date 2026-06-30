<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Utils 工具类测试
 *
 * 覆盖：
 *   - HTML 去标签
 *   - HTML 实体解码
 *   - Unicode 空白去除
 *   - 中文字符统计
 *   - Emoji 支持
 *   - 多行解析
 *   - 关键字匹配（包含 / 完全）
 *   - 字符串截断
 */
final class UtilsTest extends TestCase
{
    // ==================== stripAndNormalize ====================

    public function testStripTags(): void
    {
        $result = Utils::stripAndNormalize('<p>Hello World</p>');
        $this->assertSame('HelloWorld', $result);
    }

    public function testDecodeHtmlEntities(): void
    {
        $result = Utils::stripAndNormalize('Hello&amp;World');
        $this->assertSame('Hello&World', $result);
    }

    public function testRemoveUnicodeWhitespace(): void
    {
        $result = Utils::stripAndNormalize("Hello \t\n\r World \u{2003}");
        $this->assertSame('HelloWorld', $result);
    }

    public function testStripAndNormalizeChinese(): void
    {
        $result = Utils::stripAndNormalize('<p>你好，世界</p>');
        $this->assertSame('你好，世界', $result);
    }

    public function testStripAndNormalizeEmoji(): void
    {
        $result = Utils::stripAndNormalize('Hello 😀 World');
        $this->assertSame('Hello😀World', $result);
    }

    public function testStripAndNormalizeEmpty(): void
    {
        $result = Utils::stripAndNormalize('');
        $this->assertSame('', $result);
    }

    public function testStripAndNormalizeOnlyHtml(): void
    {
        $result = Utils::stripAndNormalize('<div><span></span></div>');
        $this->assertSame('', $result);
    }

    public function testStripAndNormalizeComplexNested(): void
    {
        $html = '<article><h1>标题</h1><p>正文 &nbsp; 内容</p><footer>© 2024</footer></article>';
        $result = Utils::stripAndNormalize($html);
        $this->assertSame('标题正文内容©2024', $result);
    }

    // ==================== getCharCount ====================

    public function testCharCountEnglish(): void
    {
        $count = Utils::getCharCount('<p>Hello World</p>');
        $this->assertSame(10, $count); // 10 letters
    }

    public function testCharCountChinese(): void
    {
        $count = Utils::getCharCount('你好世界');
        $this->assertSame(4, $count);
    }

    public function testCharCountMixed(): void
    {
        $count = Utils::getCharCount('Hello你好World世界');
        $this->assertSame(14, $count);
    }

    public function testCharCountWithHtml(): void
    {
        $count = Utils::getCharCount('<p>Hello <strong>World</strong></p>');
        $this->assertSame(10, $count);
    }

    public function testCharCountEmpty(): void
    {
        $count = Utils::getCharCount('');
        $this->assertSame(0, $count);
    }

    public function testCharCountEmoji(): void
    {
        $count = Utils::getCharCount('😀😃😄');
        $this->assertSame(3, $count);
    }

    // ==================== parseMultiline ====================

    public function testParseMultiline(): void
    {
        $input = "line1\nline2\nline3";
        $result = Utils::parseMultiline($input);
        $this->assertSame(['line1', 'line2', 'line3'], $result);
    }

    public function testParseMultilineSkipEmpty(): void
    {
        $input = "line1\n\nline2\n  \nline3\n";
        $result = Utils::parseMultiline($input);
        $this->assertSame(['line1', 'line2', 'line3'], $result);
    }

    public function testParseMultilineTrim(): void
    {
        $input = "  line1  \n  line2  ";
        $result = Utils::parseMultiline($input);
        $this->assertSame(['line1', 'line2'], $result);
    }

    public function testParseMultilineEmpty(): void
    {
        $result = Utils::parseMultiline('');
        $this->assertSame([], $result);
    }

    // ==================== containsAny ====================

    public function testContainsAnyMatch(): void
    {
        $matched = Utils::containsAny('Hello World', ['world']);
        $this->assertSame('world', $matched);
    }

    public function testContainsAnyNoMatch(): void
    {
        $matched = Utils::containsAny('Hello World', ['xyz']);
        $this->assertNull($matched);
    }

    public function testContainsAnyChinese(): void
    {
        $matched = Utils::containsAny('这是一篇快讯文章', ['快讯']);
        $this->assertSame('快讯', $matched);
    }

    public function testContainsAnyFirstMatch(): void
    {
        $matched = Utils::containsAny('Hello World', ['world', 'hello']);
        $this->assertSame('world', $matched); // 按数组顺序返回第一个匹配
    }

    public function testContainsAnyEmptyNeedle(): void
    {
        $matched = Utils::containsAny('Hello World', ['']);
        $this->assertNull($matched);
    }

    // ==================== equalsAny ====================

    public function testEqualsAnyExactMatch(): void
    {
        $matched = Utils::equalsAny('Hello', ['hello']);
        $this->assertSame('hello', $matched);
    }

    public function testEqualsAnyNoMatch(): void
    {
        $matched = Utils::equalsAny('Hello World', ['hello']);
        $this->assertNull($matched);
    }

    public function testEqualsAnyChinese(): void
    {
        $matched = Utils::equalsAny('快讯', ['快讯', '直播']);
        $this->assertSame('快讯', $matched);
    }

    // ==================== truncate ====================

    public function testTruncateShort(): void
    {
        $result = Utils::truncate('Hello', 80);
        $this->assertSame('Hello', $result);
    }

    public function testTruncateLong(): void
    {
        $long = str_repeat('A', 100);
        $result = Utils::truncate($long, 80);
        $this->assertSame(83, mb_strlen($result)); // 80 + '...'
        $this->assertStringEndsWith('...', $result);
    }

    public function testTruncateChinese(): void
    {
        $long = str_repeat('中文', 50);
        $result = Utils::truncate($long, 30);
        $this->assertLessThanOrEqual(33, mb_strlen($result));
        $this->assertStringEndsWith('...', $result);
    }
}
