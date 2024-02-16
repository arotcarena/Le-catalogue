<?php
namespace Vico\Exceptions;

use Vico\UrlHelper;

class ForbiddenException extends \Exception
{
    private $url;

    public function __construct(?string $url = null)
    {
        $this->url = $url;
    }
    public function getUrl():string 
    {
        return $this->url;
    }
}