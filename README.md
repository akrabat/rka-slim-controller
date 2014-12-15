# RKA Slim Controller

An extension to [Slim][1] that allows you use to dynamically instantiated
controllers with action methods wherever you would use a closure when routing.

The controller can optionally be loaded from Slim's DI container, allowing you
to inject dependencies as required.


## Installation

    composer require akrabat/rka-slim-controller

[1]: http://www.slimframework.com/


## Usage

Use the string format `{controller class name}:{action method name}` wherever you
would usually use a closure:

e.g.

    $app = new \RkaSc\Slim();
    $app->get('/hello:name', 'App\Controller\IndexController:homeAction');


You can also register the controller with Slim's DI container:

    $app = new \RkaSc\Slim();

    $app->container->singleton('App\IndexController', function ($container) {
        // Retrieve any required dependencies from the container and
        // inject into the constructor of the controller

        return new \App\Controller\IndexController();
    });

    $app->get('/', 'App\IndexController:indexAction');


## Controller class methods

*RKA Slim Controller* will call the controller's `setApp()`, `setRequest()` and
`setResponse()` methods if they exist and populate appropriately.

Hence, a typical controller may look like:

    <?php
    namespace App\Controller;

    class IndexController
    {
        // Optional properties
        protected $app;
        protected $request;
        protected $response;

        public function indexAction()
        {
            echo "This is the home page";
        }

        public function helloAction($name)
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
    }


## Example project

Look at [slim-di](https://github.com/akrabat/slim-di).
