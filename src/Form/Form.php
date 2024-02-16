<?php
namespace Vico\Form;

use Vico\Tools;
use Vico\Validators\Validator;
use Vico\Form\FormView\FormView;
use Vico\Form\FormBuilder\FormBuilder;

class Form
{
    /**
     * @var Object|null
     */
    private $model;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var bool
     */
    private $submitted = false;

    /**
     * @var FormBuilder|null
     */
    private $builder;

    /**
     * @var Validator|null
     */
    protected $validator;


    public function __construct(?Object $model = null)
    {
        $this->model = $model;
    }

    /**
     * dans le cas ou on utilise la classe Form directemment depuis l'extérieur
     */
    public function setValidator(Validator $validator):self
    {
        $this->validator = $validator;

        return $this;
    }

    public function handleRequest(array $request):self
    {
        if(!empty($request))
        {
            /*FUTUR FONCTIONNEMENT
            $this->errors = Tools::hydrate($this->model, $request);
            // pour que ça marche tous les Models doivent étendre d'une classe parent avec $this->errors 
            */

            if(!empty($this->validator))
            {   
                $this->validator->setData($request)->validate();
                $this->errors = $this->validator->errors();
            }
            $this->submitted = true; 
            if($this->model !== null)
            {
                Tools::hydrate($this->model, $request);
            }
        }
        return $this;
    }

    public function isSubmitted():bool 
    {
        return $this->submitted;
    }

    public function isValid():bool 
    {
        return empty($this->errors);
    }

    public function createView():FormView 
    {
        return (new FormView($this->builder, $this->errors, $this->model));
    }

    public function getBuilder():FormBuilder 
    {
        if($this->builder === null)
        {
            $this->builder = new FormBuilder();
        }
        return $this->builder;
    }
}