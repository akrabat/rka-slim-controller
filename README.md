# RKA Slim Controller

An extension to [Slim Framework][1] that allows you use to dynamically
instantiated controllers with action methods wherever you would use a
closure when routing.

The controller can optionally be loaded from Slim's DI container,
allowing you to inject dependencies as required.

[1]: http://www.slimframework.com/

## Installation

    composer require akrabat/rka-slim-controller


## Usage

Use the string format `{controller class name}:{action method name}`
wherever you would usually use a closure:

e.g.

    $app = new \RKA\Slim();
    $app->get('/hello:name', 'App\IndexController:home');


You can also register the controller with Slim's DI container:

    $app = new \RKA\Slim();

    $app->container->singleton('App\IndexController', function ($container) {
        // Retrieve any required dependencies from the container and
        // inject into the constructor of the controller

        return new \App\IndexController();
    });

    $app->get('/', 'App\IndexController:index');


## Controller class methods

*RKA Slim Controller* will call the controller's `setApp()`, `setRequest()`
and `setResponse()` methods if they exist and populate appropriately. It will
then call the controller's `init()`` method.

Hence, a typical controller may look like:

    <?php
    namespace App;

    class IndexController
    {
        // Optional properties
        protected $app;
        protected $request;
        protected $response;

        public function index()
        {
            echo "This is the home page";
        }

        public function hello($name)
        {
            echo "Hello, $name";
        }

        // Optional setters
        public function setApp($app)
        {
            $this->app = $app;
        }

        public function setRequest($request)
        {
            $this->request = $request;
        }

        public function setResponse($response)
        {
            $this->response = $response;
        }

        // Init
        public function init()
        {
            // do things now that app, request and response are set.
        }
    }


## Example project

Look at [slim-di](https://github.com/akrabat/slim-di).
