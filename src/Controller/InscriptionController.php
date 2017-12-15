<?php
/**
 * Created by PhpStorm.
 * User: tlacaill
 * Date: 15/12/17
 * Time: 10:58
 */

namespace App\Controller;


use App\Model\InscriptionModel;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class InscriptionController implements ControllerProviderInterface
{

    public function showInscriptions(Application $app, $id)
    {
        $donnees = (new InscriptionModel($app))->getEnfantsByIdAgenda($id);
        return $app["twig"]->render('inscription/show.html.twig',compact('donnees'));
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/showInscriptions/{id}', 'App\Controller\InscriptionController::showInscriptions')->bind('inscription.showInscriptions');
        $controllers->match('/showInscriptions/{id}', 'App\Controller\InscriptionController::showInscriptions')->bind('inscription.showInscriptions');
        return $controllers;
    }
}