<?php

declare(strict_types=1);

/**
 * QualityFilter 简体中文翻译
 */

return [
    // 配置页面
    'ext.quality_filter.config_note'        => 'QualityFilter 配置说明：所有过滤器按顺序执行，满足任一过滤条件的文章将被拦截。',
    'ext.quality_filter.general'             => '基本设置',
    'ext.quality_filter.enabled'            => '启用插件',
    'ext.quality_filter.enabled_help'       => '关闭后所有过滤规则暂停生效。',
    'ext.quality_filter.min_content_length' => '最小正文字符数',
    'ext.quality_filter.min_content_length_help' => '正文字符数（去除 HTML 标签和空白后）低于此值的文章将被过滤。默认 200。',
    'ext.quality_filter.min_title_length'   => '最小标题字符数',
    'ext.quality_filter.min_title_length_help' => '标题字符数低于此值的文章将被过滤。设 0 表示不限制。',
    'ext.quality_filter.action'             => '过滤动作',
    'ext.quality_filter.action_skip'        => '跳过导入',
    'ext.quality_filter.action_mark_read'   => '标记已读',
    'ext.quality_filter.action_help'        => '选择对被过滤文章的处理方式。"标记已读"将在 Phase 2 中实现。',
    'ext.quality_filter.debug'              => '调试模式',
    'ext.quality_filter.debug_help'         => '启用后将详细记录过滤日志到 extensions/freshRSS-QualityFilter/logs/filter.log。',

    // 关键字设置
    'ext.quality_filter.keyword_settings'    => '关键字匹配设置',
    'ext.quality_filter.keyword_match_mode'  => '匹配模式',
    'ext.quality_filter.match_contains'      => '包含匹配',
    'ext.quality_filter.match_exact'         => '完全匹配',
    'ext.quality_filter.keyword_match_mode_help' => '包含匹配：标题/正文中包含关键字即触发。完全匹配：标题/正文与关键字完全相等（忽略大小写）。',

    // 标题黑名单
    'ext.quality_filter.title_blacklist'    => '标题黑名单',
    'ext.quality_filter.title_keywords'      => '标题关键字',
    'ext.quality_filter.keywords_placeholder' => '快讯
直播
广告
置顶',
    'ext.quality_filter.keywords_help'       => '一行一个关键字。标题匹配任一关键字的文章将被过滤。',

    // 正文黑名单
    'ext.quality_filter.content_blacklist'  => '正文黑名单',
    'ext.quality_filter.content_keywords'    => '正文关键字',

    // URL 黑名单
    'ext.quality_filter.url_blacklist'       => 'URL 黑名单',
    'ext.quality_filter.url_blacklist_keywords' => 'URL 关键字',
    'ext.quality_filter.url_placeholder'     => 'utm_
tracking
share=
from=',
    'ext.quality_filter.url_help'            => '一行一个关键字。检查文章链接 URL，包含匹配（忽略大小写）。',

    // 作者黑名单
    'ext.quality_filter.author_blacklist'    => '作者黑名单',
    'ext.quality_filter.author_blacklist_authors' => '作者关键字',
    'ext.quality_filter.author_placeholder'  => '机器人
自动发布
新华社快讯',
    'ext.quality_filter.author_help'         => '一行一个关键字。作者名包含匹配（忽略大小写）。',

    // Feed 白名单
    'ext.quality_filter.feed_whitelist'      => 'Feed 白名单',
    'ext.quality_filter.feed_whitelist_feeds' => '允许的订阅源',
    'ext.quality_filter.feed_whitelist_placeholder' => '知乎日报
少数派
Hacker News',
    'ext.quality_filter.feed_whitelist_help'  => '一行一个 Feed 名称。留空表示允许所有 Feed。优先级：黑名单 > 白名单。',

    // Feed 黑名单
    'ext.quality_filter.feed_blacklist'      => 'Feed 黑名单',
    'ext.quality_filter.feed_blacklist_feeds' => '禁止的订阅源',
    'ext.quality_filter.feed_blacklist_placeholder' => '广告推送
自动抓取',
    'ext.quality_filter.feed_blacklist_help'  => '一行一个 Feed 名称。黑名单中的 Feed 文章将被拦截，优先级高于白名单。',

    // 正则
    'ext.quality_filter.regex'               => '正则表达式',
    'ext.quality_filter.regex_rules'         => '正则规则',
    'ext.quality_filter.regex_placeholder'   => '/广告/u
/Sponsored/i
/\[视频\]/u',
    'ext.quality_filter.regex_help'          => '一行一个 PCRE 正则表达式。建议使用 u 修饰符以支持 UTF-8。无效正则会被自动跳过。',
    'ext.quality_filter.regex_scope'         => '作用范围',
    'ext.quality_filter.scope_all'           => '所有（标题 + 正文 + URL）',
    'ext.quality_filter.scope_title'         => '仅标题',
    'ext.quality_filter.scope_content'       => '仅正文',
    'ext.quality_filter.scope_url'           => '仅 URL',
    'ext.quality_filter.regex_scope_help'    => '选择正则规则匹配的范围。',
];
