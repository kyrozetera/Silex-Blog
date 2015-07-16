<?php
namespace Blog\Controller;

use Silex\Application;

/**
 * base controller template
 * 
 * Any controller that extends BaseController must implement init()
 */
abstract class BaseController implements \Silex\ControllerProviderInterface
{
    protected $app;
    protected $controllerCollection;
    public function connect(Application $app)
    {
        $this->app = $app;
        $this->controllerCollection = $this->app['controllers_factory'];
        $this->init();
        return $this->controllerCollection;
    }
    /**
     * called from connect($app) API function
     */
    abstract function init();
}
