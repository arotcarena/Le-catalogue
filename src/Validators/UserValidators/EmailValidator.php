<?php
namespace Vico\Validators\UserValidators;


use Vico\Managers\UserManager;
use Vico\Validators\Validator;


class EmailValidator extends Validator
{

    /**
     * @var UserManager
     */
    private $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
        $this->rule('required', ['email'])
            ->rule('email', 'email');
    }
    public function validate():void 
    {
        parent::validate();
        if(!$this->manager->exists(['email' => $this->data['email']]) AND empty($this->errors['email']))
        {
            $this->errors['email'][] = 'Cette adresse email ne correspond à aucun compte existant.';
        }
    }
}