<?php

declare(strict_types=1);

/**
 * QualityFilter 配置页面
 *
 * 使用 FreshRSS 原生 UI 风格，不引入任何 CSS 框架。
 * 布局按 ADR 规范：
 *   - General (基本设置)
 *   - Title Blacklist (标题黑名单)
 *   - Content Blacklist (正文黑名单)
 *   - URL Blacklist (URL 黑名单)
 *   - Author Blacklist (作者黑名单)
 *   - Feed Whitelist (Feed 白名单)
 *   - Feed Blacklist (Feed 黑名单)
 *   - Regex (正则规则)
 */

// 通过 FreshRSS 提供的变量获取扩展实例
/** @var FreshRSS_QualityFilter_Extension $this */

// 加载当前配置
$config = $this->loadConfig();
?>

<div class="quality-filter-config">

    <p class="alert alert-warn">
        <?= _t('ext.quality_filter.config_note') ?>
    </p>

    <form method="post" autocomplete="off">
        <input type="hidden" name="_csrf" value="<?= FreshRSS_Auth::csrfToken() ?>" />

        <!-- ==================== General 基本设置 ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.general') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_enabled">
                    <?= _t('ext.quality_filter.enabled') ?>
                </label>
                <div class="group-controls">
                    <input type="checkbox"
                           id="quality_enabled"
                           name="quality_enabled"
                           value="1"
                           <?= $config['enabled'] ? 'checked="checked"' : '' ?> />
                    <span class="help"><?= _t('ext.quality_filter.enabled_help') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="group-name" for="quality_min_content_length">
                    <?= _t('ext.quality_filter.min_content_length') ?>
                </label>
                <div class="group-controls">
                    <input type="number"
                           id="quality_min_content_length"
                           name="quality_min_content_length"
                           value="<?= (int) $config['min_content_length'] ?>"
                           min="0"
                           max="100000"
                           step="1" />
                    <span class="help"><?= _t('ext.quality_filter.min_content_length_help') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="group-name" for="quality_min_title_length">
                    <?= _t('ext.quality_filter.min_title_length') ?>
                </label>
                <div class="group-controls">
                    <input type="number"
                           id="quality_min_title_length"
                           name="quality_min_title_length"
                           value="<?= (int) $config['min_title_length'] ?>"
                           min="0"
                           max="5000"
                           step="1" />
                    <span class="help"><?= _t('ext.quality_filter.min_title_length_help') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="group-name" for="quality_action">
                    <?= _t('ext.quality_filter.action') ?>
                </label>
                <div class="group-controls">
                    <select id="quality_action" name="quality_action">
                        <option value="skip" <?= $config['action'] === 'skip' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.action_skip') ?>
                        </option>
                        <option value="mark_read" <?= $config['action'] === 'mark_read' ? 'selected="selected"' : '' ?>
                                disabled="disabled">
                            <?= _t('ext.quality_filter.action_mark_read') ?> (Phase 2)
                        </option>
                    </select>
                    <span class="help"><?= _t('ext.quality_filter.action_help') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="group-name" for="quality_debug">
                    <?= _t('ext.quality_filter.debug') ?>
                </label>
                <div class="group-controls">
                    <input type="checkbox"
                           id="quality_debug"
                           name="quality_debug"
                           value="1"
                           <?= $config['debug'] ? 'checked="checked"' : '' ?> />
                    <span class="help"><?= _t('ext.quality_filter.debug_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Keyword Match Mode ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.keyword_settings') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_keyword_match_mode">
                    <?= _t('ext.quality_filter.keyword_match_mode') ?>
                </label>
                <div class="group-controls">
                    <select id="quality_keyword_match_mode" name="quality_keyword_match_mode">
                        <option value="contains" <?= $config['keyword_match_mode'] === 'contains' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.match_contains') ?>
                        </option>
                        <option value="exact" <?= $config['keyword_match_mode'] === 'exact' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.match_exact') ?>
                        </option>
                    </select>
                    <span class="help"><?= _t('ext.quality_filter.keyword_match_mode_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Title Blacklist ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.title_blacklist') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_title_keywords">
                    <?= _t('ext.quality_filter.title_keywords') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_title_keywords"
                              name="quality_title_keywords"
                              rows="6"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.keywords_placeholder') ?>"><?=
                        implode("\n", $config['title_keywords'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.keywords_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Content Blacklist ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.content_blacklist') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_content_keywords">
                    <?= _t('ext.quality_filter.content_keywords') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_content_keywords"
                              name="quality_content_keywords"
                              rows="6"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.keywords_placeholder') ?>"><?=
                        implode("\n", $config['content_keywords'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.keywords_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== URL Blacklist ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.url_blacklist') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_url_blacklist">
                    <?= _t('ext.quality_filter.url_blacklist_keywords') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_url_blacklist"
                              name="quality_url_blacklist"
                              rows="4"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.url_placeholder') ?>"><?=
                        implode("\n", $config['url_blacklist'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.url_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Author Blacklist ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.author_blacklist') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_author_blacklist">
                    <?= _t('ext.quality_filter.author_blacklist_authors') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_author_blacklist"
                              name="quality_author_blacklist"
                              rows="4"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.author_placeholder') ?>"><?=
                        implode("\n", $config['author_blacklist'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.author_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Feed Whitelist ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.feed_whitelist') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_feed_whitelist">
                    <?= _t('ext.quality_filter.feed_whitelist_feeds') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_feed_whitelist"
                              name="quality_feed_whitelist"
                              rows="4"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.feed_whitelist_placeholder') ?>"><?=
                        implode("\n", $config['feed_whitelist'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.feed_whitelist_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Feed Blacklist ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.feed_blacklist') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_feed_blacklist">
                    <?= _t('ext.quality_filter.feed_blacklist_feeds') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_feed_blacklist"
                              name="quality_feed_blacklist"
                              rows="4"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.feed_blacklist_placeholder') ?>"><?=
                        implode("\n", $config['feed_blacklist'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.feed_blacklist_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Regex ==================== -->
        <fieldset>
            <legend><?= _t('ext.quality_filter.regex') ?></legend>

            <div class="form-group">
                <label class="group-name" for="quality_regex_rules">
                    <?= _t('ext.quality_filter.regex_rules') ?>
                </label>
                <div class="group-controls">
                    <textarea id="quality_regex_rules"
                              name="quality_regex_rules"
                              rows="6"
                              cols="60"
                              placeholder="<?= _t('ext.quality_filter.regex_placeholder') ?>"><?=
                        implode("\n", $config['regex_rules'])
                    ?></textarea>
                    <span class="help"><?= _t('ext.quality_filter.regex_help') ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="group-name" for="quality_regex_scope">
                    <?= _t('ext.quality_filter.regex_scope') ?>
                </label>
                <div class="group-controls">
                    <select id="quality_regex_scope" name="quality_regex_scope">
                        <option value="all" <?= $config['regex_scope'] === 'all' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.scope_all') ?>
                        </option>
                        <option value="title" <?= $config['regex_scope'] === 'title' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.scope_title') ?>
                        </option>
                        <option value="content" <?= $config['regex_scope'] === 'content' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.scope_content') ?>
                        </option>
                        <option value="url" <?= $config['regex_scope'] === 'url' ? 'selected="selected"' : '' ?>>
                            <?= _t('ext.quality_filter.scope_url') ?>
                        </option>
                    </select>
                    <span class="help"><?= _t('ext.quality_filter.regex_scope_help') ?></span>
                </div>
            </div>
        </fieldset>

        <!-- ==================== Save Button ==================== -->
        <div class="form-group form-actions">
            <button type="submit" class="btn btn-important">
                <?= _t('gen.action.save') ?>
            </button>
            <button type="reset" class="btn">
                <?= _t('gen.action.cancel') ?>
            </button>
        </div>

    </form>
</div>
