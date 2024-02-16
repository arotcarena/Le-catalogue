


<h1>Catalogue</h1>


<?php require '_searchFilters.php' ?>

<table class="table mt-4"><tbody><tr><td></td></tr></tbody></table>



<div class="mb-4 mt-4"><?= $pagination->getCountFormated() ?></div>

<?= $pagination->links() ?>



<table class="table table-striped">
    <thead>
        <tr>
            <th>photo</th>
            <th>marque</th>
            <th>modèle</th>
            <th>description</th>
            <th>prix</th>
            <th>en stock</th>
            <th>Catégorie</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($pagination->getItems() as $product): ?>
        <tr>
            <td><img style="max-width: 100%;" src="<?= $product->getFirstImage_Url('mini') ?>" alt="image"/></td>
            <td><?= $product->getBrand() ?></td>
            <td><?= $product->getModel() ?></td>
            <td><?= $product->getDescription() ?></td>
            <td><?= $product->getPriceFormated() ?></td>
            <td><?= $product->getStock() ?></td>
            <?php if($product->getCategory() !== null): ?>
                <td><button class="btn btn-outline-<?= $product->getCategory()->color() ?> disabled"><?= $product->getCategory()->getName() ?></button></td>
            <?php endif ?>
            <td>
                <a href="<?= $router->url('products_show', ['id' => $product->getId(), 'slug' => $product->getSlug()]) ?>" class="btn btn-primary">voir plus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>




<?= $pagination->links() ?>