<?php
namespace Vico\Form;

use Vico\Router;
use Vico\UrlHelper;
use Vico\Models\User;
use Vico\Managers\UserManager;
use Vico\Validators\UserValidators\SigninValidator;

class SigninForm extends Form
{
    public function __construct(UserManager $manager, UrlHelper $url_helper, Router $router, ?User $user = null)
    {
        parent::__construct($user);
        $this->validator = new SigninValidator($manager, $url_helper, $router);
        $this->getBuilder()
                ->addInput('text', 'email', 'Entrez votre adresse e-mail')
                ->addInput('password', 'password', 'Créez un mot de passe')
                ->addInput('password', 'password_confirm', 'Confirmez le mot de passe')
                ->addChoice('checkbox', 'choice_2FA', [true], ['Exiger l\'authentification à deux facteurs'])
                ->addButton('S\'inscrire', 'btn-primary');
    }
}