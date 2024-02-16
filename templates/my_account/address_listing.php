





    <div class="row align-items-start">
    <div class="col">
        <div class="m-4">

            <?php foreach($addresses as $address): ?>
                <?php 
                $class = '';
                $disabled = '';
                if(isset($params['update']) AND (int)$params['update'] === $address->getId())
                {
                    $class = 'fw-light';
                    $disabled = 'disabled';
                }
                ?>
                    <p class="<?= $class ?>">
                        <?= $address->toHtml() ?>
                    </p>
                    <a href="<?= $url_helper->modif_get($router->url('address_update', ['update' => $address->getId()]), null, null, ['address_check']) ?>" 
                        class="btn btn-primary mb-4 <?= $disabled ?>">Modifier</a>
                    <form action="" method="post" style="display: inline;">
                        <button name="delete" value="<?= $address->getId() ?>" class="btn btn-secondary mb-4">Supprimer</button>
                    </form>
            <?php endforeach ?>
        </div>

        <?php if(isset($params['update'])): ?>
            <div class="m-4">
                <a href="<?= $url_helper->modif_get($router->url('address_listing')) ?>" 
                            class="btn btn-primary mb-4">+ Ajouter</a>
            </div>
        <?php endif ?>

    </div>
    <div class="col">
            <?= $form->view() ?>
    </div>
    <div class="col">
        <?php if(isset($_GET['address_check']) AND !empty($addresses)): ?>
            <a href="<?= $router->url('order_address_check') ?>" class="btn btn-primary">Finaliser ma commande</a>
        <?php endif ?>
    </div>
  </div>

