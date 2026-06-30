<?php

declare(strict_types=1);

/**
 * 测试引导文件
 *
 * 加载所有服务类并定义 FreshRSS Mock 类。
 * 由于测试在 FreshRSS 环境外部运行，需要模拟 FreshRSS_Entry 等相关类。
 */

// 加载服务类
require_once __DIR__ . '/../Services/FilterInterface.php';
require_once __DIR__ . '/../Services/FilterResult.php';
require_once __DIR__ . '/../Services/Utils.php';
require_once __DIR__ . '/../Services/Logger.php';
require_once __DIR__ . '/../Services/FilterPipeline.php';
require_once __DIR__ . '/../Services/LengthFilter.php';
require_once __DIR__ . '/../Services/KeywordFilter.php';
require_once __DIR__ . '/../Services/RegexFilter.php';
require_once __DIR__ . '/../Services/UrlFilter.php';
require_once __DIR__ . '/../Services/FeedFilter.php';
require_once __DIR__ . '/../Services/AuthorFilter.php';
require_once __DIR__ . '/../Services/DuplicateFilter.php';

/**
 * FreshRSS_Feed Mock 类
 */
class FreshRSS_Feed
{
    private string $nameValue;
    private int $idValue;

    public function __construct(string $name = '', int $id = 0)
    {
        $this->nameValue = $name;
        $this->idValue = $id;
    }

    public function name(): string
    {
        return $this->nameValue;
    }

    public function id(): int
    {
        return $this->idValue;
    }
}

/**
 * FreshRSS_Entry Mock 类
 *
 * 模拟 FreshRSS 文章对象，提供测试所需的所有方法。
 */
class FreshRSS_Entry
{
    private string $titleValue;
    private string $contentValue;
    private string $linkValue;
    private mixed $authorsValue;
    private ?FreshRSS_Feed $feedValue;
    private string $guidValue;
    private int $idValue;

    public function __construct(array $data = [])
    {
        $this->titleValue = $data['title'] ?? '';
        $this->contentValue = $data['content'] ?? '';
        $this->linkValue = $data['link'] ?? '';
        $this->authorsValue = $data['authors'] ?? '';
        $this->feedValue = $data['feed'] ?? null;
        $this->guidValue = $data['guid'] ?? '';
        $this->idValue = $data['id'] ?? 0;
    }

    public function title(): string
    {
        return $this->titleValue;
    }

    public function content(): string
    {
        return $this->contentValue;
    }

    public function link(): string
    {
        return $this->linkValue;
    }

    /**
     * @return string|string[]|null
     */
    public function authors(): mixed
    {
        return $this->authorsValue;
    }

    public function feed(): ?FreshRSS_Feed
    {
        return $this->feedValue;
    }

    public function guid(): string
    {
        return $this->guidValue;
    }

    public function id(): int
    {
        return $this->idValue;
    }
}
