<?php
// Définition des constantes et inclusions indispensables
define("CONF_MODE", 'dev');
ini_set('display_errors', 'on');

use Pry\Util\Registry;

require_once "includes/Pry/Pry.php";

//Autoload du framework
Pry::register();

// Lecture fichier de config dans le mode souhaité
try {
    $configIni = new Pry\Config\Ini('includes/config/config.ini', CONF_MODE);
    Registry::set('Config', $configIni); // On sauvegarde l'objet de config
    define('ROOT_PATH', $configIni->root); // Définition de la racine de l'appli
} catch (Exception $e) {
    echo $e->getMessage();
}


//Réglage horaire
date_default_timezone_set('Europe/Paris');

//Template
$myView = new Pry\View\View();
$myView->setViewBase(ROOT_PATH.'includes/view/');
$myView->set('url', $configIni->url);

$router = Pry\Controller\Router::getInstance();
$router->setPath(ROOT_PATH . 'includes/controllers/');
$router->setView($myView);

Registry::set('router', $router);

$router->load();