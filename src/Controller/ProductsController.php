<?php
namespace Vico\Controller;

use Vico\Form;
use Vico\Helper;
use Vico\Response;
use Vico\ImgPagination;
use Vico\ImagePagination;
use Vico\Models\ProductFilter;
use Vico\Form\ProductFiltersForm;
use Vico\Managers\ProductsManager;

class ProductsController extends Controller
{
    /**
     * @var ProductsManager
     */
    private $manager;

    public function __construct(Helper $helper)
    {
        parent::__construct($helper);
        $this->manager = $this->helper->getManager('ProductsManager');
    }


    /**
     * url = '/', name = 'products_index'
     */
    public function index():Response
    {
        $pagination = $this->manager->findPaginated(array_merge($_GET, ['stock_min' => 1]));

        $product_filter = new ProductFilter();
        $filter_form = new ProductFiltersForm($this->helper->getManager('CategoryManager')->findAll(), $this->manager->getBrands(), $product_filter);
        
        $filter_form->handleRequest($_GET);


        return $this->render('products/index.php', [
                'title' => 'Catalogue',
                'pagination' => $pagination, 
                'filter_form' => $filter_form->createView()->setLabels(false), 
            ]);
    }

    /**
     * url = '/[*:slug]/ref-[i:id]', name = 'products_show'
     * 
     * @param array $params [$slug, $id]
     */
    public function show(array $params):Response
    {
        $product = $this->manager->findOneOrNull(['id' => $params['id']]);
        $imagePagination = new ImagePagination($product, $this->helper->getUrlHelper());
        $quant = $_GET['quant'] ?? 1;

        return $this->render('products/show.php', [
                'title' => $product->getSlug(),
                'product' => $product,
                'quant' => $quant,
                'imagePagination' => $imagePagination
            ]);
    }
}