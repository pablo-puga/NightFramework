Night Framework
==============

This is a PHP framework made as a practice in a university master.

**Author:** Pablo Puga Peralta <[pablo-puga on GitHub](https://github.com/pablo-puga "pablo-puga on GitHub")>

If you prefer to test a complete application instead of creating one, check out [Night-Standard-Edition](https://github.com/pablo-puga/Night-Standard-Edition "Night-Standard-Edition")
Installation
-------------

**Night** is installed via composer, so add to your composer.json the following code.

```json
"repositories": [
	{
		"type": "vcs",
	    "url": "https://github.com/pablo-puga/NightFramework"
    }
],
"require": {
	"php": "^5.6",
    "pablo-puga/night": "dev-master"
}
```

Framework Features
------------------------

**Night** provides a wide set of nice features to the user to ease the development of its application.

 - It provides an abstract **NightController** class to extend to give the option to your controllers to access the services container.
```php
$container = $this->getServicesContainer();
```
 - Two different templating engines, **Twig** and **Smarty**.
```php
$twigTemplating = $container->getService('twig-templating');
$smartyTemplating = $container->getService('smarty-templating');
```
 - It supports **prod** and **dev** environments.
 - A **Translator** service
```php
$translator = $container->getService('translator');
```
 - A **Profiler** panel to debug what is happening during the execution of your application.
```php
Profiler::enable();
``` 
 - Your controller can have access to the **Request** object that contains all information relative to the globals  \$_GET, \$_POST. \$_SERVER, \$_SESSION, \$_COOKIE and to the Route information. To gain access to it is enough to add to your controller method the param *Request $request*
```php
public function myControllerAction(Request $request) {
```
 - It provides two type of responses, **Response** and **JSONResponse**
	  **Note: ** Every action method **MUST** return one response, otherwise it will throw an exception.
 - The **PDORepository** is a simpple interface to access your database.
```php
$pdoRepository = $container->getService('pdo-repository');
```

Framework Requirements
------------------------

Your application must have at least the following directory structure.
```
├─ app/
│   └─ confs/
│         ├─ general.yml
│         ├─ routing.yml | routing.php | routing.json
│         ├─ services.yml (optional)
│         ├─ services_prod.yml (optional)
│         └─ services_dev.yml (optional)
└─ public/
       └─ main.php (Your front controller)
```

###general.yml sample
```yml
general:
  environment: prod
  routingFileExtension: yml
templating:
  twig:
    templatesDirectory: ../app/templates/twig
    cacheDirectory: false
    debug: true
  smarty:
    templates:
      directory: ../app/templates/smarty
    compilation:
      directory: cache/smarty/compilations
    cache:
      enable: false
      lifeTime: 300
      directory: cache/smarty
database:
  pdo:
    host: localhost
    database: world
    user: root
    password:
    charset: latin1
```
##Routing
> **NOTE 1:** your routing file **MUST** have a **notfound** entry to display when no route matches.
> **NOTE 2:** to select the file extension for your routing file you must select it in the general.yml file and you need to redefine the routing service setting the file parser corresponding to your selection.

**Default Routing Service**
```yml
routing:
  class: \Night\Component\Routing\Routing
  arguments:
    - @yaml-parser
```
> **Available Filer Parsers:** @yaml-parser | @json-parser | @php-parser

###routing.yml sample
```yml
routeWithoutArguments:
  route: /myroute
  path:
    classname: MyRouteWithoutArgumentsController
    callablemethod: myRouteAction
routeWithArguments:
  route: /myroute/{arg1}/{arg2}
  path:
    classname: MyRouteWithArgumentsController
    callablemethod: myRouteAction
notfound:
  route:
  path:
    classname: NotFoundController
    callablemethod: notFoundAction
```

###routing.json sample
```json
{
  "routeWithoutArguments": {
    "route": "/myroute",
    "path": {
      "classname": "MyRouteWithoutArgumentsController",
      "callablemethod": "myRouteAction"
    }
  },
  "routeWithArguments": {
    "route": "/myroute/{arg1}/{arg2}",
    "path": {
      "classname": "MyRouteWithArgumentsController",
      "callablemethod": "myRouteAction"
    }
  },
  "notfound": {
    "route": "",
    "path": {
      "classname": "NotFoundController",
      "callablemethod": "notFoundAction"
    }
  }
}
```
###routing.php sample
```php
<?php
return [
    'routeWithoutArguments' => [
        'route' => '/myroute',
        'path' => [
            'classname' => 'MyRouteWithoutArgumentsController',
            'callablemethod' => 'myRouteAction'
        ]
    ],
    'routeWithArguments' => [
        'route' => '/myroute/{arg1}/{arg2}',
        'path' => [
            'classname' => 'MyRouteWithArgumentsController',
            'callablemethod' => 'myRouteAction'
        ]
    ],
    'notfound' => [
        'route' => '',
        'path' => [
            'classname' => 'NotFoundController',
            'callablemethod' => 'notFoundAction'
        ]
    ]
];
```

###Sample of Front Controller
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Night\Component\Bootstrap\Bootstrap;
use Night\Component\Profiling\Profiler;
use Night\Component\Request\Request;

$bootstrap = new Bootstrap();
$request   = Request::newFromGlobals();

//Profiler::enable(); //Uncomment to enable the Profiler

/**@var $response \Night\Component\Response\Response */
$response = $bootstrap($request);

$response->send();
```

### Services Container

The container has access to a set of services already defined.
```yml
json-parser:
  class: \Night\Component\FileParser\JSONParser
symfony-yaml:
  class: \Symfony\Component\Yaml\Yaml
  public: false
yaml-parser:
  class: \Night\Component\FileParser\YAMLParser
  arguments:
    - @symfony-yaml
php-parser:
  class: \Night\Component\FileParser\PHPParser
smarty-templating:
  class: \Night\Component\Templating\SmartyTemplating
  arguments:
    - @yaml-parser
twig-templating:
  class: \Night\Component\Templating\TwigTemplating
  arguments:
    - @yaml-parser
pdo-repository:
  class: \Night\Component\Repository\PDORepository
  arguments:
    - @yaml-parser
profiler:
  class: \Night\Component\Profiling\Profiler
  singleton: getInstance
pdo-repository-profiler:
  class: \Night\Component\Profiling\PDORepositoryProfiler
  tags:
    - profiler-component
  singleton: getInstance
routing-profiler:
  class: \Night\Component\Profiling\RoutingProfiler
  tags:
    - profiler-component
  singleton: getInstance
templating-profiler:
  class: \Night\Component\Profiling\TemplatingProfiler
  tags:
    - profiler-component
  singleton: getInstance
translator:
  class: \Night\Component\i18n\Translator
  arguments:
      - @yaml-parser
```
>**Tip:** The default FileParser for the routing is yaml-parser but if you prefer to specify the routing in json or php you can do it by creating your own definition of the routing service and passing the parser that you prefer as argument. You will need to edit the general configuration file to specify the extension. 

If you want to define your own services, you can do it by creating the file services.yml (services_prod.yml and services_dev.yml for environment specific services) in the configurations folder following the same sintax as above. 
>**Note:** If you want to overwrite a service it is enough to add a new entry to your services.yml with the same name of the service to overwrite.

