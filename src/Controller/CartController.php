<?php
namespace Vico\Controller;

use Vico\Auth;
use Vico\Helper;
use Vico\Response;
use Vico\Managers\CartManager;


class CartController extends Controller
{
    /**
     * @var CartManager
     */
    private $manager;

    public function __construct(Helper $helper)
    {
        Auth::check('user');
        parent::__construct($helper);
        $this->manager = $this->helper->getManager('CartManager');
    }
    /**
     * url = '/mon-panier', name = 'cart_index'
     */
    public function index():Response
    {
        $products = $this->manager->getProducts($_SESSION['id']);
        return $this->render('cart/index.php', ['products' => $products]);
    }


    /**
     * url = '/mon-panier/modif/ref-[i:product_id]-quant-[*:quantity]', name = 'cart_update'
     */
    public function update(array $params):Response 
    {
        if(!isset($params['product_id']) OR !isset($params['quantity']))
        {
            return $this->redirect($this->router->url('unknown_error'));
        }
        try
        {
            $this->manager->updateOne($params['product_id'], $params['quantity'], $_SESSION['id']);
            if(!isset($_GET['cart']))
            {
                $_SESSION['flash']['success'] = 'L\'article a bien été ajouté à votre panier ! <a href="'.$this->router->url('cart_index').'" class="btn btn-primary">Voir mon panier</a>';
                return $this->redirect($this->router->url('products_index'));
            }
            return $this->redirect($this->router->url('cart_index'));
        }
        catch(\Exception $e)
        {
            if(!in_array($e->getMessage(), ['no-stock', 'light-stock', 'stock-null']))
            {
                return $this->redirect($this->router->url('unknown_error'));
            }
            $url = $this->router->url('cart_index').'?id='.$params['product_id']; 
            switch ($e->getMessage()) {
                case 'no-stock':
                    $message = 'Stock insuffisant';
                    break;
                case 'light-stock':
                    $message = 'La quantité a été adaptée en raison d\'un stock insuffisant';
                    break;
                case 'stock-null':
                    $message = 'Article en rupture de stock';
                    $url = $this->router->url('products_index');
            }
            $_SESSION['flash']['danger'] = $message;
            return $this->redirect($url);
        }
    }

    /**
     * url = '/mon-panier/suppr/ref-[i:del_id]', name = 'cart_del'
     */
    public function delete(array $params):Response
    {
        if(isset($params['del_id']))
        {
            try
            {
                $this->manager->deleteAll(['user_id' => $_SESSION['id'], 'product_id' => $params['del_id']], 'AND');
                $_SESSION['flash']['success'] = 'L\'article a bien été supprimé';
            }
            catch(\Exception $e)
            {
                $_SESSION['flash']['danger'] = 'L\'article n\'a pas pu être supprimé';
            }
            return $this->redirect($this->router->url('cart_index'));
        }
        else
        {
            $_SESSION['flash']['danger'] = 'Une erreur est survenue. Veuillez réessayer ultérieurement';
        }
        return $this->redirect($this->router->url('cart_index'));
    }
}



