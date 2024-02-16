<?php 
namespace Vico\Form\FormView;

use Vico\Form\FormBuilder\FormBuilder;

class FormView
{

    /**
     * @var string
     */
    private $action;

    /**
     * @var string 
     */
    private $method;

    /**
     * @var string|null 
     */
    private $enctype;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var Object
     */
    private $model;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var bool
     */
    private $labels = true;

    /**
     * @var bool
     */
    private $btn_inline = false;

    /**
     * @var bool
     */
    private $start = false;

    /**
     * @var bool
     */
    private $end = false;


    public function __construct(FormBuilder $formBuilder, array $errors, ?Object $model = null)
    {
        $this->action = $formBuilder->getAction();
        $this->method = $formBuilder->getMethod();
        $this->enctype = $formBuilder->getEnctype();
        $this->fields = $formBuilder->getFields();

        $this->model = $model;
        $this->errors = $errors;
    }

    public function setLabels(bool $labels):self 
    {
        $this->labels = $labels;

        return $this;
    }
    public function setBtn_inline(bool $btn_inline):self 
    {
        $this->btn_inline = $btn_inline;

        return $this;
    }


    /**
     * AFFICHAGE STANDARD DU FORMULAIRE ENTIER 
     */
    public function view():string 
    {
        $html = $this->start();
        $pos = 0;
        for ($done = 0; $done < count($this->fields);) 
        {   
            if(isset($this->fields[$pos]))
            {
                $field = $this->fields[$pos];
                if($field['method'] === 'button')
                {
                    $html .= $this->button($field['label'], $field['class'], $field['hidden_field']);
                }
                else
                {
                    $method = $field['method'];
                    $html .= '<div class="form-group mt-4">'. $this->$method($field['options']['name'], $field['options']) .'</div>';
                } 
                $done ++;
            }
            $pos++;
        }
        
        return $html . '</form>';
    }

    /**
     * BALISE DE DEBUT ET ERREUR GENERALE
     */
    public function start():string 
    {
        $enctype = !empty($this->enctype) ? 'enctype="'.$this->enctype.'"': '';
        if($this->start === false)
        {
            $this->start = true;
            return '<form class="form" action="'.$this->action.'" method="'.$this->method.'" '.$enctype.'>' . $this->getGeneralError();
        }
        return '';
    }

    /**
     * BALISE DE FIN
     */
    public function end():string 
    {
        if($this->end === false)
        {
            $this->end = true;
            return '</form>';
        }
        return '';
    }

    /**
     * AFFICHAGE D UNE SEULE LIGNE
     */
    public function row(string $name):string 
    {
        foreach ($this->fields as $k => $field) 
        {
            if(isset($field['options']['name']) AND $field['options']['name'] === $name)
            {
                unset($this->fields[$k]);
                $method = $field['method'];
                return '<div class="form-group mt-4">'. $this->$method($field['options']['name'], $field['options']) .'</div>';
            }
        }
    }

    /**
     * AFFICHAGE D UN BOUTON
     */
    public function btn_row(?int $pos = null):string
    {
        if($pos)
        {
            if(isset($this->fields[$pos]))
            {
                $field = $this->fields[$pos];
                unset($this->fields[$pos]);
                return $this->button($field['label'], $field['class'], $field['hidden_field']);
            }
        }
        else
        {
            foreach($this->fields as $k => $field)
            {
                if($field['method'] === 'button')
                {
                    unset($this->fields[$k]);
                    return $this->button($field['label'], $field['class'], $field['hidden_field']);
                }
            }
        }
    }
    

    private function file(string $name, array $options):string 
    {
        $multiple = $options['multiple'] ? 'multiple': '';
        $name_value = $options['multiple'] ? $name.'[]': $name;
        $class = isset($this->errors[$name]) ? 'is-invalid': '';
            $html = <<<HTML
                    <label for="$name" class="form-label">{$options['label']}</label>
                    <input id="$name" class="form-control $class" type="file" name="$name_value" $multiple>
                HTML;

        return $html . $this->getErrorFeedback($name);
    }
    
    /**
     * champ input immodifiable
     */
    private function input_locked(string $name, array $options):string
    {
        if($this->labels)
        {
            return <<<HTML
                    <label for="$name" class="form-label">{$options['label']}</label>
                    <input id="$name" class="form-control" type="{$options['type']}" name="$name" value="{$this->getValue($name)}" disabled>
                    <input type="hidden" name="$name" value="{$this->getValue($name)}">
                HTML;
        }
        return <<<HTML
                <input class="form-control" type="{$options['type']}" name="$name" value="{$this->getValue($name)}" disabled>
                <input type="hidden" name="$name" value="{$this->getValue($name)}">
            HTML;
    }


    

    private function input(string $name, array $options):string
    {
        $class = isset($this->errors[$name]) ? 'is-invalid': '';
        if($this->labels)
        {
            $html = <<<HTML
                    <label for="$name" class="form-label">{$options['label']}</label>
                    <input id="$name" class="form-control $class" type="{$options['type']}" name="$name" value="{$this->getValue($name)}">
                HTML;
        }
        else
        {
            $html = <<<HTML
                    <input class="form-control $class" type="{$options['type']}" placeholder="{$options['label']}" name="$name" value="{$this->getValue($name)}">
                HTML;
        }
        return $html . $this->getErrorFeedback($name);
    }
    
    

