<?php

declare(strict_types=1);

/**
 * FreshRSS QualityFilter 扩展入口
 *
 * 在 RSS 文章导入数据库之前进行质量过滤。
 * 使用 EntryBeforeInsert Hook，通过责任链模式依次运行过滤器。
 *
 * 版本: 0.1.0
 * 兼容: FreshRSS 1.29.x
 * 许可: MIT
 */

// 引入服务类
require_once __DIR__ . '/Services/FilterInterface.php';
require_once __DIR__ . '/Services/FilterResult.php';
require_once __DIR__ . '/Services/Utils.php';
require_once __DIR__ . '/Services/Logger.php';
require_once __DIR__ . '/Services/FilterPipeline.php';
require_once __DIR__ . '/Services/LengthFilter.php';
require_once __DIR__ . '/Services/KeywordFilter.php';
require_once __DIR__ . '/Services/RegexFilter.php';
require_once __DIR__ . '/Services/UrlFilter.php';
require_once __DIR__ . '/Services/FeedFilter.php';
require_once __DIR__ . '/Services/AuthorFilter.php';
require_once __DIR__ . '/Services/DuplicateFilter.php';

/**
 * QualityFilter 扩展主类
 */
final class FreshRSS_QualityFilter_Extension extends Minz_Extension
{
    /** 插件版本 */
    private const VERSION = '0.1.0';

    /** FreshRSS 最低兼容版本 */
    private const MIN_FRESHRSS_VERSION = '1.29.0';

    /** 默认配置 */
    private const DEFAULT_CONFIG = [
        'enabled'           => true,
        'min_content_length' => 200,
        'min_title_length'  => 0,
        'action'            => 'skip',        // skip | mark_read
        'debug'             => false,
        'title_keywords'    => [],
        'content_keywords'  => [],
        'keyword_match_mode' => 'contains',   // exact | contains
        'url_blacklist'     => [],
        'author_blacklist'  => [],
        'feed_whitelist'    => [],
        'feed_blacklist'    => [],
        'regex_rules'       => [],
        'regex_scope'       => 'all',         // title | content | url | all
    ];

    /** 日志服务 */
    private ?Logger $logger = null;

    /** 过滤管道 */
    private ?FilterPipeline $pipeline = null;

    /**
     * 初始化扩展
     */
    public function init(): void
    {
        // 加载配置
        $config = $this->loadConfig();

        // 如果插件未启用，不注册 Hook
        if (!$config['enabled']) {
            return;
        }

        // 初始化日志服务
        $logDir = __DIR__ . '/logs';
        $this->logger = new Logger($logDir, $config['debug']);

        // 构建过滤器管道
        $this->pipeline = $this->buildPipeline($config);

        // 注册 EntryBeforeInsert Hook
        $this->registerHook('entry_before_insert', 'hookEntryBeforeInsert');

        // 注册配置页面
        if (file_exists(__DIR__ . '/configure.php')) {
            $this->registerHook('check_url_before_add', 'dummyHook'); // 占位，确保 configure 可用
        }
    }

    /**
     * EntryBeforeInsert Hook 处理
     *
     * 在文章写入数据库之前执行质量过滤。
     * 返回 null 表示跳过导入（Skip Import）。
     * 返回 Entry 对象表示允许导入。
     *
     * @param FreshRSS_Entry $entry RSS 文章对象
     * @return FreshRSS_Entry|null
     */
    public function hookEntryBeforeInsert(FreshRSS_Entry $entry): ?FreshRSS_Entry
    {
        if ($this->pipeline === null) {
            return $entry;
        }

        $result = $this->pipeline->process($entry);

        if ($result->passed) {
            return $entry;
        }

        // 根据 Action 决定处理方式
        $action = $this->getConfiguredAction();

        if ($action === 'mark_read') {
            // Mark Read 模式：仍然导入但标记为已读
            // 通过返回 Entry 但修改其状态来实现
            // 注意：FreshRSS Entry 在此阶段可能尚不支持设置 isRead
            // 暂时按 Skip 处理，Phase 2 完善
            return null;
        }

        // 默认：Skip Import — 返回 null 阻止导入
        return null;
    }

    /**
     * 安装扩展时设置默认配置
     */
    public function install(): bool
    {
        foreach (self::DEFAULT_CONFIG as $key => $value) {
            if ($this->getSystemConf($key) === null) {
                $this->saveSystemConf($key, $value);
            }
        }

        return true;
    }

