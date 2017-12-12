<?php

$app->mount("/enfant", new App\Controller\EnfantController($app));
$app->mount("/", new App\Controller\AdulteController($app));
$app->mount("/activite", new App\Controller\ActiviteController($app));
$app->mount("/agenda", new App\Controller\AgendaController($app));