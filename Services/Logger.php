<?php

declare(strict_types=1);

/**
 * 日志服务
 *
 * 在 Debug 模式开启时记录过滤详情。
 * 日志自动轮转，单文件最大 10MB。
 *
 * 日志格式：
 *   [Y-m-d H:i:s] [Feed: xxx] [Title: xxx] Reason: xxx | Matched: xxx | Length: xxx
 */
final class Logger
{
    /** 日志文件最大大小（字节） */
    private const MAX_LOG_SIZE = 10 * 1024 * 1024; // 10MB

    /** 日志文件路径 */
    private string $logPath;

    /** 是否启用日志 */
    private bool $enabled;

    /**
     * @param string $logDir  日志目录路径
     * @param bool   $enabled 是否启用日志记录
     */
    public function __construct(string $logDir, bool $enabled = false)
    {
        $this->enabled = $enabled;
        $this->logPath = rtrim($logDir, '/') . '/filter.log';

        if ($enabled && !is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
    }

    /**
     * 记录一条过滤日志
     *
     * @param string      $feedName    订阅源名称
     * @param string      $title       文章标题
     * @param string      $reason      过滤原因
     * @param string|null $matchedRule 匹配到的规则
     * @param int         $contentLength 正文长度
     */
    public function log(
        string $feedName,
        string $title,
        string $reason,
        ?string $matchedRule = null,
        int $contentLength = 0,
    ): void {
        if (!$this->enabled) {
            return;
        }

        $this->rotateIfNeeded();

        $timestamp = date('Y-m-d H:i:s');
        $feed = $this->sanitize($feedName);
        $safeTitle = $this->sanitize($title);

        $line = sprintf(
            '[%s] [Feed: %s] [Title: %s] Reason: %s',
            $timestamp,
            $feed,
            $safeTitle,
            $reason
        );

        if ($matchedRule !== null) {
            $line .= ' | Matched: ' . $this->sanitize($matchedRule);
        }

        if ($contentLength > 0) {
            $line .= ' | Length: ' . $contentLength;
        }

        $line .= "\n";

        @file_put_contents($this->logPath, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * 日志轮转：当文件超过 10MB 时，重命名为 filter.log.1
     */
    private function rotateIfNeeded(): void
    {
        if (!file_exists($this->logPath)) {
            return;
        }

        $size = @filesize($this->logPath);
        if ($size === false || $size < self::MAX_LOG_SIZE) {
            return;
        }

        $backupPath = dirname($this->logPath) . '/filter.log.1';
        @rename($this->logPath, $backupPath);
    }

    /**
     * 清理日志内容中的换行符，确保单行输出
     */
    private function sanitize(string $text): string
    {
        return str_replace(["\r", "\n"], ' ', $text);
    }
}
