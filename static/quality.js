/**
 * QualityFilter 配置页面脚本
 *
 * 最小化交互增强。
 * 仅在配置页面加载，不影响 FreshRSS 前端性能。
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var container = document.querySelector('.quality-filter-config');
    if (!container) {
        return;
    }

    // 表单重置确认
    var form = container.querySelector('form');
    if (form) {
        var resetBtn = form.querySelector('button[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                if (!confirm('确认重置所有配置？此操作不可撤销。')) {
                    e.preventDefault();
                }
            });
        }
    }

    // Debug 模式提示
    var debugCheckbox = document.getElementById('quality_debug');
    if (debugCheckbox) {
        debugCheckbox.addEventListener('change', function () {
            if (this.checked) {
                console.info('QualityFilter: 调试模式已开启，日志将写入 extensions/freshRSS-QualityFilter/logs/filter.log');
            }
        });
    }

    // 正则作用范围联动：无正则规则时禁用作用范围
    var regexTextarea = document.getElementById('quality_regex_rules');
    var regexScope = document.getElementById('quality_regex_scope');
    if (regexTextarea && regexScope) {
        var updateRegexScope = function () {
            var hasRules = regexTextarea.value.trim() !== '';
            regexScope.disabled = !hasRules;
        };
        regexTextarea.addEventListener('input', updateRegexScope);
        updateRegexScope();
    }
});
