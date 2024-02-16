<?php
namespace Vico;

use Vico\Tools;
use Vico\Config;
use Vico\UrlHelper;
use Vico\Models\User;
use Vico\Managers\UserManager;
use Vico\Exceptions\LogoutException;
use Vico\Exceptions\ForbiddenException;

class Auth
{
    private static $userManager;

    public static function check(string ...$roles):bool
    {
        if(isset($_SESSION['id']))
        {
            $user = (new UserManager())->findOneOrNull(['id' => $_SESSION['id']]);
            if($user AND in_array($user->getRole(), $roles))
            {
                return true;
            }
        }
        throw new ForbiddenException($_SERVER['REQUEST_URI']);
    }
    public static function login(User $user):void
    {
        $_SESSION['id'] = $user->getId();
        $_SESSION['last_activity'] = time();
        $_SESSION['last_login'] = $user->getLast_login_formated();
    }
    public static function logout():void
    { 
        unset($_SESSION['id']);
        if(isset($_COOKIE['remember']))
        {
            setcookie('remember', 'null', time() - 1);
        }
    }
    public static function auto_logout():void 
    {
            $user = (new UserManager())->findOneOrNull(['id' => $_SESSION['id']]);
            if(!$user OR $user->getInactive())
            {
                self::logout();
                $_SESSION['flash']['danger'] = 'Vous avez été déconnecté suite à un problème inconnu.';
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit();
            }
            if(isset($_SESSION['last_activity']) AND ($_SESSION['last_activity'] + Config::AUTO_LOGOUT_TIME) < time() AND !isset($_COOKIE['remember']))
            {
                self::logout();
                $_SESSION['flash']['danger'] = 'Vous avez été déconnecté suite à une trop longue inactivité';
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit();
            }
            $_SESSION['last_activity'] = time(); 
    }
    public static function auto_login():void 
    {
        if(isset($_COOKIE['remember']) AND filter_var(explode('==', $_COOKIE['remember'])[0], FILTER_VALIDATE_INT))
        {
            $user = (new UserManager())->findOneOrNull(['id' => explode('==', $_COOKIE['remember'])[0]]);
            if($user AND !$user->getInactive() AND $user->getId().'=='.$user->getRemember_token().'-secret-code-0000' === $_COOKIE['remember'])
            {
                self::login($user);
                \setcookie('remember', $_COOKIE['remember'], time() + 3600 * 48);
            }
        }
        
    }
    
    
    
}