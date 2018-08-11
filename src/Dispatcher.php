<?php
/**
 * e-Arc Framework - the explicit Architecture Framework
 *
 * @package earc/core
 * @link https://github.com/Koudela/earc-core/
 * @copyright Copyright (c) 2018 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\core;

use eArc\core\interfaces\LocateControllerInterface;

/**
 * Dispatcher class. Starts and controls the lifecycle of the application.
 */
class Dispatcher
{
    protected $router;
    protected $container;
    protected $dispatchStart;
    protected $dispatchBetween;
    protected $dispatchEnd;

    public function __construct(LocateControllerInterface $router, \Psr\Container\ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
        $this->dispatchStart = [];
        $this->dispatchBetween = [];
        $this->dispatchEnd = [];
    }

    /**
     * Register a closure to the beginning of the lifecycle.
     *
     * @param \Closure $fnc
     * @return void
     */    
    final public function registerDispatchStart(\Closure $fnc): void
    {
        $this->dispatchStart[] = $fnc;
    }

    /**
     * Register a closure after access controllers but before the main
     * controller.
     *
     * @param \Closure $fnc
     * @return void
     */    
    final public function registerDispatchBetween(\Closure $fnc): void
    {
        $this->dispatchBetween[] = $fnc;
    }

    /**
     * Register a closure to the end of the lifecycle.
     *
     * @param \Closure $fnc
     * @return void
     */
    final public function registerDispatchEnd(\Closure $fnc): void
    {
        $this->dispatchEnd[] = $fnc;
    }

    /**
     * Starts the dispatching process.
     *
     * @return void
     */
    final public function run(): void
    {
        $this->dispatch($this->router);
    }

    /**
     * Makes the registered variables available and executes the access/main
     * controllers found by the router as well as the closures registered to the 
     * lifecycle.
     *
     * @param LocateControllerInterface $router
     * @return void
     */
    private function dispatch(LocateControllerInterface $router): void
    {
        // execute closures registered for dispatch start
        foreach ($this->dispatchStart as $fnc) {
            $fnc($router, $this->container);
        }

        // execute the access controllers
        foreach($router->getAbsolutePathsToAccessControllers() as $path) {
            $accessController = require($path);

            if (!$accessController instanceof \Closure)
                new \RuntimeException('Access controller must be of type Closure.');

            $newRouter = $accessController($router, $this->container);

            // start the dispatching process with the new router if the access 
            // controller returns an instance of a class implementing the
            // LocateControllerInterface
            if ($newRouter instanceof LocateControllerInterface) {
                $this->dispatch($newRouter);
                return;
            }
        }

        // execute closures registered for in between the dispatching process
        foreach ($this->dispatchBetween as $fnc) {
            $fnc($router, $this->container);
        }

        // execute the main controller
        $path = $router->getAbsolutePathToMainController();
        $mainController = require($path);

        if (!$mainController instanceof \Closure)
            new \RuntimeException('Main controller must be of type Closure.');

        $newRouter = $mainController($router, $this->container);

        // start the dispatching process with the new router if the main
        // controller returns an instance of a class implementing the
        // LocateControllerInterface
        if ($newRouter instanceof LocateControllerInterface) {
            $this->dispatch($newRouter);
            return;
        }

        // execute closures registered for dispatch end
        foreach ($this->dispatchEnd as $fnc) {
            $fnc($router, $this->container);
        }
    }
}
