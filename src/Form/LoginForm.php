<?php
namespace Vico\Form;

use Vico\Helper;
use Vico\Models\Login;
use Vico\Managers\UserManager;
use Vico\Form\FormView\FormView;
use Vico\Validators\UserValidators\LoginValidator;

class LoginForm extends Form
{
    public function __construct(UserManager $manager, Helper $helper, ?Login $login = null)
    {
        parent::__construct($login);
        $this->validator = new LoginValidator($manager, $helper);
        $this->getBuilder()
                ->addInput('text', 'email', 'adresse e-mail', 1)
                ->addInput('password', 'password', 'mot de passe', 2)
                ->addChoice('checkbox', 'remember', [1], ['Se souvenir de moi'], null, 4)
                ->addButton('connexion', 'btn-primary', null, 5);    
    }

    public function createView():FormView 
    {
        if($this->validator->getStatus() === '2FA')         
        {
            $this->getBuilder()
                    ->addInput('text', 'code_2FA', 'Entrez le code d\'authentification', 3)
                    ->lock([1, 2])
                    ->addButton('demander un nouveau code', 'btn-outline-secondary', ['new_code' => 1], 6);
        }
        return parent::createView();
    }
}



        
    