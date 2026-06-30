<?php

/**
 * QualityFilter English translations
 */

return [
    'quality_filter' => [
        // Configuration page
        'config_note'              => 'QualityFilter: Filters run in sequence. Articles matching any filter rule will be intercepted.',
        'general'                   => 'General Settings',
        'enabled'                  => 'Enable Plugin',
        'enabled_help'             => 'When disabled, all filter rules are paused.',
        'min_content_length'       => 'Minimum Content Length',
        'min_content_length_help'  => 'Articles with content shorter than this (after stripping HTML tags and whitespace) will be filtered. Default: 200.',
        'min_title_length'         => 'Minimum Title Length',
        'min_title_length_help'    => 'Articles with title shorter than this will be filtered. Set to 0 to disable. Default: 0.',
        'action'                   => 'Filter Action',
        'action_skip'              => 'Skip Import',
        'action_mark_read'         => 'Mark as Read',
        'action_help'              => 'Choose how to handle filtered articles. "Mark as Read" will be implemented in Phase 2.',
        'debug'                    => 'Debug Mode',
        'debug_help'               => 'Enable detailed filter logging to extensions/freshRSS-QualityFilter/logs/filter.log.',

        // Keyword settings
        'keyword_settings'          => 'Keyword Match Settings',
        'keyword_match_mode'        => 'Match Mode',
        'match_contains'            => 'Contains',
        'match_exact'               => 'Exact',
        'keyword_match_mode_help'   => 'Contains: triggers when keyword appears anywhere in text. Exact: triggers only when text matches keyword exactly (case-insensitive).',

        // Title blacklist
        'title_blacklist'           => 'Title Blacklist',
        'title_keywords'            => 'Title Keywords',
        'keywords_placeholder'      => "Breaking\nLive\nAd\nSponsored",
        'keywords_help'             => 'One keyword per line. Articles with matching titles will be filtered.',

        // Content blacklist
        'content_blacklist'         => 'Content Blacklist',
        'content_keywords'          => 'Content Keywords',

        // URL blacklist
        'url_blacklist'             => 'URL Blacklist',
        'url_blacklist_keywords'    => 'URL Keywords',
        'url_placeholder'           => "utm_\ntracking\nshare=\nfrom=",
        'url_help'                  => 'One keyword per line. Checks article URL (case-insensitive contains match).',

        // Author blacklist
        'author_blacklist'          => 'Author Blacklist',
        'author_blacklist_authors'  => 'Author Keywords',
        'author_placeholder'        => "Robot\nAutoPost\nAuto-Publish",
        'author_help'               => 'One keyword per line. Author name contains match (case-insensitive).',

        // Feed whitelist
        'feed_whitelist'            => 'Feed Whitelist',
        'feed_whitelist_feeds'      => 'Allowed Feeds',
        'feed_whitelist_placeholder' => "Hacker News\nArs Technica\nLobsters",
        'feed_whitelist_help'       => 'One feed name per line. Leave empty to allow all feeds. Priority: Blacklist > Whitelist.',

        // Feed blacklist
        'feed_blacklist'            => 'Feed Blacklist',
        'feed_blacklist_feeds'      => 'Blocked Feeds',
        'feed_blacklist_placeholder' => "Ad Feeds\nAuto-generated",
        'feed_blacklist_help'       => 'One feed name per line. Blacklisted feeds take priority over the whitelist.',

        // Regex
        'regex'                     => 'Regex Rules',
        'regex_rules'               => 'Regex Patterns',
        'regex_placeholder'         => "/Sponsored/i\n/\\[video\\]/u\n/advertisement/ui",
        'regex_help'                => 'One PCRE regex pattern per line. Use the u modifier for UTF-8 support. Invalid patterns are silently skipped.',
        'regex_scope'               => 'Scope',
        'scope_all'                 => 'All (Title + Content + URL)',
        'scope_title'               => 'Title Only',
        'scope_content'             => 'Content Only',
        'scope_url'                 => 'URL Only',
        'regex_scope_help'          => 'Select where regex patterns should be applied.',
    ],
];
