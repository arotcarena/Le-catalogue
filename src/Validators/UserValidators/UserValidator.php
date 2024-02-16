<?php
namespace Vico\Validators\UserValidators;

use Vico\Models\User;
use Vico\Managers\UserManager;
use Vico\Validators\Validator;


class UserValidator extends Validator
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var User
     */
    private $user;

    public function __construct(UserManager $userManager, User $user)
    {
        $this->userManager = $userManager;
        $this->user = $user;
        $this->rule('required', ['email'])
            ->rule('email', 'email');
 
    }
    public function validate():void
    {
        parent::validate();
        if($this->data['email'] !== $this->user->getEmail() AND $this->userManager->exists(['email' => $this->data['email']]))
        {
            $this->errors['email'][] = 'Cette adresse email correspond à un compte déjà existant.';
        }
    }
}