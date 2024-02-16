<?php
namespace Vico\Validators\UserValidators;

use Vico\Managers\UserManager;
use Vico\Validators\Validator;




class PasswordInitValidator extends Validator
{

    public function __construct()
    {
        $this->rule('required', ['new_password', 'password_confirm'])
            ->rule('same_passwords', ['new_password', 'password_confirm']);
    }
}