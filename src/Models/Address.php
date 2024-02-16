<?php
namespace Vico\Models;

class Address
{
    private $id;

    private $user_id;

    private $civility;

    private $last_name;

    private $first_name;

    private $number;

    private $way;

    private $city;

    private $postal_code;

    private $country;

    public function getId():?int 
    {
        return $this->id;
    }
    public function getUser_id():?int 
    {
        return $this->user_id;
    }
    public function getCivility():?string 
    {
        return $this->civility;
    }
    public function getLast_name():?string 
    {
        return $this->last_name;
    }
    public function getFirst_name():?string 
    {
        return $this->first_name;
    }
    public function getNumber():?string 
    {
        return $this->number;
    }
    public function getWay():?string 
    {
        return $this->way;
    }
    public function getCity():?string 
    {
        return $this->city;
    }
    public function getPostal_code():?int 
    {
        return $this->postal_code;
    }
    public function getCountry():?string 
    {
        return $this->country;
    }

    public function setId(?int $id):self 
    {
        $this->id = $id;
        return $this;
    }
    public function setUser_id(?int $user_id):self 
    {
        $this->user_id = $user_id;
        return $this;
    }
    public function setCivility(?string $civility):self
    {
        $this->civility = $civility;
        return $this;
    }
    public function setFirst_name(?string $first_name):self
    {
        $this->first_name = $first_name;
        return $this;
    }
    public function setLast_name(?string $last_name):self
    {
        $this->last_name = $last_name;
        return $this;
    }
    public function setNumber(?string $number):self 
    {
        $this->number = $number;
        return $this;
    }
    public function setWay(?string $way):self 
    {
        $this->way = $way;
        return $this;
    }
    public function setCity(?string $city):self 
    {
        $this->city = $city;
        return $this;
    }
    public function setPostal_code(?int $postal_code):self 
    {
        $this->postal_code = $postal_code;
        return $this;
    }
    public function setCountry(?string $country):self 
    {
        $this->country = $country;
        return $this;
    }

    public function toHtml():string 
    {
        return <<<HTML
                    {$this->civility} {$this->first_name} {$this->last_name} <br>
                    {$this->number} {$this->way} <br>
                    {$this->postal_code} {$this->city} <br>
                    {$this->country}
                HTML;
    }

}