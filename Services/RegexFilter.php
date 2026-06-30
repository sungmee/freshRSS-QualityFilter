<?php

declare(strict_types=1);

/**
 * 正则表达式过滤器
 *
 * 使用 PCRE 正则对文章标题、正文、URL 进行匹配。
 * 支持 UTF-8 模式（u 修饰符）。
 *
 * 配置项：
 *   - regex_rules: string[] 正则规则（一行一个，支持 PCRE 语法）
 *   - regex_scope: string   作用范围（title / content / url / all，默认 all）
 */
final class RegexFilter implements FilterInterface
{
    /** @var string[] 正则规则列表 */
    private array $rules;

    /** 作用范围 */
    private string $scope;

    public function __construct(array $config)
    {
        $this->rules = $config['regex_rules'] ?? [];
        $this->scope = $config['regex_scope'] ?? 'all';

        // 过滤空字符串
        $this->rules = array_filter($this->rules, fn(string $r) => $r !== '');
    }

    public function getName(): string
    {
        return 'RegexFilter';
    }

    public function filter(FreshRSS_Entry $entry): FilterResult
    {
        if (count($this->rules) === 0) {
            return FilterResult::passed('No regex rules configured');
        }

        // 确定需要检查的文本范围
        $targets = $this->getTargets($entry);

        foreach ($targets as $label => $text) {
            $matchedRule = $this->checkText($text);
            if ($matchedRule !== null) {
                return FilterResult::failed(
                    reason: "{$label} matched regex: \"{$matchedRule}\"",
                    matchedRule: $matchedRule,
                );
            }
        }

        return FilterResult::passed('Regex check passed');
    }

    /**
     * 根据 scope 确定需要检查的文本集合
     *
     * @param FreshRSS_Entry $entry
     * @return array<string, string> [标签 => 文本]
     */
    private function getTargets(FreshRSS_Entry $entry): array
    {
        $targets = [];

        if ($this->scope === 'title' || $this->scope === 'all') {
            $targets['Title'] = $entry->title();
        }

        if ($this->scope === 'content' || $this->scope === 'all') {
            $targets['Content'] = $entry->content();
        }

        if ($this->scope === 'url' || $this->scope === 'all') {
            $targets['URL'] = $entry->link();
        }

        return $targets;
    }

    /**
     * 对文本逐一测试正则规则
     *
     * @param string $text 待检查的文本
     * @return string|null 匹配到的正则规则（原始字符串），未匹配返回 null
     */
    private function checkText(string $text): ?string
    {
        foreach ($this->rules as $rule) {
            // 使用 @ 抑制无效正则的警告，preg_match 返回 false 时跳过
            $result = @preg_match($rule, $text);

            if ($result === false) {
                // 无效正则，跳过
                continue;
            }

            if ($result === 1) {
                return $rule;
            }
        }

        return null;
    }
}
