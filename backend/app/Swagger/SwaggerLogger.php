<?php

namespace App\Swagger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Silent Swagger logger that suppresses warning-level messages
 * to avoid triggering PHP errors during documentation generation.
 */
class SwaggerLogger extends AbstractLogger
{
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        // Only log errors, not warnings or notices — prevents trigger_error crashes
        if (in_array($level, [LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::EMERGENCY, LogLevel::ALERT])) {
            error_log('[Swagger] ' . $message);
        }
        // Silently ignore warnings like "Required @OA\PathItem() not found"
    }
}
