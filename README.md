php-simple-di
=============

What is it?
-----------

php-simple-di (Php Simple Dependency Injection) is a library that provides a minimalistic dependency container with the ability to provide singleton or prototype versions of the dependencies identifying them by a name.
php-simple-di provides a singleton class where you can register your dependencies, indentifying them by a name and then you can retrieve, creating instances only on demand (it does not instanciate dependencies that are not needed for a request execution).

Installation
------------

### Composer:

Adding this to your composer.json should be enough (not tested, sorry):
```javascript  
{
    "name": "mcustiel/php-simple-di",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mcustiel/php-simple-di"
        }
    ],
    "require": {
        "mcustiel/php-simple-di": "dev-master"
    }
}
```

Or just download the release and include it in your path.

How to use it?
--------------

### Registering
In your bootstrap file you must define all the possible dependencies that your classes might need.

```php
use Mcustiel\PhpSimpleDependencyInjection\DependencyContainer;

$dependencyContainer = DependencyContainer::getInstance();
// ...
$dbConfig = loadDbConfig();
$cacheConfig = loadCacheConfig();
$dependencyContainer->add('dbConnection', function() {
    return new DatabaseConnection($dbConfig);
});
$dependencyContainer->add('cache', function() {
    return new CacheManager($cacheConfig);
});
```

#### Getting dependencies 
Then you can retrieve instances by refering them through their identifier.

```php
$cacheManager = DependencyContainer::getInstance()->get('cache');
```

### Instances
php-simple-di creates "singleton" instances by default, this means everytime you request for a dependency it will return the same instance every time. If by any chance you need to change this behavior, you can define that every time you asks php-simple-di for a dependency it will return a new instance. This behavior is changed through a boolean parameter in **add** method.

#### Singleton behavior

```php
$dependencyContainer->add('dbConnection', function() {
    return new DatabaseConnection($dbConfig);
});
// or also you can make it explicit:
$dependencyContainer->add('cache', function() {
    return new CacheManager($cacheConfig);
}, true);

$instance1 = DependencyContainer::getInstance()->get('cache');
$instance2 = DependencyContainer::getInstance()->get('cache');
// $instance1 and $instance2 references the same object
```

#### Prototype behavior

```php
$dependencyContainer->add('dbConnection', function() {
    return new DatabaseConnection($dbConfig);
}, false);

$instance1 = DependencyContainer::getInstance()->get('cache');
$instance2 = DependencyContainer::getInstance()->get('cache');
// $instance1 and $instance2 references different objects
```

Notes
=====

There's a lot of discussion around Singleton pattern, mentioning it as an antipattern because it's hard to test. Anyway, php-simple-di provides the container as a singleton class to allow just a single instance to be part of the execution. You should think in good practices and avoid using this class through singleton, but define it in your bootstrap file and pass the container instance as a parameter to your application dispatcher and always pass it as a parameter (injecting it as a dependency).