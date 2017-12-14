<?php
namespace App\Controller;

use App\Model\AdulteModel;
use App\Model\EnfantModel;
use App\Model\AgendaModel ;
use DateTime;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use function Sodium\add;

class EnfantController implements ControllerProviderInterface {

    private $enfantModel;

    public function validFormAdd(Application $app) {

        if (isset($_POST['nomEnfant']) && isset($_POST['prenomEnfant']) && isset($_POST['dateDeNaissance']) && isset($_POST['nomClasse'])) {
            $donnees = $this->getData($_POST);
            $erreurs = $this->erreurs($donnees);
            if(! empty($erreurs))
            {
                return $app["twig"]->render('famille/enfant/add.html.twig',['idParent'=>'idParent','donnees'=>$donnees,'erreurs'=>$erreurs]);
            }
            else
            {
                $enfantmodel= new EnfantModel($app);
                $idParent = (new AdulteModel($app))->getAdulteIdBySession($app['session']->get('username'));

                $enfantmodel->addEnfant($donnees,$idParent);
                return $app->redirect($app["url_generator"]->generate("enfant.show"));
            }

        }
        else
            return $_POST;
    }

    public function validFormUpdate(Application $app){
        $donnees = $this->getData($_POST);
        $enfants = (new EnfantModel($app))->getAllEnfants();

        $erreurs = $this->erreurs($donnees);

        if(! empty($erreurs)) {
            return $app["twig"]->render('famille/enfant/update.html.twig',array('donnees'=>$donnees,'erreurs'=>$erreurs,'enfants'=>$enfants));
        }
        else
        {
            (new EnfantModel($app))->updateEnfant($donnees['idEnfant'], $donnees);
            return $app->redirect($app["url_generator"]->generate("enfant.show"));
        }
    }
    public function show(Application $app)
    {
        $enfants = (new EnfantModel($app))->getEnfantOfParent($app['session']->get('idAdulte'));
        return $app["twig"]->render('famille/enfant/show.html.twig', compact('enfants'));
    }

    public function showEnfants(Application $app)
    {
        $enfants = (new EnfantModel($app))->getEnfantOfParent($app['session']->get('idAdulte'));
        return $app["twig"]->render('famille/enfant/show.html.twig', compact('enfants'));
    }

    public function add(Application $app) {
            $username = $app['session']->get('username');
            $idParent = (new AdulteModel($app))->getAdulteIdBySession($username);

        return $app["twig"]->render('famille/enfant/add.html.twig',compact('idParent') );
    }

    public function edit(Application $app, $id) {
        $donnees = (new EnfantModel($app))->getEnfant($id);
        return $app["twig"]->render('famille/enfant/edit.html.twig',compact('donnees'));
    }

    public function delete(Application $app, $id) {
        $enfant = (new EnfantModel($app))->deleteEnfant($id);
        return $app["twig"]->render('famille/enfant/suppression.html.twig', compact('enfant'));
    }

    public function destroy(Application $app, $id){
        (new EnfantModel($app))->deleteEnfant($id);
        return $this->show($app);
    }

    private function getData($post){
        if (isset($post['idEnfant'])){
            $data['idEnfant'] = htmlentities($post['idEnfant']);
        }
        $data['nomEnfant']=htmlentities($post['nomEnfant']);
        $data['prenomEnfant']=htmlentities($post['prenomEnfant']);
        $data['dateDeNaissance']=htmlentities($post['dateDeNaissance']);
        $data['nomClasse']=htmlentities($post['nomClasse']);
        $data['nomNiveau']=htmlentities($post['nomNiveau']);

        return $data;
    }

    private function erreurs($donnees){
        $erreurs = [];
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nomEnfant']))) $erreurs['nomEnfant']='nom composé de 2 lettres minimum';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['prenomEnfant']))) $erreurs['prenomEnfant']='prenom composé de 2 lettres minimum';
        if(! DateTime::createFromFormat('d-m-Y', $donnees['dateDeNaissance']))
        {
            $erreurs['dateDeNaissance']='saisir une date valide -> jj-mm-aaaa';
        }
        return $erreurs;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/errorEnfant', 'App\Controller\EnfantController::errorDroit')->bind('enfant.erreurs');

        $controllers->get('/showEnfant', 'App\Controller\EnfantController::show')->bind('enfant.show');
        $controllers->get('/showEnfants', 'App\Controller\EnfantController::showEnfants')->bind('enfant.showEnfants');

        $controllers->get('/addEnfant/{idParent}', 'App\Controller\EnfantController::add')->bind('enfant.add');
        $controllers->post('/addEnfant', 'App\Controller\EnfantController::validFormAdd')->bind('enfant.validFormAdd');

        $controllers->get('/deleteEnfant/{id}', 'App\Controller\EnfantController::delete')->bind('admin.deleteEnfant');
        $controllers->delete('/deleteEnfant/{id}', 'App\Controller\EnfantController::destroy')->bind('enfant.destroyEnfant');

        $controllers->get('/editEnfant/{id}', 'App\Controller\EnfantController::edit')->bind('enfant.editEnfant');
        $controllers->put('/editEnfant/{id}', 'App\Controller\EnfantController::validFormEdit')->bind('enfant.validFormEdit');
        return $controllers;
    }
}
