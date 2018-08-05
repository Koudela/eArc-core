<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/core
 * @link https://github.com/Koudela/earc-core/
 * @copyright Copyright (c) 2018 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\core\interfaces;

use eArc\core\exceptions\NoControllerFoundException;

/**
 * Describes a router that exposes methods to grant information derived from the
 * request. This methods are mandatory for the communication to the eArc
 * dispatcher class.
 */
interface LocateControllerInterface
{
    /**
     * Get the absolute paths to the access controllers in charge for the given 
     * HTTP request or an empty set if no access controller is present.
     *
     * @return array
     */
    public function getAbsolutePathsToAccessControllers(): array;

    /**
     * Get the absolute path to the business controller in charge for the given 
     * HTTP request.
     *
     * @throws NoControllerFoundException if no controller is available
     * @return string
     */
    public function getAbsolutePathToMainController(): string;
}
