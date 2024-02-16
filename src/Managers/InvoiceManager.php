<?php
namespace Vico\Managers;

use Vico\Tools;
use Vico\Pagination;
use Vico\Models\Address;
use Vico\Models\Invoice;
use Vico\Models\Product;
use Vico\Managers\Manager;


class InvoiceManager extends Manager
{
    protected $table = 'invoice';

    protected $fields = [
        'id', 'user_id', 'delivery_address', 'invoice_address', 'details', 'total_price', 'invoice_date', 'delivery_date', 'status'
    ];

    protected $class = Invoice::class;

     
    public function findPaginated(?array $filters = null, ?string $key_word = 'and'):Pagination
    {
        $filters['invoice_date_order'] = 'desc';
        $filters['per_page'] = 1;
        return parent::findPaginated($filters, $key_word);
    }

    /**
     * @param Product[] $products
     */
    public function invoicePreview(array $products):Invoice
    {
        $total_price = 0;
        foreach($products as $product)
        {
            $total_price += $product->getTotal();
        }
        if($total_price <= 0)
        {
            throw new \Exception("La commande est vide (prix total = 0)", 1);    
        }
        return (new Invoice())->setTotal_price($total_price)
                ->setDelivery_date(Tools::getAddDate('+ 2 days'))
                ->setDetails(Tools::encode($products, ['brand', 'model', 'priceFormated', 'quantity', 'totalFormated']));
    }
    

    public function createId()
    {
        return random_int(1000, 10000).'F-'.(new \DateTime())->format('d-m-Y');
    }

}