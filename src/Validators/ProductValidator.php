<?php
namespace Vico\Validators;



class ProductValidator extends Validator
{
    public function __construct()
    {
        $this->rule('required', ['brand', 'model', 'price', 'description']);
        $this->rule('image', 'images');
    }
    public function validate():void 
    {
        parent::validate();

        if(count($this->data['images']['name']) > 7)
        {
            if(!empty($this->errors['images']))
            {
                unset($this->errors['images']);
            }
            $this->errors['images'][] = 'Vous ne pouvez charger que 7 images au maximum';
            return;
        }
        
        if(empty($this->errors['images']))
        {
            foreach($this->data['images']['size'] as $pos => $size)
            {
                if($size > 900000)
                {
                    $this->errors['images'][] = 'L\'image "'.$this->data['images']['name'][$pos].'" est trop volumineuse : '. number_format($size / 1000, 0, '', '') .' ko (maximum 900 ko)';
                }
            }
        }

        if(isset($this->data['toDelete_images']) AND in_array($this->data['first_image_choice'], $this->data['toDelete_images']))
        {
            $this->errors['toDelete_images'][] = 'Vous ne pouvez pas supprimer l\'image à la une';
        }
        
    }
}