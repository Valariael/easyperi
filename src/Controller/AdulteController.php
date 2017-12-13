<?php
namespace App\Controller;

use App\Model\AdulteModel;
use Silex\Application;

use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class AdulteController implements ControllerProviderInterface{

    private $adulteModel;

    public function validFormAdd(Application $app){
        if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['ville'])
            && isset($_POST['adresse']) && isset($_POST['code_postal']) && isset($_POST['telephone'])
            && isset($_POST['adresseMail']) && isset($_POST['password'])) {

            $donnees = $this->getData($_POST);
            $erreurs = $this->erreurs($app, $donnees);
            if(! empty($erreurs))
            {
                $parents = (new AdulteModel($app))->getAllAdultes();
                return $app["twig"]->render('famille/adulte/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'adulte'=>$parents]);
            }
            else
            {

                $idParent = (new AdulteModel($app))->addAdulte($donnees);

                return $app["twig"]->render('login.html.twig', ['inscription_confirm'=>true, 'login'=>$donnees['nom'].".".$donnees['prenom']]);
            }
        }
        else {
            return $_POST;
        }
    }

    public function add(Application $app) {
        return $app["twig"]->render('famille/adulte/add.html.twig');
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
        $data['ville']=htmlentities($post['ville']);
        $data['code_postal']=htmlentities($post['code_postal']);
        $data['adresse']=htmlentities($post['adresse']);
        $data['telephone']=htmlentities($post['telephone']);
        $data['adresseMail']=htmlentities($post['adresseMail']);
        $data['password']=htmlentities($post['password']);
        return $data;
    }

    private function erreurs($app, $donnees){
        $this->adulteModel = new AdulteModel($app);
        $erreurs = [];
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
        if (!$this->adulteModel->isUserNameAvailable($donnees['nom'].".".$donnees['prenom'])) $erreurs['nom'] = 'Il semblerait qu\'un compte avec ce nom et prénom soit déjà existant' ;
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['prenom']))) $erreurs['prenom']='prenom composé de 2 lettres minimum';
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']='ville composé de 2 lettres minimum';
        if ((! preg_match("/^[0-9]{5}/",$donnees['code_postal']))) $erreurs['code_postal']='le code postal doit être composé de 5 caractères numériques';
        if ((! preg_match("/^[\w.-]+@[\w.-]+\.[a-z]{2,6}$/",$donnees['adresseMail']))) $erreurs['adresseMail']='veuillez entrer une adresse mail valide';
        if ((! preg_match("/^[0-9]{10}/",$donnees['telephone']))) $erreurs['telephone']='veuillez entrer un numéro de téléphone valide';

        return $erreurs;
    }

    public function index(Application $app) {
        return $this->connexionAdulte($app);
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

    #______________ CONNEXION ____________________________

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
        $app['session']->set('erreur', 'Mot de passe ou login incorrect');
        return $app["twig"]->render('login.html.twig', ['login'=>$login]);
    }

    public function deconnexionSession(Application $app)
    {
        $app['session']->clear();
        $app['session']->set('logged', 0);
        $app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');

        return $app->redirect($app["url_generator"]->generate("adulte.show"));
    }

    #______________ INFOS ____________________________

    public function showInfos(Application $app) {
        $this->adulteModel = new AdulteModel($app);
        $idAdulte = $app['session']->get('idAdulte');
        $donnees = $this->adulteModel->getAdulte($idAdulte);
        $donnees['telephone'] = chunk_split($donnees['telephone'], 2, ' ');
        return $app["twig"]->render('famille/adulte/showInfos.html.twig', ['donnees'=>$donnees]);
    }

    public function editInfos(Application $app) {
        $idAdulte = $app['session']->get('idAdulte');
        $this->adulteModel = new AdulteModel($app);
        $donnees = $this->adulteModel->getAdulte($idAdulte);
        return $app["twig"]->render('famille/adulte/editInfos.html.twig',['donnees'=>$donnees]);
    }

    public function testeDonneesModifieesAdulte($app, $donnees, $current_username) {
        $this->adulteModel = new AdulteModel($app);
        $erreurs = [];
        if ((! preg_match("/[A-Za-z0-9.]{5,}/",$donnees['username']))) $erreurs['username']= 'Nom d\'utilisateur composé de 5 caractères minimum';

        if ($donnees['username'] != $current_username) {
            if (!$this->adulteModel->isUserNameAvailable($donnees['username'])) $erreurs['username'] = 'Nom d\'utilisateur déjà pris';
        }
        if (!filter_var($donnees['adresseMail'], FILTER_VALIDATE_EMAIL)) { $erreurs['adresseMail']= 'Adresse mail incorrecte'; }
        if ((! preg_match("/[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']= 'Ville composée de deux lettres minimum';
        if ((! preg_match("/[A-Za-z0-9 ]{2,}/",$donnees['adresse']))) $erreurs['adresse']= 'Adresse composée de deux caractères minimum';
        if ((! preg_match("/[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']= 'Nom composé de deux lettres minimum';
        if ((! preg_match("/[A-Za-z ]{2,}/",$donnees['prenom']))) $erreurs['prenom']= 'Prénom composé de lettres minimum';
        if ((!preg_match("/^[0-9]{5}$/", $donnees['code_postal']))) $erreurs['code_postal']= 'Code postal composé de 5 chiffres';
        if ((!preg_match("/^[0-9]{10}$/", $donnees['telephone']))) $erreurs['telephone']= 'Téléphone composé de 10 chiffres';

        return $erreurs;
    }

    public function validFormEditInfos(Application $app) {
        $this->adulteModel = new AdulteModel($app);
        unset($erreurs);
        $idAdulte = $app['session']->get('idAdulte');
        $username = $app['session']->get('username');
        $donnees = [
            'username' => htmlspecialchars($_POST['username']),
            'adresseMail' => htmlspecialchars($_POST['adresseMail']),
            'nom' => htmlspecialchars($_POST['nom']),
            'prenom' => htmlspecialchars($_POST['prenom']),
            'code_postal' => htmlspecialchars($_POST['code_postal']),
            'ville' => htmlspecialchars($_POST['ville']),
            'adresse' => htmlspecialchars($_POST['adresse']),
            'password' => htmlspecialchars($_POST['password']),
            'telephone' => htmlspecialchars($_POST['telephone'])
        ];

        $erreurs = $this->testeDonneesModifieesAdulte($app, $donnees, $username);
        $password_wrong = !$this->adulteModel->loginCheckAdulte($username, $donnees['password']);

        if (!empty($erreurs) or $password_wrong) {
            if ($password_wrong) $erreurs['password'] = 'Mot de passe incorrect';
            return $app["twig"]->render('famille/adulte/editInfos.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs]);
        }
        else
        {
            // changer app session username !
            $app['session']->set('username', $donnees['username']);
            $this->adulteModel->updateAdulte($idAdulte, $donnees);
            return $app->redirect($app["url_generator"]->generate("adulte.showInfos"));
        }

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

        $controllers->get('/infos', 'App\Controller\AdulteController::showInfos')->bind('adulte.showInfos');
        $controllers->get('/infos/edit', 'App\Controller\AdulteController::editInfos')->bind('adulte.editInfos');
        $controllers->post('/infos/edit', 'App\Controller\AdulteController::validFormEditInfos')->bind('adulte.validEditInfos');

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