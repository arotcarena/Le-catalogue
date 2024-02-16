<?php
namespace Vico\Form;

use Vico\Managers\UserManager;
use Vico\Validators\UserValidators\UserValidator;

class UserForm extends Form
{
    public function __construct(UserManager $manager, ?Object $user = null)
    {
        parent::__construct($user);
        $this->validator = new UserValidator($manager, $user);
        $this->getBuilder()
                ->addInput('text', 'last_name', 'Nom')
                ->addInput('text', 'first_name', 'Prénom')
                ->addInput('text', 'email', 'Adresse e-mail')
                ->addButton('Modifier', 'btn-primary');    
    }

}
