<?php
namespace Vico\Form;

use Vico\Models\User;
use Vico\Models\PasswordUpdate;
use Vico\Validators\UserValidators\PasswordChangeValidator;

class PasswordForm extends Form
{
    public function __construct(User $user, ?PasswordUpdate $model = null)
    {
        parent::__construct($model);
        $this->validator = new PasswordChangeValidator($user);
        $this->getBuilder()
                ->addInput('password', 'old_password', 'Actuel mot de passe')
                ->addInput('password', 'new_password', 'Nouveau mot de passe')
                ->addInput('password', 'password_confirm', 'Confirmez le mot de passe')
                ->addButton('Modifier le mot de passe', 'btn-primary');    
    }

}