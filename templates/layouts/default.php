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
                        <?= $nav->link($router->url('products_index'), 'Catalogue') ?>
                    </li>
                    <li class="nav-item">
                        <?= $nav->link($router->url('contact'), 'Contact') ?>
                    </li>
                    <li class="nav-item">
                        <?= $nav->link($router->url('details'), 'Mon Compte') ?>
                    </li>
                    <li class="nav-item">
                        <?= $nav->link($router->url('cart_index'), 'Mon Panier') ?>
                    </li>
                    
                    <?php if(isset($_SESSION['id'])): ?>
                    <li class="nav-item m-auto" style="color: white;">
                        (<?= $helper->getManager('CartManager')->count(['user_id' => $_SESSION['id']]) ?>)
                    </li>
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


    <div class="container mt-4">
        
        <?php require '_alerts.php' ?>


        <?= $body ?>

    </div>


<?php require '_footer.php' ?>

</body>
</html>