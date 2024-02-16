


<nav class="navbar navbar-expand">
    <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <?php $style = $current_status === 0 ? 'font-weight: bold;': '' ?>
                <?= $nav->link($router->url('orders_listing'), 'Toutes les commandes ('.$invoiceManager->count(['user_id' => $_SESSION['id']]).')', 'match_3', '" style="'.$style.'"') ?>
            </li>
        <?php foreach([1 => 'en préparation', 2 => 'expédiées', 3 => 'livrées'] as $status => $label): ?>
            <?php $style = $current_status === $status ? 'font-weight: bold;': '' ?>
            <li class="nav-item">
                <?= $nav->link($router->url('orders_listing').'?status='.$status, 'Commandes '.$label.' ('.$invoiceManager->count(['status' => $status, 'user_id' => $_SESSION['id']], 'and').')', 'match_3', '" style="'.$style.'"') ?>
            </li>
        <?php endforeach ?>
    </ul>
</nav>







<?php foreach($pagination->getItems() as $invoice): ?>

    <div><?= $invoice->toHtml() ?></div><br>

<?php endforeach ?>
    

<?= $pagination->links() ?>


