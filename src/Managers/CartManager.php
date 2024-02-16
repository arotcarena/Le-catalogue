<?php
namespace Vico\Managers;

use Vico\Connection;
use Vico\Models\Cart;
use Vico\Models\Product;

class CartManager extends Manager
{
    protected $table = 'cart';

    protected $fields = [
        'user_id', 'product_id', 'quantity'
    ];

    protected $class = Cart::class;


    public function getProducts(int $user_id):array
    {
        return $this->createQueryBuilder()
                    ->select('p.*, c.quantity as quantity')
                    ->from('products p')
                    ->join('cart c')
                    ->on('c.product_id = p.id')
                    ->addWhere('c.user_id = :c_user_id')
                    ->setParams(['c_user_id' => $user_id])
                    ->setFetchClass(Product::class)
                    ->fetchAll();
    }
   
    public function updateOne(int $product_id, $add, int $user_id):void
    {
        $stock = $this->createQueryBuilder()
                        ->select('stock')
                        ->from('products')
                        ->addWhere('id = :product_id')
                        ->setParams(['product_id' => $product_id])
                        ->setFetchClass(Product::class)
                        ->fetch()
                        ->getStock();
        
        if($stock <= 0 AND $add > 0)
        {
            throw new \Exception("stock-null", 1);
        }
        if($add === 'less')
        {
            $add = -1;
        }
        elseif($add < 0)
        {
            $add = 0;
        }
        $queryBuilder = $this->createQueryBuilder()
                            ->insert_into($this->table)
                            ->addSet('quantity = :quantity, user_id = :user_id, product_id = :product_id')
                            ->setParams(['quantity' => $add, 'user_id' => $user_id, 'product_id' => $product_id]);
                            

        if($this->exists(['product_id' => $product_id, 'user_id' => $user_id], 'AND'))
        {
            $existing_quantity = (int)$this->createQueryBuilder()
                                        ->select('quantity')
                                        ->from($this->table)
                                        ->addWhere('product_id = :product_id AND user_id = :user_id')
                                        ->setParams(['product_id' => $product_id, 'user_id' => $user_id])
                                        ->fetchAssoc()['quantity'];
            if($existing_quantity >= $stock AND $add > 0)
            {
                throw new \Exception("no-stock", 1);
            }
            
            $updated_quantity =  $existing_quantity + $add;
            $updated_quantity = $updated_quantity < 0 ? 0: $updated_quantity;
            $queryBuilder = $this->createQueryBuilder()
                            ->update($this->table)
                            ->addSet('quantity = :quantity')
                            ->addWhere('user_id = :user_id AND product_id = :product_id')
                            ->setParams(['quantity' => $updated_quantity, 'user_id' => $user_id, 'product_id' => $product_id]);
                            
            if($updated_quantity > $stock)
            {
                $queryBuilder->setParams(['quantity' => $stock]);
                $light_stock = true;
            }
        }
        $queryBuilder->execute();
        if(isset($light_stock))
        {
            throw new \Exception("light-stock", 1);
        }
    }  


    public function updateStock(int $product_id, int $stock):void
    {
        if($stock === 0)
        {
            $this->deleteAll(['product_id' => $product_id]);
        }
        else
        {
            $this->createQueryBuilder()
                    ->update($this->table)
                    ->addSet('quantity = :stock')
                    ->addWhere('product_id = :product_id')
                    ->addWhere('quantity > :stock')
                    ->where_keyWord('and')
                    ->setParams(['product_id' => $product_id, 'stock' => $stock])
                    ->execute();
        }
    }



}