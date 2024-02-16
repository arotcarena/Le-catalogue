<?php
namespace Vico;

use Vico\Nav;
use Vico\UrlHelper;
use Vico\Managers\Manager;
use Vico\Controller\Controller;

class Helper
{
    /**
     * @var array [['manager_name' => Manager], [], ...]
     */
    private $managers = [];

    /**
     * @var array [['controller_name' => Controller], [], ...]
     */
    private $controllers = [];

    /**
     * @var array [['class_name' => Instance], [], ...]
     */
    private $instances = [];

    /**
     * @var Router
     */
    public $router;

    
    public function __construct(Router $router = null)
    {
        $this->router = $router;
    }

    public function getController(string $name):Controller
    {
        $controller = 'Vico\Controller\\'.$name;
        return new $controller($this);

        /* problème ci-dessous on appelle des fonction sans toujours passer par le constructeur du controleur et donc on rate Auth::check ainsi que les instanciation des managers
        DONC A NE PAS FAIRE
        if(!isset($this->controllers[$name]))
        {
            $controller = 'Vico\Controller\\'.$name;
            $this->controllers[$name] = new $controller($this);
        }
        return $this->controllers[$name];
        */
    }
    public function getManager(string $name):Manager
    {
        if(!isset($this->managers[$name]))
        {
            $manager = 'Vico\Managers\\'.$name;
            $this->managers[$name] = new $manager($this);
        }
        return $this->managers[$name];
    }
    

    /**
     * permet de récupérer n'importe quelle classe
     * @param string $class_name (avec le namespace spécifique : ex. Models\User)
     * @return Object de la classe demandée
     */
    public function get(string $class_name):Object
    {
        if(!isset($this->instances[$class_name]))
        {
            $class = 'Vico\\'.$class_name;
            $this->instances[$class_name] = new $class();
        }
        return $this->instances[$class_name];
    }

    /**
     * @param UrlHelper $url_helper
     * @return Nav
     */
    public function getNav($url_helper)
    {
        if(!isset($this->instances['Nav']))
        {
            $this->instances['Nav'] = new Nav($url_helper);
        }
        return $this->instances['Nav'];
    }

    /**
     * @return UrlHelper
     */
    public function getUrlHelper()
    {
        if(!isset($this->instances['UrlHelper']))
        {
            $this->instances['UrlHelper'] = new UrlHelper();
        }
        return $this->instances['UrlHelper'];
    }

}