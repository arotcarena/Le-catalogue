


<h2>Listing des produits</h2>

<a class="btn btn-outline-primary mt-4 mb-4" href="<?= $router->url('admin_products_new') ?>">+ Ajouter un produit</a>




<?php require '_search_filters.php' ?>



<div class="mb-4 mt-4"><?= $pagination->getCountFormated() ?></div>

<?= $pagination->links() ?>



<table class="table">
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
            <td><img src="<?= $product->getFirstImage_Url('mini') ?>" alt="image"/></td>
            <td><?= $product->getBrand() ?></td>
            <td><?= $product->getModel() ?></td>
            <td><?= $product->getDescription() ?></td>
            <td><?= $product->getPriceFormated() ?></td>
            <td><?= $product->getStock() ?></td>
            <?php if($product->getCategory() !== null): ?>
                <td><button class="btn btn-outline-<?= $product->getCategory()->color() ?> disabled"><?= $product->getCategory()->getName() ?></button></td>
            <?php endif ?>
            <td>
                <a href="<?= $router->url('admin_products_edit', ['id' => $product->getId(), 'slug' => $product->getSlug()]) ?>" class="btn btn-primary">Editer</a>
                <a href="<?= $router->url('admin_products_delete', ['del_id' => $product->getId()]) ?>" class="btn btn-secondary" 
                    onclick="return confirm('Voulez-vous vraiment supprimer cette fiche produit?')">
                    Supprimer
                </a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>





<?= $pagination->links() ?>





