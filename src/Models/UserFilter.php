<?php
namespace Vico\Models;

class UserFilter
{
    private $last_login_order;
    
    private $confirmed_at_order;
    
    private $last_login_min;
    
    private $per_page;

    public function __construct(array $data)
    {
        foreach($data as $key => $value)
        {
            $method = 'set'.ucfirst($key);
            if(method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
 
    public function getLast_login_order():?string
    {
        return $this->last_login_order;
    }

    public function setLast_login_order(?string $last_login_order):self
    {
        $this->last_login_order = $last_login_order;

        return $this;
    }

    public function getConfirmed_at_order():?string
    {
        return $this->confirmed_at_order;
    }

    public function setConfirmed_at_order(?string $confirmed_at_order)
    {
        $this->confirmed_at_order = $confirmed_at_order;

        return $this;
    }

    public function getLast_login_min():?string
    {
        return $this->last_login_min;
    }

    public function setLast_login_min(?string $last_login_min):self
    {
        $this->last_login_min = $last_login_min;

        return $this;
    }

    public function getPer_page():?int
    {
        return $this->per_page;
    }

    public function setPer_page(?int $per_page):self
    {
        $this->per_page = $per_page;

        return $this;
    }
}
