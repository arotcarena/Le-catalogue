<?php
namespace Vico\Managers;

use Vico\Attachment\ProductAttachment;
use Vico\Tools;
use Vico\Pagination;
use Vico\Models\Product;
use Vico\Managers\CartManager;
use Vico\Managers\CategoryManager;

class ProductsManager extends Manager
{
    protected $table = 'products';
    protected $fields = [
        'id', 'brand', 'model', 'description', 'price', 'category_id', 'stock', 'first_image_name'
    ];
    protected $q_search_fields = [
        'brand', 'model', 'description'
    ];
    protected $class = Product::class;

    public function findOneOrNull(array $filters, ?string $key_word = null):?Product
    {
        $product = parent::findOneOrNull($filters, $key_word);
        
        $images = $this->createQueryBuilder()
                        ->select('name')
                        ->from('image')
                        ->addWhere('product_id = '.$product->getId())
                        ->fetchAllAssoc();
        foreach($images as $image)
        {
            $product->addOther_image_name($image['name']);
        }
        return $product;
    }

    public function getBrands()
    {
        $products = $this->findAll();
        $brands = [];
        foreach($products as $product)
        {
            if(!in_array($product->getBrand(), $brands))
            {
                $brands[] = $product->getBrand();
            }
        }
        return $brands;
    }
    
    public function insert(Product $product):int
    {
        $id = parent::persist($product);
        //on insère les noms des images secondaires dans la table image
        foreach($product->getOther_images_name() as $name)
        {
            if(!empty($name))
            {
                $this->createQueryBuilder()
                ->insert_into('image')
                ->addSet('product_id = '.$id.', name = "'.$name.'"')
                ->execute();
            }
        }
        return $id;
    }
    public function update(Product $product):void 
    {
        parent::persist($product);
        //mise a jour du panier
        (new CartManager())->updateStock($product->getId(), $product->getStock());

        //mise a jour des images
        //suppression
        $this->createQueryBuilder()
                ->delete_from('image')
                ->addWhere('product_id = :id')
                ->setParams(['id' => $product->getId()])
                ->execute();
        //ajout other_images (on ajoute pas first_image car son name est déja dans product)
        foreach($product->getOther_images_name() as $name)
        {
            if(!empty($name))
            {   
                $this->createQueryBuilder()
                    ->insert_into('image')
                    ->addSet('product_id = :id, name = :name')
                    ->setParams(['id' => $product->getId(), 'name' => $name])
                    ->execute();
            }
        }
        
    }

    
    public function delete(int $id):void 
    {
        ProductAttachment::delete($this->findOneOrNull(['id' => $id]));
        (new CartManager())->deleteAll(['product_id' => $id]);
        parent::delete($id);
    }

    public function findPaginated(?array $filters = null, ?string $key_word = 'and'):Pagination    
    {
        $queryBuilder = $this->createQueryBuilder()
                            ->select('DISTINCT p.*')
                            ->from('products p')
                            ->join('category c')
                            ->on('p.category_id = c.id');
        if(!empty($filters))
        {
            $queryBuilder->filters($filters, $this->fields, 'p')
                            ->where_keyWord($key_word);
            if(!empty($filters['q']))
            {
                $queryBuilder->qSearch($filters['q'], $this->q_search_fields, 'p')
                            ->qSearch($filters['q'], $this->getQ_search_fields(CategoryManager::class), 'c');
            }
        }                   
        $per_page = 5;
        if(!empty($filters['per_page']))
        {
            $per_page = $filters['per_page'];
        }
        $pagination = $this->createPagination($queryBuilder->getQuery(), $per_page)
                            ->fetchClass($this->class);

        if(!empty($pagination->getItems()))                
        {  
            Tools::inject($pagination->getItems(), CategoryManager::class, 'category');
        }         
        return $pagination;
    }
    
}