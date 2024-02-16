<?php
//--dev temps de chargement de la page
$begin_microtime = microtime(true);


session_start();
use Vico\Auth;
use Vico\Config;
use Vico\Router;
require '../vendor/autoload.php';


//--dev vue des erreurs
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

/*
try  // A DECOMMENTER POUR PASSER EN PRODUCTION
{
*/
    $router = new Router();
    
    $router->auto_redirections(Config::AUTO_REDIRECTIONS)
            ->map_all(Config::ROUTES);
            
    
    if(!isset($_SESSION['id']))
    {
        Auth::auto_login();   //cookie se souvenir de moi
    }
    else
    {
        Auth::auto_logout();    //en cas d'inactivité ou compte désactivé
    }

    $router->run();
/*
}
catch(Exception $e)         //CAPTURE TOUTES LES ERREURS NON CAPTUREES POUR ETRE SUR DE RIEN LAISSER VOIR AU USER
{
    header('Location: '.$router->url('unknown_error'));
    exit();
}
*/


    //--dev temps de chargement de la page
?>
<div class="container mb-4">
    Page chargée en <strong><?= \number_format(((microtime(true) - $begin_microtime) * 1000), 1, ',', '') ?> ms</strong>
</div>