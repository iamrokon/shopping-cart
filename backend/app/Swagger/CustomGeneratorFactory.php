<?php

namespace App\Swagger;

use L5Swagger\GeneratorFactory as L5GeneratorFactory;
use L5Swagger\Generator;
use L5Swagger\SecurityDefinitions;
use L5Swagger\ConfigFactory;

class CustomGeneratorFactory extends L5GeneratorFactory
{
    public function __construct(protected ConfigFactory $configFactory)
    {
        parent::__construct($configFactory);
    }

    /**
     * Override to return our CustomGenerator instead of the default one.
     */
    public function make(string $documentation): Generator
    {
        $config = $this->configFactory->documentationConfig($documentation);

        $paths = $config['paths'];
        $scanOptions = $config['scanOptions'] ?? [];
        $constants = $config['constants'] ?? [];
        $yamlCopyRequired = $config['generate_yaml_copy'] ?? false;

        $secSchemesConfig = $config['securityDefinitions']['securitySchemes'] ?? [];
        $secConfig = $config['securityDefinitions']['security'] ?? [];

        $security = new SecurityDefinitions($secSchemesConfig, $secConfig);

        return new CustomGenerator(
            $paths,
            $constants,
            $yamlCopyRequired,
            $security,
            $scanOptions
        );
    }
}
