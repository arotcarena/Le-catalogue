<?php

use Vico\Nav;
use Vico\Auth;
use Vico\Tools;
use Vico\Managers\UserManager;
use Vico\Managers\InvoiceManager;

Auth::check('admin');




$userManager = new UserManager();
$invoiceManager = new InvoiceManager();

if(isset($_GET['inactive']))
{
    $userManager->insert(['inactive' => 1], ['id' => $params['id']]);
}
elseif(isset($_GET['active']))
{
    $userManager->insert(['inactive' => 0], ['id' => $params['id']]);
}


$user = $userManager->findOneOrNull($params);

if(isset($params['order_id']))
{
    $invoice = $invoiceManager->findOneOrNull(['id' => $params['order_id']]);
}
else
{   
    $pagination = $invoiceManager->findPaginated(array_merge($_GET, ['user_id' => $user->getId()]));
    $current_status = (int)($_GET['status'] ?? 0);
}

?>

<div class="mt-4"><a href="<?= $router->url('admin_users_listing') ?>">Retour au listing des utilisateurs</a></div>

<div class="mt-4">
    <table class="table">
        <tr><th>#id</th><td>#<?= $user->getId() ?></td>
        <?php if($user->getLast_login()): ?>
            <tr><th>Dernière connexion</th><td><?= Tools::format_sql_date($user->getLast_login(), 'date') ?></td>
        <?php endif ?>
    </table>

    <?php if($user->getRole() === 'admin'): ?>
        <p>
            <button type="button" class="btn btn-outline-info disabled">Administrateur</button>
        </p>
    <?php elseif($user->getInactive()): ?>
        <p>
            <button type="button" class="btn btn-danger disabled">Compte désactivé</button>
            <a href="?active=1" class="btn btn-outline-success">Activer</a>
        </p>
    <?php elseif($user->getConfirmed_at() === null): ?>
        <p>
            <button type="button" class="btn btn-secondary disabled">En cours d'activation</button>
        </p>
    <?php else: ?>
        <p>
            <button type="button" class="btn btn-success disabled">Compte activé</button>
            <a href="?inactive=1" class="btn btn-outline-danger">Désactiver</a>
        </p>
    <?php endif ?>
    
    <table class="table">
        <thead><th>Informations personnelles</th></thead>
        <tbody>
            <?php if($user->getFirst_name() OR  $user->getLast_name()): ?>
                <tr><th>Prénom / Nom</th><td><?= $user->getFirst_name() .' '. $user->getLast_name() ?></td>
            <?php endif ?>
            <tr><th>Adresse email</th><td><?= $user->getEmail() ?></td>
            <?php if($user->getConfirmed_at()): ?>
                <tr><th>Date d'inscription</th><td><?= Tools::format_sql_date($user->getConfirmed_at(), 'date') ?></td>
            <?php endif ?>
        </tbody>
    </table>

</div>

<!--COMMANDES-->


    <?php if(isset($invoices)): ?>
        <nav class="navbar navbar-expand">
            <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?php $style = $current_status === 0 ? 'font-weight: bold;': '' ?>
                        <?= Nav::link($router->url('admin_users_single', ['id' => $user->getId()]), 'Toutes les commandes ('.$invoiceManager->count(['user_id' => $_SESSION['id']]).')', 'match_3', '" style="'.$style.'"') ?>
                    </li>
                <?php foreach([1 => 'en préparation', 2 => 'expédiées', 3 => 'livrées'] as $status => $label): ?>
                    <?php $style = $current_status === $status ? 'font-weight: bold;': '' ?>
                    <li class="nav-item">
                        <?= Nav::link($router->url('admin_users_single', ['id' => $user->getId()]).'?status='.$status, 'Commandes '.$label.' ('.$invoiceManager->count(['status' => $status, 'user_id' => $_SESSION['id']]).')', 'match_3', '" style="'.$style.'"') ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </nav>
    <?php endif ?>

    <?php if(!empty($invoices)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Prix total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $invoice): ?>
                    <tr>
                        <th><?= $invoice->getId() ?></th>
                        <td><?= $invoice->getInvoice_date_formated() ?></td>
                        <td><?= $invoice->getTotal_price_formated() ?></td>
                        <td><a href="<?= $router->url('admin_users_single_order', ['order_id' => $invoice->getId(), 'id' => $user->getId()]) ?>" class="btn btn-primary">Voir plus</a></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?= $pagination->links() ?>

    <?php elseif(isset($invoice)): ?>
        <div class="row align-items-start mt-4">
            <div class="col">
                <?= $invoice->toHtml() ?>
            </div>
            <div class="col mt-4">
                <a href="<?= $router->url('admin_users_single', ['id' => $user->getId(), 'status' => 0]) ?>" class="btn btn-outline-primary">Fermer X</a>
            </div>
        </div>
    <?php endif ?>


<br>
<br>

<!--ADRESSES-->
<?php if(!empty($user->getAddresses())): ?>
<h5>Toutes les adresses</h5>
    
    <div class="row align-items-start mt-4">
        <?php foreach($user->getAddresses() as $address): ?>
            <div class="col">
                <?= $address->toHtml() ?>

                <?php if($address->getId() === $user->getDelivery_address_id()): ?>
                    <p><button type="button" class="btn btn-outline-info disabled">Adresse de livraison</button></p>
                <?php elseif($address->getId() === $user->getInvoice_address_id()): ?>
                    <p><button type="button" class="btn btn-outline-secondary disabled">Adresse de facturation</button></p>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>