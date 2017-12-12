<?php
namespace App\Controller;

use App\Model\AdulteModel;
use App\Model\EnfantModel;
use App\Model\AgendaModel;

use Silex\Application;

use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class AgendaController implements ControllerProviderInterface{

    private $agendaModel ;

    public function show(Application $app){
        $enfant = (new EnfantModel($app))->getEnfant($_GET['id']);
        $idMaxAgenda = intval((new AgendaModel($app))->getMaxId());
        $agenda = array() ;
        for($i = 1; $i < 8 ; $i++) {
            $agenda[$i] = (new AgendaModel($app))->getAgenda($i);
        }
        return $app["twig"]->render('agenda/showAgenda2.html.twig',['agenda'=>$agenda, 'idEnfant'=>$enfant['idEnfant']]);

    }

    public function validAddInscription(Application $app){
        $agendaModel = new AgendaModel($app);
        $idEnfant = $_POST['idEnfant'];
        unset($_POST['idEnfant']);
        foreach ($_POST as $idAgenda)  {
            $agendaModel->addInscription($idEnfant, $idAgenda);
        }
        return $app->redirect($app["url_generator"]->generate("enfant.show"));
    }


    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];


        $controllers->get('/showAgenda2', 'App\Controller\AgendaController::show')->bind('agenda.showAgenda');

        $controllers->get('/ajouterInscription', 'App\Controller\AgendaController::ajouterInscription')->bind('agenda.ajouterInscription');
        $controllers->match('/ajouterInscription/{id}', 'App\Controller\AgendaController::ajouterInscription')->bind('agenda.ajouterInscription');
        $controllers->post('/ajouterInscrptionValid', 'App\Controller\AgendaController::validAddInscription')->bind('agenda.validAjouterInscription') ;
        return $controllers;
    }
}