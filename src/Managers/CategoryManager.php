<?php
namespace Vico\Managers;

use Vico\UrlHelper;
use Vico\Pagination;
use Vico\Models\Product;
use Vico\Models\Category;
use Vico\Managers\CartManager;

class CategoryManager extends Manager
{
    protected $table = 'category';
    protected $fields = ['id', 'name'];
    protected $q_search_fields = ['name'];
    protected $class = Category::class;

    /**
     * @param Product[] $products
     */
    public static function hydrateProducts(array $products):void
    {
        $categories = (new self())->findAll();
        $categoriesById = [];
        foreach($categories as $category)
        {
            $categoriesById[$category->getId()] = $category;
        }
        foreach($products as $product)
        {
            $product->setCategory($categoriesById[$product->getCategory_id()]);
        }
    }
    
}