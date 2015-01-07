<?php
/**
 * Override of \Slim\Slim to add the ability to dynamically create a controller
 * and call an action method on it.
 *
 * Copyright 2014-2015 Rob Allen (rob@akrabat.com).
 * License: New-BSD
 */
namespace RKA;

class Slim extends \Slim\Slim
{
    /**
     * Add a route as per the parent method, additionally supporting the syntax
     * "{controller class name}:{action method name}" as the last argument which
     * will be converted to a closure that instantiates the controller (or gets
     * from container) and then calls the method on it.
     *
     * @inheritdoc
     *
     * @param   array (See notes above)
     * @return  \Slim\Route
     */
    public function mapRoute($args)
    {
        $callable = array_pop($args);
        if (is_string($callable) && substr_count($callable, ':', 1) == 1) {
            $callable = $this->createControllerClosure($callable);
        }
        $args[] = $callable;

        return parent::mapRoute($args);
    }

    /**
     * Create a closure that instantiates (or gets from container) and then calls
     * the action method.
     *
     * Also if the methods exist on the controller class, call setApp(), setRequest()
     * and setResponse() passing in the appropriate object.
     *
     * @param  string $name controller class name and action method name separated by a colon
     * @return closure
     */
    protected function createControllerClosure($name)
    {
        list($controllerName, $actionName) = explode(':', $name);

        // Create a callable that will find or create the controller instance
        // and then execute the action
        $app = $this;
        $callable = function () use ($app, $controllerName, $actionName) {

            // Try to fetch the controller instance from Slim's container
            if ($app->container->has($controllerName)) {
                $controller = $app->container->get($controllerName);
            } else {
                // not in container, assume it can be directly instantiated
                $controller = new $controllerName($app);
            }

            // Set the app, request and response into the controller if we can
            if (method_exists($controller, 'setApp')) {
                $controller->setApp($app);
            }
            if (method_exists($controller, 'setRequest')) {
                $controller->setRequest($app->request);
            }
            if (method_exists($controller, 'setResponse')) {
                $controller->setResponse($app->response);
            }

            // Call init in case the controller wants to do something now that
            // it has an app, request and response.
            if (method_exists($controller, 'init')) {
                $controller->init();
            }

            return call_user_func_array(array($controller, $actionName), func_get_args());
        };

        return $callable;
    }
}
