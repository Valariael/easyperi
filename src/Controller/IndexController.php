<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0


class IndexController implements ControllerProviderInterface
{
    public function index(Application $app)
    {
        return $app["twig"]->render("layout.html.twig");
    }

    public function erreurDroit(Application $app)
    {
        return $app["twig"]->render("erreurDroit.html.twig");
    }

    public function connect(Application $app)
    {
        $index = $app['controllers_factory'];
        $index->match("/erreurdroit", 'App\Controller\IndexController::erreurDroit')->bind('erreurDroit');
        $index->match("/", 'App\Controller\IndexController::index')->bind('home');

        return $index;
    }


}
