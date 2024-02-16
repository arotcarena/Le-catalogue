<?php
namespace Vico\Models;

class Login 
{
    /**
     * @var ?string
     */
    private $email;

    /**
     * @var ?string
     */
    private $password;

    /**
     * @var ?int
     */
    private $code_2FA;

    /**
     * @var bool
     */
    private $remember = false;

    public function setEmail(?string $email):self
    {
        $this->email = $email;

        return $this;
    }
    public function getEmail():?string 
    {
        return $this->email;
    }
    public function setPassword(?string $password):self
    {
        $this->password = $password;

        return $this;
    }
    public function getPassword():?string 
    {
        return $this->password;
    }
    public function setCode_2FA(?string $code_2FA):self
    {
        $this->code_2FA = $code_2FA;

        return $this;
    }
    public function getCode_2FA():?string 
    {
        return $this->code_2FA;
    }
    

    public function getRemember():bool
    {
        return $this->remember;
    }

    public function setRemember(bool $remember):self
    {
        $this->remember = $remember;

        return $this;
    }
}