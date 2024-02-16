<?php
namespace Vico\Controller;

use Vico\Auth;
use Vico\Tools;
use Vico\Helper;
use Vico\Response;
use Knp\Snappy\Pdf;
use Vico\Form\Form;
use Vico\Notification;
use Vico\Managers\CartManager;

class OrderController extends Controller
{
    /**
     * @var InvoiceManager
     */
    private $invoiceManager;

    /**
     * @var CartManager
     */
    private $cartManager;

    /**
     * @var ProductsManager
     */
    private $productsManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AddressManager
     */
    private $addressManager;

    public function __construct(Helper $helper)
    {
        Auth::check('user');
        parent::__construct($helper);
        $this->invoiceManager = $this->helper->getManager('InvoiceManager');
        $this->productsManager = $this->helper->getManager('ProductsManager');
        $this->cartManager = $this->helper->getManager('CartManager');
        $this->userManager = $this->helper->getManager('UserManager');
        $this->addressManager = $this->helper->getManager('AddressManager');
    }
    /**
     * url = '/ma-commande/verification-de-l\'adresse', name = 'order_address_check'
     */
    public function address_check():Response
    {
        $user = $this->userManager->findOneOrNull(['id' => $_SESSION['id']]);
        //si on clique sur le bouton modifier, on efface l'adresse correspondante
        if(isset($_GET['modif']) AND in_array($_GET['modif'], ['adresse-de-livraison', 'adresse-de-facturation']))
        {
            $setter = $_GET['modif'] === 'adresse-de-facturation' ? 'setInvoice_address_id': 'setDelivery_address_id';
            $user->$setter(null);
        }
        //si une adresse a été choisie, on l'insère en base de donnée
        if(isset($_POST['delivery_address_id']))
        {
            $user->setDelivery_address_id($_POST['delivery_address_id']);
        }
        if(isset($_POST['invoice_address_id']))
        {
            $user->setInvoice_address_id($_POST['invoice_address_id']);
        }
        $this->userManager->persist($user);
        //on récupére le user après ça pour hydrater avec les bonnes adresses de livraison et facturation
        $user = $this->userManager->findOneOrNull(['id' => $_SESSION['id']]);
        //préparation des formulaires pour delivery_address et invoice_address
        $addresses_ids = array_map(function ($address) {
            return $address->getId();;
        }, $user->getAddresses());
        $addresses_html = array_map(function ($address) {
            return $address->toHtml();
        }, $user->getAddresses());


        $delivery_form = new Form(); 
        $delivery_form->getBuilder()->addChoice('radio', 'delivery_address_id', $addresses_ids, $addresses_html)
                                    ->addButton('valider', 'btn-primary');
        $invoice_form = new Form();
        $invoice_form->getBuilder()->addChoice('radio', 'invoice_address_id', $addresses_ids, $addresses_html)
                                    ->addButton('valider', 'btn-primary');

        //récupération du récapitulatif de commande                    
        $products = $this->cartManager->getProducts($user->getId());
        $invoice_preview = $this->invoiceManager->invoicePreview($products);

        return $this->render('order/address_check.php', [
            'title' => 'ma-commande : vérification de l\'adresse',
            'user' => $user,
            'invoice_preview' => $invoice_preview,
            'delivery_form' => $delivery_form->createView(),
            'invoice_form' => $invoice_form->createView()
        ]);
    }
    /**
     * url = '/ma-commande/paiement', name = 'order_payment'
     */
    public function payment():Response
    {
        $products = $this->cartManager->getProducts($_SESSION['id']);
        $invoice = $this->invoiceManager->invoicePreview($products);

        $user = $this->userManager->findOneOrNull(['id' => $_SESSION['id']]);

        $delivery_address = $this->addressManager->findOneOrNull(['id' => $user->getDelivery_address_id()]);
        $invoice_address = $this->addressManager->findOneOrNull(['id' => $user->getInvoice_address_id()]);

        return $this->render('order/payment.php', [
            'title' => 'ma-commande : paiement',
            'invoice' => $invoice,
            'delivery_address' => $delivery_address,
            'invoice_address' => $invoice_address
        ]);
    }
    /**
     * url = '/ma-commande/traitement-de-la-commande', name = 'order_processing'
     */
    public function processing():Response
    {
        try
        {
            //récupération de toutes les données nécessaires
            $products = $this->cartManager->getProducts($_SESSION['id']);
            $user = $this->userManager->findOneOrNull(['id' => $_SESSION['id']]);
            if($user->getDelivery_address_id() === null OR $user->getInvoice_address_id() === null)
            {
                throw new \Exception("Adresse manquante", 1);
            }
            $delivery_address = $this->addressManager->findOneOrNull(['id' => $user->getDelivery_address_id()]);
            $invoice_address = $this->addressManager->findOneOrNull(['id' => $user->getInvoice_address_id()]);

            //création de la facture et enregistrement dans la base de donnée
            $invoice = $this->invoiceManager->invoicePreview($products)
                                        ->setId($this->invoiceManager->createId())
                                        ->setInvoice_date((new \DateTime())->format('Y-m-d H:i:s'))
                                        ->setUser_id($_SESSION['id'])
                                        ->setDelivery_address(Tools::encode($delivery_address, ['civility', 'last_name', 'first_name', 'number', 'way', 'city', 'postal_code', 'country']))
                                        ->setInvoice_address(Tools::encode($invoice_address, ['civility', 'last_name', 'first_name', 'number', 'way', 'city', 'postal_code', 'country']))
                                        ->setStatus(1);
            $this->invoiceManager->persist($invoice);
        }
        catch(\Exception $e)
        {
            $_SESSION['flash']['danger'] = 'Un problème est survenu lors de la commande. Veuillez réessayer';

            return $this->redirect($this->router->url('cart_index'));
        }
        //création de la facture pdf    A VOIR PLUS TARD


        //email de confirmation de commande (facture pdf jointe)
        (new Notification())->orderConfirm($user->getEmail(), $this->router->url('orders_listing'), $invoice);

        //on vide le panier
        $this->cartManager->deleteAll(['user_id' => $_SESSION['id']]);
        //mise a jour des stocks
        foreach($products as $product)
        {
            $product->setStock($product->getStock() - $product->getQuantity());
            $this->productsManager->persist($product);
        }
        $_SESSION['flash']['success'] = 'La commande a bien été passée !';

        return $this->redirect($this->router->url('orders_listing'));
    }
}