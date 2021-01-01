# eArc core

Core component of the eArc framework. The eArc stands for **e**xplicit **arc**hitecture. 
It is about the urge to make code as easy to comprehend as possible and the
strive to touch the programmers freedom to code as little as possible. In short
it is about simplicity and good architecture.

The Core handles aspects all earc components have in common. Users of the framework
or some of its components will never need to install this package directly.

## Table of Contents
 
 - [Configuration](#configuration)
   - [Customizing](#customizing)
   - [Trouble shooting](#trouble-shooting)
 - [Public components](#public-components)
 - [Releases](#releases)
   - [Release 1.0](#release-10)
   - [Release 0.1](#release-01)

## Configuration

Place a file named `.earc-config.php` beneath the vendor dir. It's the configuration
file for all the earc components.

```php
<?php #.earc-config.php

return ['earc' => [
    'is_production_environment' => true
    //.. place here the parameters for the components
]];
```

Then put the following code in the bootstrap section of your framework or your
`index.php` file.

```php
use eArc\DI\DI;
use eArc\Core\Configuration;

DI::init();
Configuration::build();
```

That's it. You're ready to go.

### Customizing

If you want to put the configuration file somewhere else you can pass the filename
as parameter to the `build` method.

Hint: If you prefer the YAML format and do not use php constructs in your configuration,
you can use a yaml parser. 

### Trouble shooting

If you get an error

```html
PHP Fatal error:  Uncaught Error: Class 'eArc\DI\DI' not found
```

you most probably have not registered the composer autoloader yet. You can do this 
by requiring the composer autoload script in the vendor directory.

```php
use eArc\DI\DI;
use eArc\Core\Configuration;

require '/absolute/path/to/your/vendor'.'/autoload.php';
DI::init();
Configuration::build();
```

## Public components

All components can be used without the framework. Although some components depend
on each other.

 - (advanced) dependency injection: [earc/di](https://github.com/Koudela/eArc-di)
 - (lucid) event handling: [earc/event-tree](https://github.com/Koudela/eArc-eventTree)
 - (explicit) routing: [earc/router](https://github.com/Koudela/eArc-router)

## Releases

### Release 1.0

- complete rewrite

### Release 0.1

- the first official release