    /**
     * 卸载扩展时清理配置
     */
    public function uninstall(): bool
    {
        foreach (array_keys(self::DEFAULT_CONFIG) as $key) {
            $this->removeSystemConf($key);
        }

        return true;
    }

    /**
     * 处理配置页面的 POST 请求
     *
     * 在 FreshRSS 处理配置页面表单提交时调用。
     */
    public function handleConfigureAction(): void
    {
        if (!Minz_Request::isPost()) {
            return;
        }

        // 基本设置
        $this->saveSystemConf('enabled', (bool) Minz_Request::param('quality_enabled', false));
        $this->saveSystemConf('min_content_length', (int) Minz_Request::param('quality_min_content_length', '200'));
        $this->saveSystemConf('min_title_length', (int) Minz_Request::param('quality_min_title_length', '0'));
        $this->saveSystemConf('action', Minz_Request::param('quality_action', 'skip'));
        $this->saveSystemConf('debug', (bool) Minz_Request::param('quality_debug', false));

        // 关键字设置
        $this->saveSystemConf('title_keywords', Utils::parseMultiline(
            Minz_Request::param('quality_title_keywords', '')
        ));
        $this->saveSystemConf('content_keywords', Utils::parseMultiline(
            Minz_Request::param('quality_content_keywords', '')
        ));
        $this->saveSystemConf('keyword_match_mode', Minz_Request::param('quality_keyword_match_mode', 'contains'));

        // URL / 作者 / Feed 设置
        $this->saveSystemConf('url_blacklist', Utils::parseMultiline(
            Minz_Request::param('quality_url_blacklist', '')
        ));
        $this->saveSystemConf('author_blacklist', Utils::parseMultiline(
            Minz_Request::param('quality_author_blacklist', '')
        ));
        $this->saveSystemConf('feed_whitelist', Utils::parseMultiline(
            Minz_Request::param('quality_feed_whitelist', '')
        ));
        $this->saveSystemConf('feed_blacklist', Utils::parseMultiline(
            Minz_Request::param('quality_feed_blacklist', '')
        ));

        // 正则设置
        $this->saveSystemConf('regex_rules', Utils::parseMultiline(
            Minz_Request::param('quality_regex_rules', '')
        ));
        $this->saveSystemConf('regex_scope', Minz_Request::param('quality_regex_scope', 'all'));

        // 重新初始化（应用新配置）
        $this->init();

        // 显示成功消息
        Minz_Request::good(_t('feedback.conf.updated'), []);
    }

    /**
     * 加载所有配置项（带默认值）
     *
     * @return array 完整配置数组
     */
    public function loadConfig(): array
    {
        $config = [];
        foreach (self::DEFAULT_CONFIG as $key => $default) {
            $value = $this->getSystemConf($key);
            $config[$key] = ($value !== null) ? $value : $default;
        }

        return $config;
    }

    /**
     * 构建过滤器管道
     *
     * @param array $config 配置数组
     * @return FilterPipeline
     */
    private function buildPipeline(array $config): FilterPipeline
    {
        $pipeline = new FilterPipeline($this->logger);

        // 按 ADR 规范顺序注册过滤器：
        // Length → Keyword → Regex → URL → Feed → Author → Duplicate

        // 1. 长度过滤器
        $pipeline->addFilter(new LengthFilter($config));

        // 2. 关键字过滤器
        $pipeline->addFilter(new KeywordFilter($config));

        // 3. 正则过滤器
        $pipeline->addFilter(new RegexFilter($config));

        // 4. URL 黑名单
        $pipeline->addFilter(new UrlFilter($config));

        // 5. Feed 白名单/黑名单
        $pipeline->addFilter(new FeedFilter($config));

        // 6. 作者黑名单
        $pipeline->addFilter(new AuthorFilter($config));

        // 7. 去重（Phase 2 占位）
        $pipeline->addFilter(new DuplicateFilter());

        return $pipeline;
    }

    /**
     * 获取配置的行为模式
     */
    private function getConfiguredAction(): string
    {
        return $this->getSystemConf('action') ?? self::DEFAULT_CONFIG['action'];
    }

    /**
     * 空 Hook 占位（FreshRSS 要求至少有一个 Hook 注册才能显示配置页面）
     */
    public function dummyHook(): void
    {
        // 无操作
    }
}
