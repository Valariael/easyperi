<?php
require "config.php";

//On initialise le timeZone
ini_set('date.timezone', 'Europe/Paris');

//On ajoute l'autoloader (compatible winwin)
$loader = require_once join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'vendor', 'autoload.php']);

//dans l'autoloader nous ajoutons notre répertoire applicatif
$loader->addPsr4('App\\',join(DIRECTORY_SEPARATOR,[dirname(__DIR__), 'src']));

//Nous instancions un objet Silex\Application
$app = new Silex\Application();

// connexion à la base de données
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbhost' => hostname,
        'host' => hostname,
        'dbname' => database,
        'user' => username,
        'password' => password,
        'charset'   => 'utf8mb4',
    ),
));

//utilisation de twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => join(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'src', 'View'])
));  // pour utiliser path dans twig
//$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
//$app->register(new RoutingServiceProvider());

// utilisation des sessoins
$app->register(new Silex\Provider\SessionServiceProvider());

//en dev, nous voulons voir les erreurs
$app['debug'] = true;

// rajoute la méthode asset dans twig

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.named_packages' => array(
        'css' => array(
            'version' => 'css2',
            'base_path' => __DIR__.'/../web/'
        ),
    ),
));


// par défaut les méthodes DELETE PUT ne sont pas prises en compte
use Symfony\Component\HttpFoundation\Request;
Request::enableHttpMethodParameterOverride();

// validator      => php composer.phar  require symfony/validator
// $app->register(new Silex\Provider\ValidatorServiceProvider());

//Inclusion des routes
include('route.php');

$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute=$request->get("_route");
    $nomRoute = explode('.', $nomRoute);
    $admin = $nomRoute[0];
    if ($app['session']->get('roles') != 'ROLE_ADMIN'  && $admin == "admin") {
        return $app->redirect($app["url_generator"]->generate("enfant.erreurs"));
    }

});

//On lance l'application
$app->run();