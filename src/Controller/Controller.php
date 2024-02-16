<?php 
namespace Vico\Controller;

use Vico\Helper;
use Vico\Router;
use Vico\Response;
use Vico\Redirection;
use Vico\TemplateEngine;

Abstract Class Controller
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Router
     */
    protected $router;


    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
        $this->router = $helper->router;
    }

    protected function render(string $template, ?array $params = []):Response
    {
        $templateEngine = new TemplateEngine($template, array_merge($params, ['helper' => $this->helper]));
        return new Response($templateEngine->render());
    }


    protected function redirect(string $url, ?string $http_response_code = null):Response
    {
        return (new Response())
                    ->setHeader('Location: '.$url)
                    ->setResponse_code($http_response_code);
    }
}