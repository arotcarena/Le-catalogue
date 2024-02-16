<?php
namespace Vico\Controller;

use Vico\Auth;
use Vico\Tools;
use Vico\Helper;
use Vico\Response;
use Vico\Form\Form;
use Vico\UrlHelper;
use Vico\Models\User;
use Vico\Models\Login;
use Vico\Notification;
use Vico\Form\LoginForm;
use Vico\Form\SigninForm;
use Vico\Managers\UserManager;
use Vico\Models\PasswordUpdate;
use Vico\Validators\UserValidators\EmailValidator;
use Vico\Validators\UserValidators\PasswordInitValidator;

class AuthController extends Controller 
{
    /**
     * @var UserManager
     */
    private $manager;

    /**
     * @var UrlHelper
     */
    private $url_helper;

    public function __construct(Helper $helper)
    {
        parent::__construct($helper);
        $this->manager = $this->helper->getManager('UserManager');
        $this->url_helper = $this->helper->getUrlHelper();
    }

    /**
     * url = '/connexion', route = 'login'
     */
    public function login():Response 
    {
        //en cas de redirection depuis page signin
        if(isset($_GET['email']))
        {
            $_POST['email'] = $_GET['email'];
        }
        $login = new Login();
        $login_form = new LoginForm($this->manager, $this->helper, $login); 
        $login_form->getBuilder()
                    ->setAction($this->url_helper->modif_get($this->router->url('login'), null, ['email']));

        $login_form->handleRequest($_POST);
        if($login_form->isSubmitted() AND $login_form->isValid())     
        {
            $user = $this->manager->findOneOrNull(['email' => $login->getEmail()]); 
            if($login->getRemember())
            {
                $this->manager->remember($user);
            }
            
            Auth::login($user);

            $user->setLast_login((new \DateTime())->format('Y-m-d'));
            $this->manager->persist($user);

            $target = $_GET['target'] ?? $this->router->url('products_index');
            $_SESSION['flash']['success'] = 'Vous êtes connecté. '.$_SESSION['last_login'];

            return $this->redirect($this->url_helper->modif_get($target, null, ['target']));
        }

        return $this->render('auth/login.php', [
            'title' => 'connexion',
            'login_form' => $login_form->createView()->setBtn_inline(true)
        ]);
    }
    /**
     * url = '/deconnexion', route = 'logout'
     */
    public function logout():Response 
    {
        Auth::logout();
        $_SESSION['flash']['success'] = 'Vous avez bien été déconnecté.';
        $target = $_GET['target'] ?? $this->router->url('products_index');
        return $this->redirect($target);
    }
    /**
     * url = '/inscription', route = 'signin'
     */
    public function signin():Response 
    {
        $user = new User();
        $form = new SigninForm($this->manager, $this->url_helper, $this->router, $user);
        
        $form->handleRequest($_POST);
        if($form->isSubmitted() AND $form->isValid())
        {
            $user->encryptPassword();
            $id = $this->manager->persist($user);
            $user->setId($id);
            
            $this->manager->sendWelcomeToken($user, $this->router);
            $_SESSION['flash']['success'] = 'Votre inscription a bien été enregistrée. Un email vous a été envoyé pour finaliser votre inscription.';
            
            return $this->redirect($this->router->url('login'));
        }
        return $this->render('auth/signin.php', [
            'title' => 'inscription',
            'form' => $form->createView()
        ]);
    }
    /**
     * url = 
     */
    public function new_welcomeToken(array $params):Response
    {
        $user = $this->manager->findOneOrNull(['id' => $params['id']]);
        $this->manager->sendWelcomeToken($user, $this->router);
        $_SESSION['flash']['success'] = 'Un email vous a été envoyé pour finaliser votre inscription.';
        
        return $this->redirect($this->router->url('login'));
    }
    /**
     * url = '/autorisation-refusee', route = 'login_change'
     */
    public function login_change():Response 
    {
        return $this->render('auth/login_change.php');
    }
    /**
     * url = '/confirmation-de-compte', route = 'confirm_account'
     */
    public function confirm_account():Response 
    {
        if($this->manager->verify_token($_GET))
        {
            $user = $this->manager->findOneOrNull(['id' => $_GET['id']]);
            $user->setConfirmed_at((new \DateTime())->format('Y-m-d H:i:s'));
            $this->manager->persist($user);
            $_SESSION['flash']['success'] = 'Votre adresse e-mail a bien été confirmée. Vous pouvez désormais vous connecter.';
            return $this->redirect($this->url_helper->modif_get($this->router->url('login'), null, null, ['target']));
        }
        $_SESSION['flash']['danger'] = 'Ce lien de vérification n\'est pas valide.';
        return $this->redirect('/page-introuvable');   
    }
    /**
     * url = '/mot-de-passe-oublie', route = 'forgot_password'
     */
    public function forgot_password():Response 
    {
        $model = new User();
        $form = (new Form($model))
                ->setValidator(new EmailValidator($this->manager))
                ->handleRequest($_POST);
        if($form->isSubmitted() AND $form->isValid())
        {
            $token = Tools::token(60);
            $user = $this->manager->findOneOrNull(['email' => $model->getEmail()]);
            $user->setConfirmation_token($token)
                ->setConfirmation_token_expire((new \DateTime())->add(new \DateInterval('PT10M'))->format('Y-m-d H:i:s'));
            $this->manager->persist($user);
            
            $link = $this->url_helper->modif_get($this->router->url('init_password'), ['id' => $user->getId(), 'token' => $token]);
            (new Notification())->init_passwordEmail($user->getEmail(), $link);  
            $_SESSION['flash']['success'] = 'Un lien pour réinitialiser votre mot de passe vous a été envoyé par email';
        }
        $form->getBuilder()
                ->addInput('text', 'email', 'Entrez votre adresse e-mail')
                ->addButton('Valider', 'btn-primary');

        return $this->render('auth/forgot_password.php', [
            'form' => $form->createView()
        ]);
    }
    /**
     * url = '/reinitialiser-le-mot-de-passe', route = 'init_password'
     */
    public function init_password():Response 
    {
        if(!$this->manager->verify_token($_GET))
        {
            $_SESSION['flash']['danger'] = 'Le lien utilisé n\'est pas valide';
            return $this->redirect('/page-inconnue');
        }
        $model = new PasswordUpdate();
        $form = (new Form($model))
                ->setValidator(new PasswordInitValidator())
                ->handleRequest($_POST);
        if($form->isSubmitted() AND $form->isValid())
        {
            $user = $this->manager->findOneOrNull(['id' => $_GET['id']]);
            $user->setPassword($model->getNew_password())
                    ->encryptPassword();
            $_SESSION['flash']['success'] = 'Votre mot de passe a bien été réinitialisé.';
            return $this->redirect($this->url_helper->modif_get($this->router->url('login'), null, null, ['target']));
        }
        $form->getBuilder()
                ->addInput('password', 'new_password', 'Nouveau mot de passe')
                ->addInput('password', 'password_confirm', 'Confirmez le mot de passe')
                ->addButton('Valider', 'btn-primary');

        return $this->render('auth/init_password.php', [
            'form' => $form->createView()
        ]);
    }
    
}
