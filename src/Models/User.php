<?php
namespace Vico\Models;

use Vico\Models\Address;

class User
{
    private $id;

    private $email;

    private $first_name;

    private $last_name;

    private $password;

    private $role = 'user';

    private $invoice_address_id;

    private $delivery_address_id;

    /**
     * @var Address
     */
    private $delivery_address;

    /**
     * @var Address
     */
    private $invoice_address;

    /**
     * @var Address[]
     */
    private $addresses;

    private $confirmation_token;

    private $confirmation_token_expire;

    private $code_2FA;

    private $code_2FA_expire;

    private $choice_2FA = 0;
    
    private $remember_token;

    private $confirmed_at;

    private $last_login;

    private $inactive = 0;

    private $delete_at;




    public function getId():?int {return $this->id;}

    public function getEmail():?string {return $this->email;}

    public function getFirst_name():?string {return $this->first_name;}

    public function getLast_name():?string {return $this->last_name;}

    public function getInvoice_address_id():?int {return $this->invoice_address_id;}

    public function getDelivery_address_id():?int {return $this->delivery_address_id;}

    public function getInvoice_address():?Address {return $this->invoice_address;}

    public function getDelivery_address():?Address {return $this->delivery_address;}

    public function getPassword():?string {return $this->password;}
    
    public function getRole():string {return $this->role;}

    public function getConfirmation_token(): ?string {return $this->confirmation_token;}
    
    public function getConfirmation_token_expire(): ?string {return $this->confirmation_token_expire;}

    public function getCode_2FA():?int {return $this->code_2FA;}
    
    public function getCode_2FA_expire():?int {return $this->code_2FA_expire;}
    
    public function getChoice_2FA():int {return $this->choice_2FA;}

    public function getRemember_token(): ?string {return $this->remember_token;}

    public function getConfirmed_at(): ?string {return $this->confirmed_at;}
    
    public function getDelete_at(): ?string {return $this->delete_at;}

    public function getLast_login(): ?string {return $this->last_login;}

    public function getLast_login_formated():string 
    {
        return $this->last_login === null ? 'Bienvenue sur notre site.': '(Dernière connexion le '.(new \DateTime($this->last_login))->format('d/m/Y').')';
    }
    
    public function getInactive():int {return $this->inactive;}

    public function getConfirmed_at_formated(): ?string 
    {
        return (new \DateTime($this->confirmed_at))->format('d/m/Y');
    }

    

    public function setId(int $id):self
    {
        $this->id = $id;
        return $this;
    }
    public function setEmail(string $email):self
    {
        $this->email = $email;
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
    public function setPassword(string $password):self
    {
        $this->password = $password;
        return $this;
    }
    public function encryptPassword():self 
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        return $this;
    }
    public function setRole(string $role):self
    {
        $this->role = $role;
        return $this;
    }
    public function setInvoice_address_id(?string $invoice_address_id):self
    {
        $this->invoice_address_id = $invoice_address_id;
        return $this;
    }
    public function setDelivery_address_id(?string $delivery_address):self
    {
        $this->delivery_address_id = $delivery_address;
        return $this;
    }
    public function setInvoice_address(?Address $invoice_address):self
    {
        $this->invoice_address = $invoice_address;
        return $this;
    }
    public function setDelivery_address(?Address $delivery_address):self
    {
        $this->delivery_address = $delivery_address;
        return $this;
    }

    public function setConfirmed_at(?string $confirmed_at):self 
    {
        $this->confirmed_at = $confirmed_at;
        return $this;
    }
    public function setLast_login(string $last_login):self 
    {
        $this->last_login = $last_login;
        return $this;
    }
    public function setInactive(bool $inactive):self 
    {
        $this->inactive = $inactive;
        return $this;
    }
    /**
     * utilisé pour entrer un objet user dans archived_users
     */
    public function setDelete_at(?string $delete_at):self
    {
        $this->delete_at = $delete_at;
        return $this;
    }

    public function setConfirmation_token(?string $confirmation_token):self 
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }
    public function setConfirmation_token_expire(?string $confirmation_token_expire):self 
    {
        $this->confirmation_token_expire = $confirmation_token_expire;

        return $this;
    }
    public function setRemember_token(?string $remember_token):self 
    {
        $this->remember_token = $remember_token;

        return $this;
    }
    public function setCode_2FA(?int $code_2FA):self 
    {
        $this->code_2FA = $code_2FA;

        return $this;
    }
    public function setCode_2FA_expire(?int $code_2FA_expire):self 
    {
        $this->code_2FA_expire = $code_2FA_expire;

        return $this;
    }

    /**
     * @return  ?Address[]
     */ 
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param  Address[]  $addresses
     *
     * @return  self
     */ 
    public function setAddresses(array $addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }
}