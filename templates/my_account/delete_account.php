<?php

use Vico\Auth;
use Vico\Managers\UserManager;
use Vico\Managers\AddressManager;
use Vico\Managers\ArchivedUserManager;

Auth::check('user');

$userManager = new UserManager();
$user = $userManager->findOneOrNull(['id' => $_SESSION['id']]);
$user->setDelete_at((new DateTime())->format('Y-m-d H:i:s'));

//on archive l'utilisateur
(new ArchivedUserManager())->insert($user);

//on le supprime
$userManager->delete(['id' => $_SESSION['id']]);

//on supprime les adresses qui correspondent a cet utilisateur
(new AddressManager())->delete(['user_id' => $_SESSION['id']]);


$_SESSION['flash']['success'] = 'Votre compte a bien été supprimé.';
header('Location: '.$router->url('logout'));
exit();



