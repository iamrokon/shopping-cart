<?php

namespace App\Swagger;

use L5Swagger\Generator as L5Generator;
use OpenApi\Generator as OpenApiGenerator;

/**
 * Custom L5Swagger Generator that uses a silent PSR-3 logger
 * to prevent swagger-php warnings from triggering PHP errors.
 */
class CustomGenerator extends L5Generator
{
    /**
     * Override to inject our custom logger into OpenApiGenerator.
     */
    protected function createOpenApiGenerator(): OpenApiGenerator
    {
        // Pass our silent logger to prevent trigger_error crashes
        $generator = new OpenApiGenerator(new SwaggerLogger());

        if (! empty($this->scanOptions['default_processors_configuration'])
            && is_array($this->scanOptions['default_processors_configuration'])
        ) {
            $generator->setConfig($this->scanOptions['default_processors_configuration']);
        }

        $version = $this->scanOptions['open_api_spec_version'] ?? L5Generator::OPEN_API_DEFAULT_SPEC_VERSION;
        $generator->setVersion($version);

        $this->setProcessors($generator);
        $this->setAnalyser($generator);

        return $generator;
    }
}
