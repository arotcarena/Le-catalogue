<?php
namespace Vico\Validators\UserValidators;

use Vico\Router;
use Vico\UrlHelper;
use Vico\Managers\UserManager;
use Vico\Validators\Validator;


class SigninValidator extends Validator
{
    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @var UrlHelper
     */
    private $url_helper;

    public function __construct(UserManager $userManager, UrlHelper $url_helper, Router $router)
    {
        $this->manager = $userManager;
        $this->url_helper = $url_helper;
        $this->router = $router;
        $this->rule('required', ['email', 'password', 'password_confirm'])
            ->rule('email', 'email')
            ->rule('same_passwords', ['password', 'password_confirm']);
   
    }
    public function validate():void
    {
        parent::validate();
        if(!empty($this->data['email']) AND $this->manager->exists(['email' => $this->data['email']]))
        {
            $this->errors['email'][] = 'Cette adresse email correspond à un compte déjà existant.
                                        <a href="'.$this->url_helper->modif_get($this->router->url('login'), ['email' => $this->data['email']]).'">Cliquez ici pour vous connecter</a>';
        }
    }
    
}