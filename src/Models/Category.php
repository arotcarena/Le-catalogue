<?php
namespace Vico\Models;

class Category
{
    private $id;

    private $name;


    public function getId():int
    {
        return $this->id;
    }

    public function getName():?string
    {
        return $this->name;
    }

    public function setName(?string $name):self
    {
        $this->name = $name;

        return $this;
    }

    public function color():?string
    {
        if($this->id === 1)
        {
            return 'danger';
        }
        if($this->id === 2)
        {
            return 'info';
        }
        if($this->id === 3)
        {
            return 'secondary';
        }
    }
}
