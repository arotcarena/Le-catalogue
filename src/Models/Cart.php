<?php 
namespace Vico\Models;

class Cart 
{
    private $user_id;

    private $product_id;

    private $quantity;

    public function getUser_id():int {return $this->user_id;}

    public function getProduct_id():int {return $this->product_id;}
    
    public function getQuantity():int {return $this->quantity;}
}