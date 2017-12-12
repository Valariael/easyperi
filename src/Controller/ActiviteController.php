<?php
/**
 * Created by PhpStorm.
 * User: LaKriss
 * Date: 25/11/2017
 * Time: 12:29
 */

namespace App\Controller;
use App\Model\ActiviteModel;
use Silex\Application;

use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class ActiviteController implements ControllerProviderInterface
{
    private $ActiviteModel;

    public function validFormAdd(Application $app){
        if (isset($_POST['nomActivite']) && isset($_POST['descriptionActivite'])) {

            $donnees = $this->getData($_POST);
            $erreurs = $this->erreurs($donnees);
            if(! empty($erreurs))
            {
                $parents = (new ActiviteModel($app))->getAllActivites();
                return $app["twig"]->render('famille/activite/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'Activite'=>$parents]);
            }
            else
            {
                $idActivite = (new ActiviteModel($app))->addActivite($donnees);


                return $app->redirect($app["url_generator"]->generate("activite.add",compact('idActivite')));
            }
        }
        else {
            return $_POST;
        }
    }

    public function add(Application $app) {
        return $app["twig"]->render('famille/activite/add.html.twig');
    }

    private function getData($post){
        if (isset($post['idActivite'])){
            $data['idActivite'] = htmlentities($post['idActivite']);
        }
        $data['nomActivite']=htmlentities($post['nomActivite']);
        $data['descriptionActivite']=htmlentities($post['descriptionActivite']);
        if(isset($post['idTheme']))
            $data['idTheme']=htmlentities($post['idTheme']);
        return $data;
    }

    private function erreurs($donnees){
        $erreurs = [];
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nomActivite']))) $erreurs['nomActivite']='nomActivite composé de 2 lettres minimum';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['descriptionActivite']))) $erreurs['descriptionActivite']='descriptionActivite composé de 2 lettres minimum';
        return $erreurs;
    }

    public function show(Application $app) {
        $activite= (new ActiviteModel($app))->getAllActivites();
        return $app["twig"]->render('famille/activite/show.html.twig',compact('activite'));
    }

    public function edit(Application $app, $id) {
        $donnees = (new ActiviteModel($app))->getActivite($id);
        $this->destroy($app,$id);
        return $app["twig"]->render('famille/activite/update.html.twig',compact('donnees'));
    }

    public function delete(Application $app, $id) {
        $activite = (new ActiviteModel($app))->getActivite($id);
        return $app["twig"]->render('famille/activite/suppression.html.twig', compact('activite'));
    }

    public function destroy(Application $app, $id){
        (new ActiviteModel($app))->deleteActivite($id);
        return $this->show($app);
    }







    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];



        $controllers->get('/errorActivite', 'App\Controller\ActiviteController::errorDroit')->bind('activite.erreurs');

        $controllers->get('/showActivite', 'App\Controller\ActiviteController::show')->bind('activite.show');

        $controllers->get('/addActivite', 'App\Controller\ActiviteController::add')->bind('activite.add');
        $controllers->post('/addActivite', 'App\Controller\ActiviteController::validFormAdd')->bind('activite.validFormAdd');

        $controllers->get('/deleteActivite/{id}', 'App\Controller\ActiviteController::delete')->bind('admin.delete');
        $controllers->delete('/deleteActivite/{id}', 'App\Controller\ActiviteController::destroy')->bind('admin.destroy');

        $controllers->get('/editActivite/{id}', 'App\Controller\ActiviteController::edit')->bind('admin.edit');
        $controllers->put('/editActivite/{id}', 'App\Controller\ActiviteController::validFormUpdate')->bind('activite.validFormUpdate');
        return $controllers;
    }


}