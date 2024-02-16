<?php
namespace Vico\Controller;


use Vico\Auth;
use Vico\Helper;
use Vico\Response;
use Vico\Form\UserForm;
use Vico\Models\Address;
use Vico\Form\AddressForm;
use Vico\Form\PasswordForm;
use Vico\Managers\UserManager;
use Vico\Models\PasswordUpdate;
use Vico\Managers\InvoiceManager;


class AccountController extends Controller
{
    /**
     * @var UserManager
     */
    private $manager;

    public function __construct(Helper $helper)
    {
        Auth::check('user');
        parent::__construct($helper);
        $this->manager = $this->helper->getManager('UserManager');
    }
    /**
     * path =  '/mon-compte'   name= 'details'  
     */
    public function details():Response
    {
        $user = $this->manager->findOneOrNull(['id' => $_SESSION['id']]);
        return $this->render('my_account/details.php', [
            'user' => $user
        ]);
    }
    /**
     * url= 
     */
    public function address_listing(?array $params = []):Response
    {
        /** @var AddressManager */
        $addressManager = $this->helper->getManager('AddressManager');
        $url_helper = $this->helper->getUrlHelper();
        $user = $this->manager->findOneOrNull(['id' => $_SESSION['id']]);
        $addresses = $user->getAddresses();

        if(isset($_POST['delete']))
        {
            //vérification du csrf
            $addressManager->delete($_POST['delete']);
            $_SESSION['flash']['success'] = 'L\'adresse a bien été supprimée !';
            return $this->redirect($url_helper->modif_get($this->router->url('address_listing')));
        }
        $address = isset($params['update']) ? $addressManager->findOneOrNull(['id' => $params['update']]): new Address();
        $action = isset($params['update']) ? 'Modifier': '+ Ajouter';

        $form = new AddressForm($address);
        $form->getBuilder()
                ->addButton($action, 'btn-primary');

        $form->handleRequest($_POST);
        

        if($form->isSubmitted() AND $form->isValid())
        {
            if(empty($address->getId()))
            {  
                $address->setUser_id($_SESSION['id']);
                $addressManager->persist($address);
                $_SESSION['flash']['success'] = 'L\'adresse a bien été ajoutée !';
                return $this->redirect($url_helper->modif_get($this->router->url('address_listing')));
            }
            else
            {
                $addressManager->persist($address);
                $_SESSION['flash']['success'] = 'L\'adresse a bien été modifiée !';
                return $this->redirect($url_helper->modif_get($this->router->url('address_listing')));
            }
        }

        return $this->render('my_account/address_listing.php', [
            'addresses' => $addresses,
            'form' => $form->createView(),
            'params' => $params
        ]);
    }
    /**
     * url = 
     */
    public function orders_listing():Response
    {
        /** @var InvoiceManager */
        $invoiceManager = $this->helper->getManager('InvoiceManager');
        $pagination = $invoiceManager->findPaginated(array_merge($_GET, ['user_id' => $_SESSION['id']]));
        $current_status = (int)($_GET['status'] ?? 0);

        return $this->render('my_account/orders_listing.php', [
            'invoiceManager' => $invoiceManager,
            'pagination' => $pagination,
            'current_status' => $current_status
        ]);
    }

    /**
     * path = '/mon-compte/modifier-mes-informations-personnelles'  name = 'update_user'
     */
    public function update_user()
    {
        $user = $this->manager->findOneOrNull(['id' => $_SESSION['id']]);

        $form = new UserForm($this->manager, $user);

        $form->handleRequest($_POST);

        if($form->isSubmitted() AND $form->isValid())
        {
            $this->manager->persist($user);   
            $_SESSION['flash']['success'] = 'Votre profil a bien été mis à jour !';
            return $this->redirect($this->router->url('details'));
        }
        
        return $this->render('my_account/update_details.php', [
            'form' => $form->createView()
        ]);
    }

    /**
     * path = '/mon-compte/changer-de-mot-de-passe'  name = 'update_password'
     */
    public function update_password()
    {
        $user = $this->manager->findOneOrNull(['id' => $_SESSION['id']]);

        $model = new PasswordUpdate();
        $form = new PasswordForm($user, $model);

        $form->handleRequest($_POST);

        if($form->isSubmitted() AND $form->isValid())
        {
            $user->setPassword($model->getNew_password())
                    ->encryptPassword();
            $this->manager->persist($user);    
            $_SESSION['flash']['success'] = 'Votre mot de passe a bien été mis à jour !';
            return $this->redirect($this->router->url('details'));
        }
        
        return $this->render('my_account/update_details.php', [
            'form' => $form->createView()
        ]);
         
    }
    /**
     * path = 
     */
    public function delete():Response
    {
        $user = $this->manager->findOneOrNull(['id' => $_SESSION['id']]);

        $user->setDelete_at((new \DateTime())->format('Y-m-d H:i:s'));
        //on archive l'utilisateur
        ($this->helper->getManager('ArchivedUserManager'))->persist($user);
        //on le supprime
        $this->manager->delete($_SESSION['id']);

        $_SESSION['flash']['success'] = 'Votre compte a bien été supprimé.';
        return $this->redirect($this->router->url('home'));
    }
}




