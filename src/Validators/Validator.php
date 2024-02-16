<?php
namespace Vico\Validators;

abstract class Validator
{
    protected $rules = [];

    protected $errors = [];

    protected $data;


    public function setData(array $data):self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array|string $field
     */
    public function rule(string $rule, $field):self
    {
        if(is_array($field))
        {
            foreach($field as $f)
            {
                $this->rules[$rule][] = $f;
            }
        }
        else
        {
            $this->rules[$rule][] = $field;
        }
        return $this;
    }
    public function errors():array
    {
        return $this->errors;
    }
    public function validate():void
    {
        if(array_key_exists('required', $this->rules))
        {
            foreach($this->rules['required'] as $field)
            {
                if(empty($this->data[$field]))
                {
                    $this->errors[$field][] = 'Ce champ ne peut pas être vide';
                }
            }
        }
        if(array_key_exists('email', $this->rules))
        {
            foreach($this->rules['email'] as $field)
            {
                if(!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL))
                {
                    $this->errors[$field][] = 'Ceci n\'est pas une adresse email valide';
                }
            }
        }
        if(array_key_exists('same_passwords', $this->rules))
        {
            $field_1 = $this->rules['same_passwords'][0];
            $field_2 = $this->rules['same_passwords'][1];
            if($this->data[$field_1] !== $this->data[$field_2])
            {
                $this->errors[$field_1][] = 'Les deux mots de passe ne correspondent pas';
                $this->errors[$field_2][] = 'Les deux mots de passe ne correspondent pas';
            }
        }
        if(array_key_exists('positive_int', $this->rules))
        {
            foreach($this->rules['positive_int'] as $field)
            {
                if(!\filter_var($this->data[$field], FILTER_VALIDATE_INT) OR $this->data[$field] <= 0)
                {
                    $this->errors[$field][] = 'Ce champ doit être un entier supérieur à 0';
                }
            }
        }
        if(array_key_exists('image', $this->rules))
        {
            foreach($this->rules['image'] as $field)
            {
                if(!empty($this->data[$field]['tmp_name']))
                {
                    $finfo = new \finfo();
                    if(is_array($this->data[$field]['tmp_name']))
                    {
                        foreach($this->data[$field]['tmp_name'] as $pos => $tmp_name)
                        {
                            if(!empty($tmp_name))
                            {
                                $info = $finfo->file($tmp_name, FILEINFO_MIME_TYPE);
                                if(!in_array($info, ['image/jpeg', 'image/png']))
                                {
                                    $this->errors[$field][] = 'Le fichier "'.$this->data[$field]['name'][$pos].'" n\'est pas une image valide. Formats acceptés : JPEG, PNG';
                                }
                            }
                        }
                    }
                    else
                    {   
                        $info = $finfo->file($this->data[$field]['tmp_name'], FILEINFO_MIME_TYPE);
                        if(!in_array($info, ['image/jpeg', 'image/png']))
                        {
                            $this->errors[$field][] = 'Le fichier n\'est pas une image valide. Formats acceptés : JPEG, PNG';
                        }
                    }
                    
                }
                
            }
        }
        
    }
}