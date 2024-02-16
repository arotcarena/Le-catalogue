<?php
namespace Vico\Validators\UserValidators;

use Vico\Models\User;
use Vico\Validators\Validator;




class PasswordChangeValidator extends Validator
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->rule('required', ['old_password', 'new_password', 'password_confirm'])
            ->rule('same_passwords', ['new_password', 'password_confirm']);

    }
    public function validate():void 
    {
        parent::validate();
        if(!\password_verify($this->data['old_password'], $this->user->getPassword()))
        {
            $this->errors['old_password'][] = 'Le mot de passe est incorrect.';
        }
    }
}