


<div class="m-4">
    <?php if(empty($user->getAddresses())): ?>
        <h4>Aucune adresse de livraison</h4>  
        <a href="<?= $router->url('address_listing').'?address_check=1' ?>" class="btn btn-primary mt-2"> + Ajouter une adresse</a> 
    <?php else: ?>

        <!--ADRESSE DE LIVRAISON !-->
        <?php if($user->getDelivery_address() !== null): ?>
            <h4>Adresse de livraison</h4>
            <?= $user->getDelivery_address()->toHtml() ?>
            <p>
                <a href="<?= $url_helper->modif_get($router->url('order_address_check'), ['modif' => 'adresse-de-livraison']) ?>" class="btn btn-primary mt-2 mb-2">Modifier</a>
            </p> 
        <?php else: ?>
            <h4>Choisissez une adresse de livraison</h4>
            <?= $delivery_form->view() ?>
            <a href="<?= $router->url('address_listing').'?address_check=1' ?>" class="btn btn-primary mt-2"> + Ajouter une adresse</a>
        <?php endif ?>
            
        <!--ADRESSE DE FACTURATION !-->
        <?php if($user->getInvoice_address() !== null): ?>
            <h4>Adresse de facturation</h4>
            <?= $user->getInvoice_address()->toHtml() ?>
            <p>
                <a href="<?= $url_helper->modif_get($router->url('order_address_check'), ['modif' => 'adresse-de-facturation']) ?>" class="btn btn-primary  mt-2 mb-2">Modifier</a>
            </p> 
        <?php else: ?>
            <h4>Choisissez une adresse de facturation</h4>
            <?= $invoice_form->view() ?>
            <a href="<?= $router->url('address_listing').'?address_check=1' ?>" class="btn btn-primary mt-2"> + Ajouter une adresse</a>
        <?php endif ?>

    <?php endif ?>   
</div>



<?= $invoice_preview->toHtml() ?>

    <a href="<?= $router->url('cart_index') ?>" class="btn btn-secondary m-2">Revenir au panier</a>
<?php if(!empty($user->getInvoice_address()) AND !empty($user->getDelivery_address())): ?>
    <a href="<?= $router->url('order_payment') ?>" class="btn btn-primary">Valider et payer</a>
<?php endif ?>




