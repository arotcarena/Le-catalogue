<?php
namespace Vico\Models;

use Vico\Config;
use Vico\Models\Category;
use Vico\Attachment\ProductAttachment;



class Product
{
    private $id;

    private $brand;

    private $model;

    private $description;

    private $price;

    

    /**
     * Uploadable
     * @var array
     * 'tmp_name'[]
     */
    private $images = [];

    /**
     * @var array
     */
    private $toDelete_images = [];

    /**
     * @var string|null
     */
    private $first_image_choice;

    /**
     * @var string|null
     */
    private $first_image_name;

    /**
     * @var array
     */
    private $other_images_name = [];


    /**
     * @var ?Category
     */
    private $category;

    private $category_id;

    private $slug;

    private $stock;

    private $quantity;

    public function getQuantity():?int
    {
        return (int)$this->quantity;
    }

    public function getStock():?int
    {
        return (int)$this->stock;
    }
    public function setStock(int $stock):self 
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSlug():?string 
    {
        if($this->slug === null)
        {
            $this->slug = strtolower(implode('-', explode(' ', $this->brand))).'-'.strtolower(implode('-', explode(' ', $this->model)));
        }
        return $this->slug;
    }
    

    public function getId():?int
    {
        return $this->id;
    }

    public function setId(int $id):self
    {
        $this->id = $id;
        return $this;
    }

    public function getBrand():?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand):self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getModel():?string
    {
        return $this->model;
    }

    public function setModel(string $model):self
    {
        $this->model = $model;
        return $this;
    }

    public function getDescription():?string
    {
        return $this->description;
    }

    public function setDescription(string $description):self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice():?int
    {
        return $this->price;
    }
    public function getPriceFormated():?string
    {
        return \number_format($this->price, 0, '.', ' '). ' €';
    }
    public function getTotalFormated():?string 
    {
        return \number_format($this->getPrice() * $this->getQuantity(), 0, '', ' ').' €';
    }
    public function getTotal():?string 
    {
        return $this->getPrice() * $this->getQuantity();
    }
    public function setPrice(int $price):self
    {
        $this->price = $price;
        return $this;
    }

 
    public function getCategory_id():?int
    {
        return $this->category_id;
    }

    public function setCategory_id(int $category_id):self
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function getCategory():?Category
    {
        return $this->category;
    }
    public function setCategory(?Category $category):self
    {
        $this->category = $category;

        return $this;
    }


    public function getFirstImage_Url(string $format):string 
    {
        if(file_exists(Config::product_image_path() . DIRECTORY_SEPARATOR . $this->getFirst_image_name() . '_' . $format . ProductAttachment::EXTENSION))
        {
            return Config::PRODUCT_IMAGE_URL . $this->getFirst_image_name() . '_' . $format . ProductAttachment::EXTENSION;
        }
        return Config::PRODUCT_IMAGE_URL . 'default_' . $format . ProductAttachment::EXTENSION;
    }
    public function getOtherImages_Urls(string $format):array 
    {
        $urls = [];
        $names = $this->getOther_images_name();
        if(!empty($names))
        {
            foreach($names as $name)
            {
                if(file_exists(Config::product_image_path() . DIRECTORY_SEPARATOR . $name . '_' . $format . ProductAttachment::EXTENSION))
                {
                    $urls[] = Config::PRODUCT_IMAGE_URL . $name . '_' . $format . ProductAttachment::EXTENSION;
                }
            }
        }
        return $urls;
    }
    public function getImages_Urls(string $format):array
    {
        $urls = [];
        $names = $this->getImages_name();
        if(!empty($names))
        {
            foreach($names as $name)
            {
                if(file_exists(Config::product_image_path() . DIRECTORY_SEPARATOR . $name . '_' . $format . ProductAttachment::EXTENSION))
                {
                    $urls[] = Config::PRODUCT_IMAGE_URL . $name . '_' . $format . ProductAttachment::EXTENSION;
                }
            }
        }
        return $urls;
    }


    public function changeFirst_image(string $first_image):self
    {
        $this->removeImages_name([$first_image])
                ->addOther_image_name($this->first_image_name)
                ->setFirst_image_name($first_image);

        return $this;
    }

    public function removeImages_name(array $remove):self
    {
        if(in_array($this->first_image_name, $remove))
        {
            $this->first_image_name = null;
        }
        foreach($this->other_images_name as $key => $name)
        {
            if(in_array($name, $remove))
            {
                unset($this->other_images_name[$key]);
            }
        }
        return $this;
    }

    /**
     * Get the value of first_image_name
     *
     * @return  string|null
     */ 
    public function getFirst_image_name()
    {
        return $this->first_image_name;
    }

    /**
     * Set the value of first_image_name
     *
     * @param  string|null  $first_image_name
     *
     * @return  self
     */ 
    public function setFirst_image_name($first_image_name)
    {
        $this->first_image_name = $first_image_name;

        return $this;
    }

        /**
     * Get the value of other_images_name
     *
     * @return  array
     */ 
    public function getOther_images_name()
    {
        return $this->other_images_name;
    }

    /**
     * Set the value of other_images_name
     *
     * @param  array  $other_images_name
     *
     * @return  self
     */ 
    public function setOther_images_name(array $other_images_name)
    {
        $this->other_images_name = $other_images_name;

        return $this;
    }

    public function addOther_images_name(array $images_name):self
    {
        $this->other_images_name = array_merge($this->other_images_name, $images_name);

        return $this;
    }

    public function addOther_image_name(string $name):self
    {
        $this->other_images_name[] = $name;

        return $this;
    }

    /**
     * Get the value of images
     *
     * @return  array
     */ 
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set the value of images
     *
     * @param  array  $images
     *
     * @return  self
     */ 
    public function setImages(array $images)
    {
        $this->images = $images['tmp_name'] ?? [];

        return $this;
    }

    /**
     * @return array  (array_merge de firstImages_name ET otherImages_name)
     */
    public function getImages_name():array
    {
        return array_merge([$this->getFirst_image_name()], $this->getOther_images_name());
    }
    

   

    

    /**
     * Get the value of first_image_choice
     *
     * @return  string|null
     */ 
    public function getFirst_image_choice()
    {
        return $this->first_image_choice ?: $this->first_image_name;
    }

    /**
     * Set the value of first_image_choice
     *
     * @param  string|null  $first_image_choice
     *
     * @return  self
     */ 
    public function setFirst_image_choice($first_image_choice)
    {
        $this->first_image_choice = $first_image_choice;

        return $this;
    }

    /**
     * Get the value of toDelete_images
     *
     * @return  array
     */ 
    public function getToDelete_images()
    {
        return $this->toDelete_images;
    }

    /**
     * Set the value of toDelete_images
     *
     * @param  array  $toDelete_images
     *
     * @return  self
     */ 
    public function setToDelete_images(array $toDelete_images)
    {
        $this->toDelete_images = $toDelete_images;

        return $this;
    }
}