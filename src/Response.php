<?php
namespace Vico;

use Vico\Redirection;
use Vico\TemplateEngine;

class Response
{
    /**
     * @var string|null
     */
    private $view;


    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var int|null
     */
    private $response_code;

    /**
     * @param string|null $view
     */
    public function __construct($view = null)
    {
        $this->view = $view;
    }
    
    public function send():void 
    {
        if(!empty($this->view))
        {
            echo $this->view;
        }
        if(!empty($this->headers))
        {
            foreach($this->headers as $header)
            {
                header($header);
            }
        }
        if(!empty($this->response_code))
        {
            http_response_code($this->response_code);
        }
    }


    /**
     * Set the value of view
     *
     * @param  string|null  $view
     *
     * @return  self
     */ 
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set the value of header
     *
     * @param  string|null  $header
     *
     * @return  self
     */ 
    public function setHeader($header)
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Get the value of response_code
     *
     * @return  int|null
     */ 
    public function getResponse_code()
    {
        return $this->response_code;
    }

    /**
     * Set the value of response_code
     *
     * @param  int|null  $response_code
     *
     * @return  self
     */ 
    public function setResponse_code($response_code)
    {
        $this->response_code = $response_code;

        return $this;
    }
}