<?php
namespace Vico\Validators;



class InvoiceValidator extends Validator
{

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->rule('required', ['delivery_date', 'status']);

        if(!in_array($this->data['status'], [1, 2, 3]))
        {
            $this->errors['status'][] = 'doit être un entier entre 1 et 3. (1 = en préparation, 2 = expédié, 3 = livré)';
        }
    }
    
}