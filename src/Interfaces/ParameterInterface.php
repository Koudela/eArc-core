<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/core
 * @link https://github.com/Koudela/earc-core/
 * @copyright Copyright (c) 2018-2020 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\Core\Interfaces;

interface ParameterInterface
{
    /** string auto configured */
    const VENDOR_DIR = 'earc.vendor_directory';
    /** string defaults to `.earc-config.php` one directory beneath `self::VENDOR_DIR` */
    const CONFIG_FILE = 'earc.configuration_file';
    /** bool defaults to false */
    const IS_PROD_ENV = 'earc.is_production_environment';
}
