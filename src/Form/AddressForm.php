<?php
namespace Vico\Form;

use Vico\Validators\AddressValidator;

class AddressForm extends Form
{
    public function __construct(?Object $model = null)
    {
        parent::__construct($model);
        $this->validator = new AddressValidator();
        $this->getBuilder()
                ->addChoice('radio', 'civility', ['M.', 'Mme', 'Mlle'], ['Monsieur', 'Madame', 'Mademoiselle'])
                ->addInput('text', 'first_name', 'prénom')
                ->addInput('text', 'last_name', 'nom')
                ->addInput('text', 'number', 'numéro')
                ->addInput('text', 'way', 'voie')
                ->addInput('text', 'postal_code', 'code postal')
                ->addInput('text', 'city', 'ville')
                ->addInput('text', 'country', 'pays');
    }
}

