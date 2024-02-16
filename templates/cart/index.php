
<h1>Mon Panier</h1>



<?php if(!empty($products)): ?>



    <p>Votre panier comporte <?= count($products) ?> article(s)</p>

    
    <a href="<?= $router->url('products_index') ?>">Continuer mes achats</a>


    <table class="table table-striped">
        <thead>
            <tr>
                <th>photo</th>
                <th>marque</th>
                <th>modèle</th>
                <th>description</th>
                <th>prix</th>
                <th>en stock</th>
                <th>quantité</th>
                <th>sous-total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            
                <?php $total_price = 0 ?>

            <?php foreach($products as $product): ?>
            
                <?php $total_price += $product->getPrice() * $product->getQuantity() ?>

            <tr>
                <td><img src="../img/<?= $product->getImg() ?>.jpg" alt="image"/></td>
                <td><?= $product->getBrand() ?></td>
                <td><?= $product->getModel() ?></td>
                <td><?= $product->getDescription() ?></td>
                <td><?= $product->getPriceFormated() ?></td>

                <?php if(isset($_GET['id']) AND (int)$_GET['id'] === $product->getId()): ?>
                <td><span class="alert alert-danger"><?= $product->getStock() ?></span></td>
                <?php else: ?>
                    <td><?= $product->getStock() ?></td>
                <?php endif ?>

                <td>
                    <a href="<?= $router->url('cart_update', ['product_id' => $product->getId(), 'quantity' => 'less']).'?cart=1' ?>" class="btn btn-secondary">-</a>
                        <?=  $product->getQuantity()  ?>
                        <a href="<?= $router->url('cart_update', ['product_id' => $product->getId(), 'quantity' => 1]).'?cart=1' ?>" class="btn btn-secondary">+</a>
                </td>
                <td>
                    <?= $product->getTotalFormated() ?>
                </td>

                <td>
                        <a href="<?= $router->url('products_show', ['id' => $product->getId(), 'slug' => $product->getSlug()]) ?>" class="btn btn-primary">Voir plus</a>
                        <a href="<?= $router->url('cart_del', ['del_id' => $product->getId()]) ?>" class="btn btn-secondary" 
                            onclick="return confirm('Voulez-vous vraiment supprimer cet article de votre panier?')">
                            Supprimer
                        </a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>







    <div class="row align-items-start mb-4">
        <div class="col">
        
        </div>
        <div class="col">
        
        </div>
        <div class="col">
        <h5>Total : </h5>
        </div>
        <div class="col" style="font-size: 1.3em">
            <h4><?= number_format($total_price, 0, '', ' ').' €' ?></h4>
        </div>
        <div class="col">
            <?php if($total_price > 0): ?>
            <a class="btn btn-primary" style="font-size: 1.3em;" href="<?= $router->url('order_address_check') ?>">Passer commande</a>
            <?php endif ?>
        </div>
    </div>




<?php else: ?>
    <p>Votre panier est vide</p>
<?php endif ?>

    
<a href="<?= $router->url('products_index') ?>">Continuer mes achats</a>