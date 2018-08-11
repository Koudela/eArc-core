# eArc core

Core component of the [eArc framework](https://github.com/Koudela/eArc-core).

The eArc core holds the dispatcher component. It controls the lifecycle of the
app.
 
 ## Table of Contents
 
 - [Installation](#installation)
 - [Usage](#usage)
   - [Launching the application](#launching-the-application)
   - [Using the controller](#using-the-controller)
   - [The application lifecycle](#the-application-lifecycle)
     - [The access controllers](#the-access-controllers)
     - [The main controller](#the-main-controller)
     - [Middleware](#middleware) 
       - [Example](#example)

## Installation

If you want to use the eArc dispatcher without the eArc framework, you can
install the component via composer.

```
$ composer install earc/core
```

## Usage

### Launching the application

A new dispatcher object is always constructed with a 
[router](https://github.com/Koudela/eArc-router) and a 
[dependency container](https://github.com/Koudela/eArc-di).

You can use your own router class as long as it implements the
eArc locate controller interface. The dependency container must be 
[psr-11](https://www.php-fig.org/psr/psr-11/) compatible, in particular it has
to implement the PSR container interface.

```php
use eArc\core\Dispatcher;

$dispatcher = new Dispatcher(
    $router, // instance of \eArc\core\interfaces\LocateControllerInterface
    $dependencyContainer // instance of \Psr\Container\ContainerInterface
);

$dispatcher->run();
```

The `run()` method starts the application lifecycle.

### Using the controller

In contradiction to other frameworks eArc uses closures and not classes as
controllers. (A controller controls flow not state and is therefore nearer
to the functional programming paradigm than the object oriented programming
paradigm.)

The locate controller interface references neither classes/objects nor
functions, it references paths to files. The referenced files must return a
closure.

```php
<?php

return function()
{
    // ...controller code goes here...    
};
```

The dispatcher injects the router and the dependency container in every
controller closure.

```php
<?php

use eArc\core\interfaces\LocateControllerInterface;
use Psr\Container\ContainerInterface;

return function(LocateControllerInterface $router, ContainerInterface $container)
{
    // ...controller code goes here...    
};
```

If you use the eArc framework the injected classes can be written more specific. 


```php
<?php

use eArc\router\Router;
use eArc\di\DependencyContainer;

return function(Router $router, DependencyContainer $dc)
{
    // ...controller code goes here...    
};
```

## Advanced Usage

### The application lifecycle

The dispatching process has 5 phases:
1. Execution of the middleware registered to dispatch start.
2. Execution of the access controllers 
3. Execution of the middleware registered to dispatch between.
4. Execution of the main controller
5. Execution of the middleware registered to dispatch end.

If one of the controllers returns a router object the dispatching process
starts all over again injecting the new router object. 

#### The access controllers

Access controllers can be used for anything by ignorant ingenious humans but
they were invented to simplify the handling of access.

First of all the router checks whether the route has any access permissions 
and hands the corresponding access handlers (a.k.a. access controllers) to the 
dispatcher.

If an access right is violated the corresponding access controller can return
an new router object configured with the login/registering route or an access
denied page. Or if the request was an ajax call send a access denied signal and
die.

Lets go on with the login/registering route. The new routing object is returned
to the dispatcher and the dispatching process starts all over again. Since the 
application now sees the router object corresponding to the login page the login
page is displayed and no access rights are violated anymore although the users
browser still displays the old URL.

Hint: If you handle the login/registering process in the access controller using
POST variables only you can leave the browsers URL intact.

If the login/registering process succeeds the access controller should return
the current router object to start the dispatching process (in the logged in
state) all over again. This ensures all access rights get checked properly.

The main controller can now do his work on the original page without the
slightest chance to mess it up. He isn't even aware that a login process has
happened.

The login page behaves like a popup although it is handled by the backend. It is
completely sealed against the original page. Even working with more than one
login layer is now as easy as drinking a cup of tea. 

Hint: Working with an request object boosts this approach even further. The
violated access controller can store it away and restore it on login/registering
success. Thus POST requests can be fully restored and enhance the user
experience.

To deepen the understanding of the power of the access controller concept 
reading the [eArc router manual](https://github.com/Koudela/eArc-router) might 
be a good idea.

#### The main controller

For each route there is only one main controller. He handles the business and
output domains corresponding to the route. The preparation and execution of the
call to their api should be all the main controller cares for. 

#### Middleware

To enhance the eArc framework middleware can be registered pre and post the
execution of the controllers. `registerDispatchStart()`, 
`registerDispatchBetween()` and `registerDispatchEnd()` are the corresponding
methods of the dispatcher object. They all take a closure as argument.

The closure gets the same objects injected as the controllers.
 
```php
<?php

use eArc\router\Router;
use eArc\di\DependencyContainer;

// some launching code or maybe the body of a class

$dispatcher->registerDispatchStart(function(Router $router, DependencyContainer $dc) {
    // ...middleware code or the call to the middleware api goes here... 
});

// some launching code or maybe the body of a class
```

##### Example

This example shows how to use middleware to transform the eArc framework into a
twig template route loader. Making it really simple to design some reusable
HTML-CSS-stuff to show to your customers in front of the app building process
saving some additional work.

Since the middleware needs some special dependency configuration to be done
parts of the launching code are also shown. 

```php
<?php

use \eArc\core\Dispatcher;
use \eArc\router\Router;
use \eArc\di\DependencyContainer;

// ...some other launching code goes here...

$routingBasePath = '/absolute/path/to/route/base/folder/';

$dc = new DependencyContainer();

$dc->set(Dispatcher::class, [Router::class, DependencyContainer::class]);

// usage of the inline factory instead of the array configuration approach
// otherwise if any of the arguments matches a container key we are in trouble 
$dc->set(Router::class, function() use ($routingBasePath) {
    return new Router(
        $routingBasePath,
        filter_input(INPUT_SERVER, 'REQUEST_METHOD'),
        filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL) ?? '/',
        ['GET' => ['index.twig.html'], 'POST' => ['index.twig.html']]
    );
});

$dc->set('twig', function() use ($routingBasePath) {

    $loader = new \Twig_Loader_Filesystem($routingBasePath);
    $twig = new \Twig_Environment($loader, array());
    
    return $twig;
});

/**
 * @var Dispatcher $dispatcher
 */
$dispatcher = $dc->get(Dispatcher::class);

$dispatcher->registerDispatchStart(function(Router $router, \Psr\Container\ContainerInterface $container) {
    if ($router->cntVirtualArgs() > 0) {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        exit;
    }
    echo $container->get('twig')->render(implode(DIRECTORY_SEPARATOR, $router->getRealArgs()) . DIRECTORY_SEPARATOR . 'index.twig.html', []);
    exit;
});

$dispatcher->run();
```
