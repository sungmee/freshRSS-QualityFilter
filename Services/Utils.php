<?php

declare(strict_types=1);

/**
 * 工具类
 *
 * 提供文本处理和字符统计等静态工具方法。
 * 所有方法均为纯函数，无副作用。
 */
final class Utils
{
    /**
     * 对 HTML 内容进行去标签和规范化处理
     *
     * 处理流程（按 ADR 规范）：
     *   1. strip_tags() — 去除所有 HTML 标签
     *   2. html_entity_decode() — 解码 HTML 实体（如 &amp; → &）
     *   3. preg_replace('/\s+/u', '') — 去除所有空白字符（含 Unicode 空白）
     *   4. trim() — 去除首尾空白
     *
     * @param string $html 原始 HTML 内容
     * @return string 规范化后的纯文本
     */
    public static function stripAndNormalize(string $html): string
    {
        // 去除 HTML 标签
        $text = strip_tags($html);

        // 解码 HTML 实体
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 去除所有 Unicode 空白字符
        $text = preg_replace('/\s+/u', '', $text);

        // 去除首尾空白
        $text = trim($text);

        return $text;
    }

    /**
     * 获取规范化后的字符数
     *
     * 先执行 stripAndNormalize，然后使用 mb_strlen 统计字符数。
     * 支持 UTF-8、中文、Emoji 等多字节字符。
     *
     * @param string $html 原始 HTML 内容
     * @return int 字符数
     */
    public static function getCharCount(string $html): int
    {
        $normalized = self::stripAndNormalize($html);
        return mb_strlen($normalized, 'UTF-8');
    }

    /**
     * 将多行字符串解析为数组（去空行、去首尾空白）
     *
     * 用于将配置页面的 textarea 输入转换为数组。
     * 一行一个规则，空行自动忽略。
     *
     * @param string $multilineText 多行文本
     * @return string[] 非空行数组
     */
    public static function parseMultiline(string $multilineText): array
    {
        $lines = explode("\n", $multilineText);
        $result = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $result[] = $trimmed;
            }
        }

        return $result;
    }

    /**
     * 检查字符串是否包含任意关键字（包含匹配）
     *
     * @param string   $haystack 待搜索的字符串
     * @param string[] $needles  关键字列表
     * @return ?string 匹配到的第一个关键字；未匹配返回 null
     */
    public static function containsAny(string $haystack, array $needles): ?string
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && mb_stripos($haystack, $needle, 0, 'UTF-8') !== false) {
                return $needle;
            }
        }

        return null;
    }

    /**
     * 检查字符串是否等于任意关键字（完全匹配，不区分大小写）
     *
     * @param string   $haystack 待匹配的字符串
     * @param string[] $needles  关键字列表
     * @return ?string 匹配到的关键字；未匹配返回 null
     */
    public static function equalsAny(string $haystack, array $needles): ?string
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && mb_strtolower($haystack, 'UTF-8') === mb_strtolower($needle, 'UTF-8')) {
                return $needle;
            }
        }

        return null;
    }

    /**
     * 截断字符串用于日志显示（避免日志行过长）
     *
     * @param string $text    原始文本
     * @param int    $maxLen  最大长度
     * @return string 截断后的文本
     */
    public static function truncate(string $text, int $maxLen = 80): string
    {
        if (mb_strlen($text, 'UTF-8') <= $maxLen) {
            return $text;
        }

        return mb_substr($text, 0, $maxLen, 'UTF-8') . '...';
    }
}
