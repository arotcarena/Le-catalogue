<?php
namespace Vico\Models;

class ProductFilter 
{
    /**
     * @var int|null
     */
    private $category_id;

    /**
     * @var string|null
     */
    private $brand;

    /**
     * @var string|null
     */
    private $price_order;
    
    /**
     * @var int|null
     */
    private $price_max;

    /**
     * @var int|null
     */
    private $price_min;

    /**
     * @var int|null
     */
    private $per_page;

    /**
     * @var string|null
     */
    private $q;

    


    /**
     * Get the value of category_id
     *
     * @return  int|null
     */ 
    public function getCategory_id()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @param  int|null  $category_id
     *
     * @return  self
     */ 
    public function setCategory_id($category_id)
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Get the value of brand
     *
     * @return  string|null
     */ 
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set the value of brand
     *
     * @param  string|null  $brand
     *
     * @return  self
     */ 
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get the value of price_order
     *
     * @return  string|null
     */ 
    public function getPrice_order()
    {
        return $this->price_order;
    }

    /**
     * Set the value of price_order
     *
     * @param  string|null  $price_order
     *
     * @return  self
     */ 
    public function setPrice_order($price_order)
    {
        $this->price_order = $price_order;

        return $this;
    }

    /**
     * Get the value of price_max
     *
     * @return  int|null
     */ 
    public function getPrice_max()
    {
        return $this->price_max;
    }

    /**
     * Set the value of price_max
     *
     * @param  int|null  $price_max
     *
     * @return  self
     */ 
    public function setPrice_max($price_max)
    {
        $this->price_max = $price_max;

        return $this;
    }

    /**
     * Get the value of price_min
     *
     * @return  int|null
     */ 
    public function getPrice_min()
    {
        return $this->price_min;
    }

    /**
     * Set the value of price_min
     *
     * @param  int|null  $price_min
     *
     * @return  self
     */ 
    public function setPrice_min($price_min)
    {
        $this->price_min = $price_min;

        return $this;
    }

    /**
     * Get the value of per_page
     *
     * @return  int|null
     */ 
    public function getPer_page()
    {
        return $this->per_page;
    }

    /**
     * Set the value of per_page
     *
     * @param  int|null  $per_page
     *
     * @return  self
     */ 
    public function setPer_page($per_page)
    {
        $this->per_page = $per_page;

        return $this;
    }

    /**
     * Get the value of q
     *
     * @return  string|null
     */ 
    public function getQ()
    {
        return $this->q;
    }

    /**
     * Set the value of q
     *
     * @param  string|null  $q
     *
     * @return  self
     */ 
    public function setQ($q)
    {
        $this->q = $q;

        return $this;
    }
}