<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * FeedFilter 测试
 *
 * 覆盖：
 *   - Feed 黑名单匹配
 *   - Feed 白名单匹配
 *   - 黑名单优先级高于白名单
 *   - 白名单为空时全通过
 *   - 不在白名单中的 Feed 被过滤
 *   - Feed 名称为空时处理
 */
final class FeedFilterTest extends TestCase
{
    private function createEntry(string $feedName): FreshRSS_Entry
    {
        $feed = new FreshRSS_Feed($feedName, 1);
        return new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'Content',
            'feed' => $feed,
        ]);
    }

    public function testBlacklistMatch(): void
    {
        $filter = new FeedFilter([
            'feed_blacklist' => ['广告推送', '自动抓取'],
        ]);
        $entry = $this->createEntry('每日广告推送频道');

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('is in blacklist', $result->reason);
    }

    public function testBlacklistNoMatch(): void
    {
        $filter = new FeedFilter([
            'feed_blacklist' => ['广告推送', '自动抓取'],
        ]);
        $entry = $this->createEntry('优质内容源');

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testWhitelistMatch(): void
    {
        $filter = new FeedFilter([
            'feed_whitelist' => ['知乎日报', '少数派'],
        ]);
        $entry = $this->createEntry('知乎日报');

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testWhitelistNoMatch(): void
    {
        $filter = new FeedFilter([
            'feed_whitelist' => ['知乎日报', '少数派'],
        ]);
        $entry = $this->createEntry('垃圾信息源');

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('is not in whitelist', $result->reason);
    }

    public function testWhitelistEmptyAllowsAll(): void
    {
        $filter = new FeedFilter([
            'feed_whitelist' => [],
        ]);
        $entry = $this->createEntry('任意内容源');

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }

    public function testBlacklistPriorityOverWhitelist(): void
    {
        // 同时在白名单和黑名单中，黑名单优先
        $filter = new FeedFilter([
            'feed_whitelist' => ['知乎日报', '少数派'],
            'feed_blacklist' => ['知乎'],
        ]);
        $entry = $this->createEntry('知乎日报');

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
        $this->assertStringContainsString('is in blacklist', $result->reason);
    }

    public function testCaseInsensitive(): void
    {
        $filter = new FeedFilter([
            'feed_blacklist' => ['AD Feed'],
        ]);
        $entry = $this->createEntry('ad feed channel');

        $result = $filter->filter($entry);
        $this->assertFalse($result->passed);
    }

    public function testEmptyFeedNamePasses(): void
    {
        $filter = new FeedFilter([
            'feed_whitelist' => ['知乎日报'],
            'feed_blacklist' => ['广告'],
        ]);
        $entry = new FreshRSS_Entry([
            'title' => 'Title',
            'content' => 'Content',
            'feed' => null, // 无 Feed 对象
        ]);

        $result = $filter->filter($entry);
        $this->assertTrue($result->passed);
    }
}
