<?php
namespace Vico;

use Vico\Router;

class Config 
{
    //adresse du site (a modifier pour passer en production)
    public const APP_URL = 'http://localhost:8000';
    //adresse email pour les envois d'email auto
    public const APP_SEND_MAIL = 'no-reply@vector.com';
    //port email
    public const MAIL_HOST = 'smtp://localhost:1025';

    //DATABASE CONFIG
    public const DATA_BASE = 'mysql';

    public const DBNAME = 'vector';

    public const HOST = 'localhost';

    public const DB_ID = 'root';

    public const DB_PASSWORD = 'root';

    public const PDO_ATTR_ERRMODE = \PDO::ERRMODE_EXCEPTION;

    //en secondes
    public const AUTO_LOGOUT_TIME = 300;
    //en secondes
    public const CODE_2FA_EXPIRE = 300;


    //TEMPLATES DIRECTORIES
    public static function templates_directory():string 
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
    }
    public static function layouts_directory():string 
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR;
    }

    //LAYOUTS
    public const LAYOUTS = [
        '/admin' => 'admin',
        '/mon-compte' => 'my_account'
    ];

    public const DEFAULT_LAYOUT = 'default';

    //IMAGES PATH
    public static function image_path():string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'image';
    }
    //product's images directory 
    public static function product_image_path():string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . 'product';
    } 
    //relatives path
    public const IMAGE_URL= '../../image/';
    public const PRODUCT_IMAGE_URL = '../../image/product/';



    /**
     * AUTO REDIRECTIONS
     */
    public const AUTO_REDIRECTIONS = [

        //REDIRECTION PAGE D ACCUEIL ADMIN
        [   
            'current_url' => '/admin',
            'redirection_url' => '/admin/listing-des-produits'
            ]
        // AUTRES REDIRECTIONS A RAJOUTER ICI
            
    ];
    
    /**
     * ROUTES
     * [method, url, controller::method, name]
     * @var array
     */
    public const ROUTES = [
        //ADMIN
        //gestion des produits
        ['GET|POST', '/admin/[*:slug]/ref-[i:id]', 'Admin\AdminProductsController::edit', 'admin_products_edit'],
        ['GET', '/admin/suppr-[i:del_id]', 'Admin\AdminProductsController::delete', 'admin_products_delete'],
        ['GET|POST', '/admin/nouveau-produit', 'Admin\AdminProductsController::new', 'admin_products_new'],
        ['GET', '/admin/listing-des-produits', 'Admin\AdminProductsController::index', 'admin_products_index'],
        //utilisateurs
        ['GET|POST', '/admin/gerer-les-utilisateurs/id-[i:id]/commande-[*:order_id]', 'admin/users/single', 'admin_users_single_order'],
        ['GET|POST', '/admin/gerer-les-utilisateurs/id-[i:id]/[i:status]', 'admin/users/single', 'admin_users_single'],
        ['GET', '/admin/gerer-les-utilisateurs', 'admin/users/listing', 'admin_users_listing'],
        //contact
        ['GET|POST', '/admin/nous-contacter', 'admin/contact', 'admin_contact'],
        //commandes
        ['GET|POST', '/admin/gerer-les-commandes/[i:status]', 'admin/orders', 'admin_orders'],
        //paniers
        ['GET', '/admin/voir-les-paniers', 'admin/carts', 'admin_carts'],


        //MON-COMPTE
        ['GET', '/mon-compte', 'AccountController::details', 'details'],
        ['GET|POST', '/mon-compte/modifier-mes-informations-personnelles', 'AccountController::update_user', 'update_user'],
        ['GET|POST', '/mon-compte/changer-de-mot-de-passe', 'AccountController::update_password', 'update_password'],
        ['POST', '/mon-compte/suppr', 'AccountController::delete', 'delete_account'],
        //mon-compte/mes-adresses
        ['GET|POST', '/mon-compte/mes-adresses', 'AccountController::address_listing', 'address_listing'],
        ['GET|POST', '/mon-compte/mes-adresses/modifier-une-adresse/[i:update]', 'AccountController::address_listing', 'address_update'],
        //mon-compte/mes-commandes
        ['GET', '/mon-compte/mes-commandes', 'AccountController::orders_listing', 'orders_listing'],


        //
        //ma-commande
        ['GET', '/ma-commande/traitement-de-la-commande', 'OrderController::processing', 'order_processing'],
        ['GET', '/ma-commande/paiement', 'OrderController::payment', 'order_payment'],
        ['GET|POST', '/ma-commande/verification-de-l\'adresse', 'OrderController::address_check', 'order_address_check'],
        //mon-panier
        ['GET|POST', '/mon-panier/suppr/ref-[i:del_id]', 'CartController::delete', 'cart_del'],
        ['GET', '/mon-panier/modif/ref-[i:product_id]-quant-[*:quantity]', 'CartController::update', 'cart_update'],
        ['GET|POST', '/mon-panier', 'CartController::index', 'cart_index'],

        //auth
        ['GET|POST', '/reinitialiser-le-mot-de-passe', 'AuthController::init_password', 'init_password'],   
        ['GET|POST', '/mot-de-passe-oublie', 'AuthController::forgot_password', 'forgot_password'],  
        ['GET', '/confirmation-de-compte', 'AuthController::confirm_account', 'confirm_account'],
        ['GET', '/autorisation-refusee', 'AuthController::login_change', 'login_change'],
        ['GET', '/demander-nouvel-email-confirmation/[i:id]', 'AuthController::new_welcomeToken', 'new_welcomeToken'],
        ['GET|POST', '/inscription', 'AuthController::signin', 'signin'],
        ['GET', '/deconnexion', 'AuthController::logout', 'logout'],
        ['GET|POST', '/connexion', 'AuthController::login', 'login'],

        //contact
        ['GET', '/nous-contacter', 'ContactController::index', 'contact'],
        //error
        ['GET', '/erreur', 'ErrorController::index', 'unknown_error'],

        //catalogue
        ['GET', '/[*:slug]/ref-[i:id]', 'ProductsController::show', 'products_show'],
        ['GET', '/', 'ProductsController::index', 'products_index']     
    ];
}


