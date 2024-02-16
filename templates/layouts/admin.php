<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
    <title><?= $title ?? 'mon site' ?></title>
</head>
<body>

    <nav class="navbar navbar-expand navbar-dark bg-dark" aria-label="Second navbar example">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarsExample02">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?= $nav->link($router->url('admin_products_index'), 'Listing des produits') ?>
                    </li>
                    <li class="nav-item">
                        <?= $nav->link($router->url('admin_contact'), 'Contact') ?>
                    </li>
                    <li class="nav-item">
                            <?= $nav->link($router->url('admin_orders', ['status' => 0]), 'Gérer les commandes') ?>
                    </li>
                    <li class="nav-item">
                        <?= $nav->link($router->url('admin_users_listing'), 'Gérer les utilisateurs') ?>
                    </li>
                    <li class="nav-item">
                        <?= $nav->link($router->url('admin_carts'), 'Voir les paniers') ?>
                    </li>
                    
                    <?php if(isset($_SESSION['id'])): ?>
                    
                    <li class="nav-item">
                            <?= $nav->link($router->url('logout'), 'Se Déconnecter') ?>
                    </li>
                    <?php endif ?>
                </ul>
                <form>
                <input class="form-control" type="text" placeholder="Search" aria-label="Search">
                </form>
            </div>
        </div>
    </nav>

   


    <div class="container">
        
        <?php require '_alerts.php' ?>


        <?= $body ?>


    </div>



<?php require '_footer.php' ?>

</body>
</html>