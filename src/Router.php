<?php
namespace Vico;

use Vico\Helper;
use Vico\UrlHelper;
use Vico\Exceptions\ForbiddenException;

class Router
{
    /**
     * @var \AltoRouter
     */
    private $router;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var UrlHelper
     */
    private $url_helper;

    public function __construct()
    {
        $this->router = new \AltoRouter();
        $this->helper = new Helper($this);
        $this->url_helper = $this->helper->getUrlHelper();
    }
    public function map_all(array $routes):self
    {
        foreach($routes as $route)
        {
            $this->router->map($route[0], $route[1], $route[2], $route[3]);
        }
        return $this;
    }
    public function url(string $name, array $params = []):string 
    {
        return $this->router->generate($name, $params);
    }
    /**
     * @param array $redirections (Config::AUTO_REDIRECTIONS)
     */
    public function auto_redirections(array $redirections):self
    {
        foreach($redirections as $redirection)
        {
            if($_SERVER['REQUEST_URI'] === $redirection['current_url'])
            {
                http_response_code(303);
                header('Location: '.$redirection['redirection_url']);
                exit();
            }
        }
        return $this;
    }

    public function run():void
    {
        $response = $this->match();

        $response->send();
    }

   

    private function match()
    {
        $match = $this->router->match();
        
        if($match)
        { 
            $controller_name = explode('::', $match['target'])[0];
            $method_name = explode('::', $match['target'])[1];
            try
            {
                $controller = $this->helper->getController($controller_name);     //   cette ligne doit être dans le try car c lors de l'instanciation du controller que Auth::check est appelé si nécessaire
                return !empty($match['params']) ? $controller->$method_name($match['params']): $controller->$method_name();  
            }
            catch(ForbiddenException $e)
            {
                $get = $this->url_helper->explode_get($e->getUrl());
                $get['target'] = $this->url_helper->del_get($e->getUrl());      //pour éviter d'avoir "?target=mon-compte?ceci=cela&machin=truc...

                if(isset($_SESSION['id']))
                {
                    $target = $this->url_helper->modif_get($this->url('login_change'), $get);
                }
                else
                {
                    $target = $this->url_helper->modif_get($this->url('login'), $get);
                    $_SESSION['flash']['danger'] = $_SESSION['flash']['danger'] ?? 'Accès refusé';
                }
                return (new Response())->setHeader('Location: '.$target);
            }
            
        }
        else
        {
            $templateEngine = new TemplateEngine('errors/direction_error.php', ['helper' => $this->helper]);
            return (new Response($templateEngine->render()))->setResponse_code(404);
        }
    }

    

    

   

}