<?php
namespace Vico\Models;

class PasswordUpdate 
{
    /**
     * @var string|null
     */
    private $old_password;

    /**
     * @var string|null
     */
    private $new_password;

    /**
     * @var string|null
     */
    private $password_confirm;

    

    /**
     * Get the value of password_confirm
     *
     * @return  string|null
     */ 
    public function getPassword_confirm()
    {
        return $this->password_confirm;
    }

    /**
     * Set the value of password_confirm
     *
     * @param  string|null  $password_confirm
     *
     * @return  self
     */ 
    public function setPassword_confirm($password_confirm)
    {
        $this->password_confirm = $password_confirm;

        return $this;
    }

    /**
     * Get the value of new_password
     *
     * @return  string|null
     */ 
    public function getNew_password()
    {
        return $this->new_password;
    }

    /**
     * Set the value of new_password
     *
     * @param  string|null  $new_password
     *
     * @return  self
     */ 
    public function setNew_password($new_password)
    {
        $this->new_password = $new_password;

        return $this;
    }

    /**
     * Get the value of old_password
     *
     * @return  string|null
     */ 
    public function getOld_password()
    {
        return $this->old_password;
    }

    /**
     * Set the value of old_password
     *
     * @param  string|null  $old_password
     *
     * @return  self
     */ 
    public function setOld_password($old_password)
    {
        $this->old_password = $old_password;

        return $this;
    }
}