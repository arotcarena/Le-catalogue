<?php
namespace Vico\Validators;

use Vico\Models\Address;
use Vico\Validators\Validator;



class AddressValidator extends Validator
{

    public function __construct()
    {
        $this->rule('required', [
            'civility', 'last_name', 'first_name', 'number', 'way', 'city', 'postal_code', 'country'
        ]);
        $this->rule('positive_int', ['postal_code']);
    }
    
}