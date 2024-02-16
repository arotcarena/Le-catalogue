<?php
namespace Vico;

use Vico\UrlHelper;


class Nav
{
    /**
     * @var UrlHelper
     */
    private $url_helper;

    public function __construct(UrlHelper $url_helper)
    {
        $this->url_helper = $url_helper;
    }


    public function link(string $href, string $label, ?string $match = null, ?string $css = null):string
    {
        $css = $css ?: 'active';
        $class = 'nav-link';
        if($match !== null)
        {
            $class .= $this->url_helper->$match($_SERVER['REQUEST_URI'], $href) ? ' '.$css: '';
        }
        else
        {
            if($this->url_helper->match($_SERVER['REQUEST_URI'], '/admin'))
            {
                $class .= $this->url_helper->match_2($_SERVER['REQUEST_URI'], $href) ? ' '.$css: '';
            }
            else
            {
                $class .= $this->url_helper->match($_SERVER['REQUEST_URI'], $href) ? ' '.$css: '';
            }
        }
        
        
        return <<<HTML
            <a class="$class" aria-current="page" href="$href">$label</a>
            HTML;

    }
}