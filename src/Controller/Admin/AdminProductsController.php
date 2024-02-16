<?php
namespace Vico\Controller\Admin;

use Vico\Attachment\ProductAttachment;
use Vico\Auth;
use Vico\Form;
use Vico\Helper;
use Vico\Response;
use Vico\ImgPagination;
use Vico\Models\Product;
use Vico\Form\ProductForm;
use Vico\Models\ProductFilter;
use Vico\Controller\Controller;
use Vico\Form\ProductFiltersForm;
use Vico\Managers\ProductsManager;

class AdminProductsController extends Controller
{
    /**
     * @var ProductsManager
     */
    private $manager;

    public function __construct(Helper $helper)
    {
        Auth::check('admin');
        parent::__construct($helper);
        $this->manager = $this->helper->getManager('ProductsManager');
    }


    /**
     * url = '/admin/listing-des-produits'  name = 'admin_products_index
     */
    public function index():Response
    {
        $pagination = $this->manager->findPaginated($_GET);

        $product_filter = new ProductFilter();
        $filter_form = new ProductFiltersForm($this->helper->getManager('CategoryManager')->findAll(), $this->manager->getBrands(), $product_filter);
        
        $filter_form->handleRequest($_GET);


        return $this->render('admin/products/index.php', [
                'title' => 'Admin : Gestion des produits',
                'pagination' => $pagination, 
                'filter_form' => $filter_form->createView()->setLabels(false), 
            ]);
    }
    /**
     * url = '/admin/[*:slug]/ref-[i:id]'  name = 'admin_products_edit'
     */
    public function edit(array $params):Response 
    {
        $product = $this->manager->findOneOrNull(['id' => $params['id']]);
        if($params['slug'] !== $product->getSlug())
        {
            return $this->redirect($this->router->url('admin_products_edit', ['id' => $product->getId(), 'slug' => $product->getSlug()]));
        }

        $form = (new ProductForm($this->helper->getManager('CategoryManager'), $product))
                ->handleRequest(array_merge($_POST, $_FILES));

        if($form->isSubmitted() AND $form->isValid())
        {
            //on déplace les images téléchargées, on supprime celles a supprimer et on met à jour les propriétés image dans product
            ProductAttachment::updateImages($product);
            //le product est hydraté avec tous ses noms d'images donc on vide la table image where product_id = product.id et on reremplit avec les images de product
            $this->manager->update($product);
            $_SESSION['flash']['success'] = 'L\'article a bien été modifié !';
        }

        /********** peut-etre créer un Form spécial pour éviter ça*/
        $labels = [];
        foreach($product->getImages_Urls('nano') as $urls)
        {
            $labels[] = '<img src="'.$urls.'">';
        }

        $form->getBuilder()
                ->addChoice('radio', 'first_image_choice', $product->getImages_name(), $labels, 'Choix de l\'image à la une', true, 1)
                ->addChoice('checkbox', 'toDelete_images', $product->getImages_name(), $labels, 'Supprimer une image', true, 2)
                ->addButton('Modifier', 'btn-primary', null, 12);
        /******************************************************* */
        
        return $this->render('admin/products/edit.php', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * url = '/admin/nouveau-produit'  name = 'admin_products_new'
     */
    public function new():Response 
    {
        $product = new Product();

        $form = (new ProductForm($this->helper->getManager('CategoryManager'), $product))
                ->handleRequest(array_merge($_POST, $_FILES));

        if($form->isSubmitted() AND $form->isValid())
        {
            ProductAttachment::uploadImages($product);  //déplace les images téléchargées et met à jour les propriété image_name dans product
            //on enregistre le product 
            $this->manager->insert($product);
            $_SESSION['flash']['success'] = 'L\'article a bien été ajouté !';
            return $this->redirect($this->router->url('admin_products_index'));
        }

        $form->getBuilder()
                ->addButton('+ Ajouter', 'btn-primary', null, 12);

        return $this->render('admin/products/new.php', [
            'form' => $form->createView()
        ]);
    }

    /**
     * url = '/admin/suppr-[i:del_id]'  name = 'admin_products_delete'
     */
    public function delete(array $params):Response 
    {
        $product = $this->manager->findOneOrNull(['id' => $params['del_id']]);
        $this->manager->delete($product->getId());
        $_SESSION['flash']['success'] = 'L\'article a bien été supprimé !';
        return $this->redirect($this->router->url('admin_products_index'));
    }

}