<?php
namespace Blog\Controller;

/**
 * root application controller
 */
class IndexController extends BaseController
{
    public function init()
    {
        $this->controllerCollection->get("/", array($this, 'index'))->bind('home');
    }

    public function index()
    {
        return $this->app['twig']->render('home.html.twig');
    }
}
