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

/**
 * Describes methods to grant information derived from the request. The stated
 * methods are the eArc standard methods to hand information from the router to
 * the controllers. (They are not mandatory for the framework itself to
 * function.)
 */
interface RequestInformationInterface
{
    /**
     * Get the request type (GET, POST, PUT, PATCH, DELETE, CONSOLE, ...). Can
     * be different from `$_SERVER['REQUEST_METHOD']`!
     *
     * @return string
     */
    public function getRequestType(): string;

    /**
     * Get the count of args related to the Controller path.
     *
     * @return integer
     */
    public function cntRealArgs(): int;

    /**
     * Get the arg at position $pos related to the Controller path or null if
     * there is no arg at position $pos.
     *
     * @param integer $pos
     * @return string|null
     */
    public function getRealArg(int $pos): ?string;

    /**
     * Get a copy of the args related to the Controller path
     *
     * @return array
     */
    public function getRealArgs(): array;

    /**
     * Get the count of args not related to the Controller path.
     *
     * @return integer
     */
    public function cntVirtualArgs(): int;

    /**
     * Get the arg at position $pos not related to the Controller path or null
     * if there is no arg at position $pos.
     *
     * @param integer $pos
     * @return string|null
     */
    public function getVirtualArg(int $pos): ?string;

    /**
     * Get a copy of the args not related to the Controller path
     *
     * @return array
     */
    public function getVirtualArgs(): array;
}
