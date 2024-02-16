<?php

use Vico\Auth;
use Vico\Tools;
use Vico\Managers\UserManager;


Auth::check('admin');



$pagination = (new UserManager())->findPaginated($_GET);



?>



 <?= require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR . 'user_filters.php' ?> <!-- POUR EFFACER LE 1 QUI SORT DE NULLE PART --></p>



    <?= $pagination->getCountFormated() ?>
    <?= $pagination->links() ?>


<?php if($pagination->getCount() > 0): ?>
    <table class="table">
        <thead>
            <tr>
                <th>#id</th>
                <th>Adresse e-mail</th>
                <th>Prénom / Nom</th>
                <th>Adresse de facturation</th>
                <th>Adresse de livraison</th>
                <th>date d'inscripton</th>
                <th>dernière connexion</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pagination->getItems() as $user): ?>
                <tr>
                    <td><?= "#".$user->getId() ?></td>
                    <td><?= $user->getEmail() ?></td>
                    <td><?= $user->getFirst_name().' '.$user->getLast_name() ?></td>
                    <td>
                        <?php if($user->getInvoice_address()): ?>
                            <?= $user->getInvoice_address()->toHtml() ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if($user->getDelivery_address()): ?>
                            <?= $user->getDelivery_address()->toHtml() ?>
                        <?php endif ?>
                    </td>
                    <td><?= Tools::format_sql_date($user->getConfirmed_at(), 'date') ?></td>
                    <td><?= Tools::format_sql_date($user->getLast_login(), 'date') ?></td>
                    <td>
                        <a href="<?= $router->url('admin_users_single', ['id' => $user->getId(), 'status' => 0]) ?>" class="btn btn-primary">Voir plus</a>
                    </td>
                    <td>
                        <?php if($user->getRole() === 'admin'): ?>
                            <button type="button" class="btn btn-outline-info disabled">Administrateur</button>
                        <?php elseif($user->getInactive()): ?>
                            <button type="button" class="btn btn-outline-danger disabled">Compte désactivé</button>
                        <?php elseif($user->getConfirmed_at() === null): ?>
                            <button type="button" class="btn btn-outline-dark disabled">En cours d'activation</button>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>

    </table>
<?php endif ?>

    <?= $pagination->links() ?>
