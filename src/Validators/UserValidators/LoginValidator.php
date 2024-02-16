<?php
namespace Vico\Validators\UserValidators;

use Vico\Helper;
use Vico\Models\User;
use Vico\Managers\UserManager;
use Vico\Validators\Validator;


class LoginValidator extends Validator
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @var UrlHelper
     */
    private $url_helper;

    /**
     * @var Router
     */
    private $router;

    public function __construct(UserManager $manager, Helper $helper)
    {
        $this->manager = $manager;
        $this->url_helper = $helper->getUrlHelper();
        $this->router = $helper->router;
        $this->rule('required', ['email', 'password']);
    }

    public function validate():void
    {
        parent::validate();
        if(!empty($this->errors))
        {
            return;
        }

        $user = $this->manager->findOneOrNull(['email' => $this->data['email']]);
        if($user === null OR !password_verify($this->data['password'], $user->getPassword()))
        {
            $this->errors['general']['danger'] = 'Ces identifiants sont incorrects';
        }
        elseif($user->getConfirmed_at() === null)
        {
            $this->errors['general']['danger'] = 'Vous devez confirmer votre adresse email à l\'aide du lien reçu. <a href="'.$this->url_helper->modif_get($this->router->url('new_welcomeToken', ['id' => $user->getId()])).'">Cliquez ici pour recevoir un nouveau lien</a>';
        }
        elseif($user->getInactive())
        {
            $this->errors['general']['danger'] = 'Votre compte a été désactivé. Veuillez nous contacter pour régler le problème.';
        }
        elseif($user->getChoice_2FA() === true)
        {
            $this->status = '2FA';
            if(!isset($this->data['code_2FA']) OR isset($this->data['new_code']))
            {
                $this->errors['general']['info'] =  $this->manager->sendCode2FA($user);
            }
            elseif((int)$this->data['code_2FA'] !== $user->getCode_2FA() OR time() > $user->getCode_2FA_expire())
            {
                $this->errors['code_2FA'][] = 'Le code n\'est pas valide';
            }
        }
    }
    
    public function getStatus():?string
    {
        return $this->status;
    }
    
}