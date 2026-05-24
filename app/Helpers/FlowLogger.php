<?php

namespace App\Helpers;

class FlowLogger
{
    private static string $file = '';

    public static function log(string $step, string|array $data, ?\Throwable $exception = null): void
    {
        if (!self::$file) {
            self::$file = storage_path('logs/flow-debug-' . date('Y-m-d') . '.log');
        }

        $entry = sprintf(
            "[%s] [%s] %s",
            now()->format('Y-m-d H:i:s.u'),
            strtoupper($step),
            is_string($data) ? $data : json_encode($data, JSON_PRETTY_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if ($exception) {
            $entry .= "\nEXCEPTION: " . $exception->getMessage() . "\n" . $exception->getTraceAsString();
        }

        $entry .= "\n---\n";

        @file_put_contents(self::$file, $entry, FILE_APPEND | LOCK_EX);
    }

    public static function getLogPath(): string
    {
        if (!self::$file) {
            self::$file = storage_path('logs/flow-debug-' . date('Y-m-d') . '.log');
        }
        return self::$file;
    }

    public static function getContents(int $lines = 200): string
    {
        $path = self::getLogPath();
        if (!file_exists($path)) {
            return "No hay logs aún.";
        }
        $content = file($path);
        $content = array_slice($content, -$lines);
        return implode('', $content);
    }

    public static function clear(): void
    {
        $path = self::getLogPath();
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
