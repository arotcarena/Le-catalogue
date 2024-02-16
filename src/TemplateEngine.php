<?php
namespace Vico;

use Vico\Config;
use Vico\Router;
use Vico\UrlHelper;

class TemplateEngine
{
    /**
     * @var string 
     */
    private $templates_directory;
    
    /**
     * @var string 
     */
    private $layouts_directory;

    /**
     * @var string $template (le chemin du fichier à partir du dossier template)
     */
    private $template;


    /**
     * @var array $params (les variables qu'on passe à la vue)
     */
    private $params = [];

    public function __construct(string $template, ?array $params = [])
    {
        $this->template = $template;
        $this->params = $params;
        $this->templates_directory = Config::templates_directory();
        $this->layouts_directory = Config::layouts_directory();
    }

    /**
     * Renvoie la vue complète : layout et template
     */
    public function render():string
    {
        foreach($this->params as $var_name => $value)
        {
            $$var_name = $value;
        }
        //pour aller plus vite dans les vues
        $url_helper = $helper->getUrlHelper();
        $nav = $helper->getNav($url_helper);
        $router = $helper->router;

        ob_start();
        require $this->templates_directory . $this->template;
        $body = ob_get_clean();
        ob_start();
        require $this->layouts_directory . $this->getLayout($url_helper) . '.php';
        return ob_get_clean();
    }

    public function addParams(array $addParams):self
    {
        $this->params = array_merge($this->params, $addParams);

        return $this;
    }

    private function getLayout(UrlHelper $url_helper):string 
    {
        foreach(Config::LAYOUTS as $url => $layout)
        {
            if($url_helper->match($_SERVER['REQUEST_URI'], $url))
            {
                return $layout;
            }
        }
        return Config::DEFAULT_LAYOUT;
    }
}