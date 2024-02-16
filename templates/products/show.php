

<div><a class="btn btn-outline-primary" href="<?= $router->url('products_index') ?>"><<< Retour au listing des produits</a></div>

<br>

<div class="row align-items-center mt-4">
    <div class="col-7">
        <?= $imagePagination->view() ?>
    </div>


    <div class="col">
        <h4><strong><?= $product->getBrand() ?></strong> <?= $product->getModel() ?></h4>
        <h4 style="font-weight: bold; font-size: 3em"><?= $product->getPriceFormated() ?></h4>
        <div><?= $product->getDescription() ?></div>

        <?php $class = isset($_GET['no_stock']) ? 'alert alert-danger': '' ?>
        <div class="mt-4 <?= $class ?>">En stock : <?= $product->getStock() ?></div>

        <div class="mt-4">Quantité :

            <a href="?quant=<?= $quant > 1 ? $quant - 1: 1 ?>" class="btn btn-secondary">-</a>
                            <?= $quant ?>
            <?php if($quant < $product->getStock()): ?>
            <a href="?quant=<?= $quant + 1 ?>" class="btn btn-secondary">+</a>
            <?php else: ?>
            <a href="?quant=<?= $quant ?>&no_stock=1" class="btn btn-secondary">+</a>
            <?php endif ?>
            
            <p class="mt-4" style="font-size: 1.3em">total : <span style="font-weight: bold; font-size: 1.5em"><?= number_format($product->getPrice() * $quant, 0, '', ' ')  ?> €</span></p>
        
            <div class="mt-4">
                <a class="btn btn-primary mt-4" href="<?= $router->url('cart_update', ['product_id' => $product->getId(), 'quantity' => $quant]) ?>" 
                onclick="return confirm('Voulez-vous vraiment ajouter cet article à votre panier?')">
                    Ajouter au panier
                </a>
            </div>
        </div>
    </div>
</div>






