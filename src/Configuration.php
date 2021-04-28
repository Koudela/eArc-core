<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/core
 * @link https://github.com/Koudela/earc-core/
 * @copyright Copyright (c) 2018-2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Core;

use eArc\Core\Exceptions\InvalidConfigurationException;
use eArc\Core\Interfaces\ParameterInterface;
use eArc\DI\DI;

abstract class Configuration implements ParameterInterface
{
    public static function build(?string $configurationFile = null)
    {
        self::setBasicParameter($configurationFile);
        self::importConfiguration();
    }

    protected static function setBasicParameter(?string $configurationFile = null): void
    {
        if (!function_exists('di_import_param')) {
            throw new InvalidConfigurationException(sprintf(
                '{f3e7ec6d-2e5d-4794-9c18-2118b2564631} `earc/di` is not bootstrapped. Please place the line `%s::init();` right before the `%s::build()` call.',
                DI::class, self::class
            ));
        }

        $vendorDir = dirname(__DIR__, 3);

        di_import_param(['earc' => [
            'vendor_directory' => $vendorDir,
            'configuration_file' => $configurationFile ?? dirname($vendorDir).'/.earc-config.php',
            'is_production_environment' => false,
        ]]);
    }

    protected static function importConfiguration(): void
    {
        $configFile = di_param(ParameterInterface::CONFIG_FILE);

        if (!is_file($configFile)) {
            throw new InvalidConfigurationException(sprintf(
                '{f9c4bd7e-ff09-465f-a94e-bef4eec84dd9} Configuration file `%s` not found.', $configFile
            ));
        }

        $config = include($configFile);

        if (!is_array($config)) {
            throw new InvalidConfigurationException(sprintf('{b5d0bc54-b221-4b5e-a590-2c01326fe705} `%s` has to return an array.', $configFile));
        }

        di_import_param($config);
    }
}
