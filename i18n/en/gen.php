<?php

declare(strict_types=1);

/**
 * QualityFilter English translations
 */

return [
    // Configuration page
    'ext.quality_filter.config_note'        => 'QualityFilter: Filters run in sequence. Articles matching any filter rule will be intercepted.',
    'ext.quality_filter.general'             => 'General Settings',
    'ext.quality_filter.enabled'            => 'Enable Plugin',
    'ext.quality_filter.enabled_help'       => 'When disabled, all filter rules are paused.',
    'ext.quality_filter.min_content_length' => 'Minimum Content Length',
    'ext.quality_filter.min_content_length_help' => 'Articles with content shorter than this (after stripping HTML tags and whitespace) will be filtered. Default: 200.',
    'ext.quality_filter.min_title_length'   => 'Minimum Title Length',
    'ext.quality_filter.min_title_length_help' => 'Articles with title shorter than this will be filtered. Set to 0 to disable. Default: 0.',
    'ext.quality_filter.action'             => 'Filter Action',
    'ext.quality_filter.action_skip'        => 'Skip Import',
    'ext.quality_filter.action_mark_read'   => 'Mark as Read',
    'ext.quality_filter.action_help'        => 'Choose how to handle filtered articles. "Mark as Read" will be implemented in Phase 2.',
    'ext.quality_filter.debug'              => 'Debug Mode',
    'ext.quality_filter.debug_help'         => 'Enable detailed filter logging to extensions/freshRSS-QualityFilter/logs/filter.log.',

    // Keyword settings
    'ext.quality_filter.keyword_settings'    => 'Keyword Match Settings',
    'ext.quality_filter.keyword_match_mode'  => 'Match Mode',
    'ext.quality_filter.match_contains'      => 'Contains',
    'ext.quality_filter.match_exact'         => 'Exact',
    'ext.quality_filter.keyword_match_mode_help' => 'Contains: triggers when keyword appears anywhere in text. Exact: triggers only when text matches keyword exactly (case-insensitive).',

    // Title blacklist
    'ext.quality_filter.title_blacklist'    => 'Title Blacklist',
    'ext.quality_filter.title_keywords'      => 'Title Keywords',
    'ext.quality_filter.keywords_placeholder' => "Breaking\nLive\nAd\nSponsored",
    'ext.quality_filter.keywords_help'       => 'One keyword per line. Articles with matching titles will be filtered.',

    // Content blacklist
    'ext.quality_filter.content_blacklist'  => 'Content Blacklist',
    'ext.quality_filter.content_keywords'    => 'Content Keywords',

    // URL blacklist
    'ext.quality_filter.url_blacklist'       => 'URL Blacklist',
    'ext.quality_filter.url_blacklist_keywords' => 'URL Keywords',
    'ext.quality_filter.url_placeholder'     => "utm_\ntracking\nshare=\nfrom=",
    'ext.quality_filter.url_help'            => 'One keyword per line. Checks article URL (case-insensitive contains match).',

    // Author blacklist
    'ext.quality_filter.author_blacklist'    => 'Author Blacklist',
    'ext.quality_filter.author_blacklist_authors' => 'Author Keywords',
    'ext.quality_filter.author_placeholder'  => "Robot\nAutoPost\nAuto-Publish",
    'ext.quality_filter.author_help'         => 'One keyword per line. Author name contains match (case-insensitive).',

    // Feed whitelist
    'ext.quality_filter.feed_whitelist'      => 'Feed Whitelist',
    'ext.quality_filter.feed_whitelist_feeds' => 'Allowed Feeds',
    'ext.quality_filter.feed_whitelist_placeholder' => "Hacker News\nArs Technica\nLobsters",
    'ext.quality_filter.feed_whitelist_help'  => 'One feed name per line. Leave empty to allow all feeds. Priority: Blacklist > Whitelist.',

    // Feed blacklist
    'ext.quality_filter.feed_blacklist'      => 'Feed Blacklist',
    'ext.quality_filter.feed_blacklist_feeds' => 'Blocked Feeds',
    'ext.quality_filter.feed_blacklist_placeholder' => "Ad Feeds\nAuto-generated",
    'ext.quality_filter.feed_blacklist_help'  => 'One feed name per line. Blacklisted feeds take priority over the whitelist.',

    // Regex
    'ext.quality_filter.regex'               => 'Regex Rules',
    'ext.quality_filter.regex_rules'         => 'Regex Patterns',
    'ext.quality_filter.regex_placeholder'   => "/Sponsored/i\n/\\[video\\]/u\n/advertisement/ui",
    'ext.quality_filter.regex_help'          => 'One PCRE regex pattern per line. Use the u modifier for UTF-8 support. Invalid patterns are silently skipped.',
    'ext.quality_filter.regex_scope'         => 'Scope',
    'ext.quality_filter.scope_all'           => 'All (Title + Content + URL)',
    'ext.quality_filter.scope_title'         => 'Title Only',
    'ext.quality_filter.scope_content'       => 'Content Only',
    'ext.quality_filter.scope_url'           => 'URL Only',
    'ext.quality_filter.regex_scope_help'    => 'Select where regex patterns should be applied.',
];