    private function textarea(string $name, array $options):string
    {
        $class = isset($this->errors[$name]) ? 'is-invalid': '';
        if($this->labels)
        {
            $html = <<<HTML
                    <label for="$name" class="form-label">{$options['label']}</label>
                    <textarea id="$name" class="form-control $class" name="$name">{$this->getValue($name)}</textarea>
                HTML;
        }
        else
        {
            $html = <<<HTML
                    <textarea class="form-control $class" name="$name" placeholder="{$options['label']}">{$this->getValue($name)}</textarea>
                HTML;
        }
        return $html . $this->getErrorFeedback($name);
    }
    
    

    private function checkbox(string $name, array $options):string
    {
        $data = $this->valLab_toArray($options['values'], $options['labels']);
        $name_string = count($data) > 1 ? $name.'[]': $name;
        $style = $options['inline'] ? 'style="display: inline;"': '';
        
        $class = isset($this->errors[$name]) ? 'is-invalid': '';
        $html = '';
        if(!empty($options['title']))
        {
            $html .= '<div class="form-group mb-2">'.$options['title'].'</div>';
        }
        foreach($data as $label => $value)
        { 
            $checked = '';
            if(is_array($this->getValue($name)))
            {
                if(in_array($value, $this->getValue($name)))
                {
                    $checked = 'checked';
                }
            }
            elseif($this->getValue($name) == $value)
            {
                $checked = 'checked';
            }
            $html .= <<<HTML
                    <div class="form-check $class" $style>
                        <label class="form-check-label">
                        <input class="form-check-input $class" type="checkbox" name="{$name_string}" value="{$value}" {$checked}>
                        {$label}
                        </label>
                    </div>
                    HTML;
        }
        return $html . $this->getErrorFeedback($name);
    }
    private function radio(string $name, array $options):string
    {
        $data = $this->valLab_toArray($options['values'], $options['labels']);
        
        $style = $options['inline'] ? 'style="display: inline;"': '';
        $class = isset($this->errors[$name]) ? 'is-invalid': '';
        $html = '';
        if(!empty($options['title']))
        {
            $html .= '<div class="form-group mb-2">'.$options['title'].'</div>';
        }
        foreach($data as $label => $value)
        { 
            $checked = '';
            if($this->getValue($name) == $value)
            {
                $checked = 'checked';
            }
            $html .= <<<HTML
                    <div class="form-check $class" $style>
                        <label class="form-check-label">
                        <input class="form-check-input $class" type="radio" name="{$name}" value="{$value}" {$checked}>
                            {$label}
                        </label>
                    </div>
                    HTML;
        }
        return $html . $this->getErrorFeedback($name);
    }

    

    private function select(string $name, array $options):string 
    {
        if($this->labels)
        {
            $label = '<label for="'.$name.'" class="form-label">'.$options['title'].'</label>';
            $default = $options['default'] ?? 'choisissez';
        }
        else
        {
            $label = null;
            $default = $options['title'];
        }
        $html = <<<HTML
                {$label}
                 <select id="$name" class="form-select" aria-label="Default select example" name="$name">
                    <option value="" selected>$default</option>
                HTML;

       
        $data = $this->valLab_toArray($options['values'], $options['labels']);

        foreach($data as $label => $value)
        {
            $class = '';
            if($this->getValue($name) == $value)
            {
                $class = 'selected';
            }
            $html .= <<<HTML
                    <option $class value="$value">$label</option>
                    HTML;
        }
        return $html . '</select>';
    }
    
    private function button(string $label, string $class, ?array $button_field):string 
    {
        $name = '';
        $value = '';
        if(!empty($button_field))
        {
            $name = array_key_first($button_field);
            $value = $button_field[$name];
        }
        if(!$this->btn_inline)
        {
            return <<<HTML
                    <div class="form-group">
                    <button class="mt-4 btn $class" type="submit" name="$name" value="$value">$label</button>
                    </div>
                    HTML;
        }
        return '<button class="mt-4 btn '.$class.'" type="submit" name="'.$name.'" value="'.$value.'">'.$label.'</button>';
    }

    private function getGeneralError():?string 
    {
        if(isset($this->errors['general']))
        {
            $html = '';
            foreach($this->errors['general'] as $class => $message)
            {
                $html .= '<div class="alert alert-'.$class.'">'.$message.'</div>';
            }
            return $html;
        }
        elseif(!empty($this->errors))
        {
            return '<div class="alert alert-danger">Le formulaire comporte des erreurs</div>';
        }
        return null;
    }

    private function getValue(string $name):mixed
    {
        if(is_object($this->model))
        {
            $method = 'get'.ucfirst($name);
            if(\method_exists($this->model, $method))
            {
                return $this->model->$method();
            }
            return '';
        }
        return \htmlentities($this->model[$name] ?? '');            // CE SERA A SUPPRIMER SI VRAIMENT JE N UTILISE QUE DES OBJETS COMME MODEL
    }
    private function getErrorFeedback(string $name):?string
    {
        if(isset($this->errors[$name]))
        {
           $errors = implode('<br>', $this->errors[$name]);
           return <<<HTML
                    <div class="invalid-feedback">$errors</div>
                    HTML;
        }
        return null;
    }
    

    private function valLab_toArray(array $values, array $labels):array 
    {
        $data = [];
        $i = 0;
        foreach($labels as $label)  
        {
            $data[$label] = $values[$i];
            $i++;
        }
        return $data;
    }
}