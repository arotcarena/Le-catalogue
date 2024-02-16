<?php 
namespace Vico\Form\FormBuilder;

use Vico\Validators\Validator;

class FormBuilder
{
        /**
     * @var string
     */
    private $action = '';

    /**
     * @var string 
     */
    private $method = 'post';

    /**
     * @var string 
     */
    private $enctype;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var Validator|null 
     */
    private $validator = null;


    public function setAction(string $action):self
    {
        $this->action = $action;

        return $this;
    }
    public function getAction():string 
    {
        return $this->action;
    }
    public function setMethod(string $method):self
    {
        $this->method = $method;

        return $this;
    }
    public function getMethod():string 
    {
        return $this->method;
    }
    public function setEnctype(string $enctype):self 
    {
        $this->enctype = $enctype;

        return $this;
    }
    public function getEnctype():?string 
    {
        return $this->enctype;
    }
    public function setValidator(Validator $validator):self 
    {
        $this->validator = $validator;

        return $this;
    }
    public function getValidator():?Validator 
    {
        return $this->validator;
    }
    public function getFields():array 
    {
        return $this->fields;
    }


    //TOUTES LES FONCTION POUR AJOUTER, SUPPRIMER OU BLOQUER(visible mais non modifiable) DES CHAMPS
    public function addFile(string $name, ?string $label = null, ?bool $multiple = false, ?int $pos = null):self 
    {
        $field = [
            'method' => 'file',
            'options' => [
                'name' => $name,
                'label' => $label,
                'multiple' => $multiple
            ]
            ];
        return $this->addField($field, $pos);
    }


    /**
     * @param ?int $pos (la position relative souhaitée par rapport aux autres champs)
     */
    public function addInput(string $type, string $name, ?string $label = null, ?int $pos = null):self
    {
        $field =  [
            'method' => 'input',
            'options' => [
                'name' => $name,
                'type' => $type,
                'label' => $label
            ]
        ];
        return $this->addField($field, $pos);
    }
    public function addTextarea(string $name, ?string $label = null, ?int $pos = null):self
    {
        $field = [
            'method' => 'textarea',
            'options' => [
                'name' => $name,
                'label' => $label
            ]
        ];

        return $this->addField($field, $pos);
    }
    /**
     * @param bool|null $inline = false
     */
    public function addChoice(string $method, string $name, array $values, array $labels, ?string $title = null, $inline = false, ?int $pos = null):self
    {
        $field = [
            'method' => $method,
            'options' => [
                'title' => $title,
                'name' => $name,
                'values' => $values,
                'labels' => $labels,
                'inline' => $inline
            ] 
        ];

        return $this->addField($field, $pos);
    }
    /**
     * @param ?string $default (sera utilisé comme valeur par défaut seulement si $form_view->labels = false, dans le cas contraire c'est title qui est utilisé)
     */
    public function addSelect(string $name, string $title, array $values, array $labels, ?string $default = null, ?int $pos = null):self
    {
        $field = [
            'method' => 'select',
            'options' => [
                'name' => $name,
                'title' => $title,
                'default' => $default,
                'values' => $values,
                'labels' => $labels
                ]
        ];

        return $this->addField($field, $pos);
    }
    /**
     * @param ?array $hidden_field (['name' => $value] le champ caché ajouté en cas d'appui sur le bouton)
     */
    public function addButton(string $label, string $class, ?array $hidden_field = null, ?int $pos = null):self 
    {
        $field = [
            'method' => 'button',
            'label' => $label,
            'class' => $class,
            'hidden_field' => $hidden_field
        ];
           
        return $this->addField($field, $pos);
    }

    public function lock(array $fields_pos):self
    {
        foreach($fields_pos as $pos)
        {
            $this->fields[$pos]['method'] .= '_locked'; 
        }

        return $this;
    }

    public function deleteField(string $name):self 
    {
        unset($this->fields[$name]);
        
        return $this;
    }

    private function addField(array $field, ?int $pos):self
    {
        if($pos !== null)
        {
            $this->fields[$pos] = $field;
        }
        else
        {
            $this->fields[] = $field;
        }
        return $this;
    }

}
