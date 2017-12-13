<?php
namespace App\Controller;

use App\Model\AdulteModel;
use Silex\Application;

use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class AdulteController implements ControllerProviderInterface{

    private $adulteModel;

    public function validFormAdd(Application $app){
        if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['nomVille'])
            && isset($_POST['adresse']) && isset($_POST['codePostal']) && isset($_POST['telephone'])
            && isset($_POST['adresseMail']) && isset($_POST['password'])) {

            $donnees = $this->getData($_POST);
            $erreurs = $this->erreurs($donnees);
            if(! empty($erreurs))
            {
                $parents = (new AdulteModel($app))->getAllAdultes();
                return $app["twig"]->render('famille/adulte/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'adulte'=>$parents]);
            }
            else
            {

                $idParent = (new AdulteModel($app))->addAdulte($donnees);

                return $app["twig"]->render('login.html.twig', ['inscription_confirm'=>true]);
            }
        }
        else {
            return $_POST;
        }
    }

    public function add(Application $app) {
        return $app["twig"]->render('famille/adulte/add.html.twig');
    }

    public function home(Application $app){
        return $app["twig"]->render('famille/accueil.html.twig');
    }

    public function addResp(Application $app, $id){
        $idParent = (new AdulteModel($app))->getAdulteIdBySession($app['session']->get('username'));
        (new AdulteModel($app))->addAdulteResp($id,$idParent);
        return $app["twig"]->render('layout.html.twig');
    }

    private function getData($post){
        if (isset($post['idAdulte'])){
            $data['idAdulte'] = htmlentities($post['idAdulte']);
        }
        $data['nom']=htmlentities($post['nom']);
        $data['prenom']=htmlentities($post['prenom']);
        $data['nomVille']=htmlentities($post['nomVille']);
        $data['codePostal']=htmlentities($post['codePostal']);
        $data['adresse']=htmlentities($post['adresse']);
        $data['telephone']=htmlentities($post['telephone']);
        $data['adresseMail']=htmlentities($post['adresseMail']);
        $data['codePostal']=$post['codePostal'];
        $data['password']=$post['password'];
        return $data;
    }

    private function erreurs($donnees){
        $erreurs = [];
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['prenom']))) $erreurs['prenom']='prenom composé de 2 lettres minimum';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nomVille']))) $erreurs['nomVille']='ville composé de 2 lettres minimum';
        if ((! preg_match("/^[0-9]{5}/",$donnees['codePostal']))) $erreurs['codePostal']='le code postal doit être composé de 5 caractères numériques';
        if ((! preg_match("/^[\w.-]+@[\w.-]+\.[a-z]{2,6}$/",$donnees['adresseMail']))) $erreurs['adresseMail']='veuillez entrer une adresse mail valide';
        if ((! preg_match("/^[0-9]{10}/",$donnees['telephone']))) $erreurs['telephone']='veuillez entrer un numéro de téléphone valide';

        return $erreurs;
    }

    public function index(Application $app) {
        return $app["twig"]->render('accueil.html.twig');
    }

    public function show(Application $app) {
        $adultes = (new AdulteModel($app))->getAllAdultes();
        return $app["twig"]->render('famille/adulte/show.html.twig',compact('adultes'));
    }

    public function edit(Application $app, $id) {
        $donnees = (new AdulteModel($app))->getAdulte($id);
        $this->destroy($app,$id);
        return $app["twig"]->render('famille/adulte/update.html.twig',compact('donnees'));
    }

    public function delete(Application $app, $id) {
        $adulte = (new AdulteModel($app))->getAdulte($id);
        return $app["twig"]->render('famille/adulte/suppression.html.twig', compact('adulte'));
    }

    public function destroy(Application $app, $id){
        (new AdulteModel($app))->deleteAdulte($id);
        return $this->show($app);
    }

    public function connexionAdulte(Application $app)
    {
        $this->deconnexionSession($app);
        return $app["twig"]->render('login.html.twig');
    }

    public function validFormConnexionAdulte(Application $app, Request $req) {

        $login=$app->escape($req->get('username'));
        $pw=$app->escape($req->get('password'));

        $this->adulteModel = new AdulteModel($app);
        $data = $this->adulteModel->loginCheckAdulte($login,$pw);
        if($data != NULL) {
                $app['session']->set('role', $data['role']);
                $app['session']->set('username', $data['username']);
                $app['session']->set('idAdulte', $data['idAdulte']);
                $app['session']->set('logged', 1);
                if ($app['session']->get('role') == 'ROLE_ADMIN') {
                    return $app->redirect($app["url_generator"]->generate("adulte.show"));
                }
                return $app->redirect($app["url_generator"]->generate("enfant.show"));
        }
        $app['session']->set('erreur', 'mot de passe ou login incorrect');
        return $app["twig"]->render('login.html.twig');
    }

    public function deconnexionSession(Application $app)
    {
        $app['session']->clear();
        $app['session']->set('logged', 0);
        $app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');

        return $app->redirect($app["url_generator"]->generate("adulte.show"));
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/', 'App\Controller\AdulteController::index')->bind('adulte.index');
        $controllers->get('/login', 'App\Controller\AdulteController::connexionAdulte')->bind('adulte.login');

        $controllers->post('/login', 'App\Controller\AdulteController::validFormConnexionAdulte')->bind('adulte.validFormlogin');
        $controllers->get('/logout', 'App\Controller\AdulteController::deconnexionSession')->bind('adulte.logout');

        $controllers->get('/errorAdulte', 'App\Controller\AdulteController::errorDroit')->bind('adulte.erreurs');

        $controllers->get('/showAdulte', 'App\Controller\AdulteController::show')->bind('adulte.show');

        $controllers->get('/addAdulte', 'App\Controller\AdulteController::add')->bind('adulte.add');
        $controllers->get('/addResp/{id}', 'App\Controller\AdulteController::addResp')->bind('adulte.addResp');
        $controllers->post('/addAdulte', 'App\Controller\AdulteController::validFormAdd')->bind('adulte.validFormAdd');

        $controllers->get('/deleteAdulte/{id}', 'App\Controller\AdulteController::delete')->bind('admin.delete');
        $controllers->delete('/deleteAdulte/{id}', 'App\Controller\AdulteController::destroy')->bind('admin.destroy');

        $controllers->get('/editAdulte/{id}', 'App\Controller\AdulteController::edit')->bind('admin.edit');
        $controllers->put('/editAdulte/{id}', 'App\Controller\AdulteController::validFormUpdate')->bind('adulte.validFormUpdate');
        return $controllers;
    }

}