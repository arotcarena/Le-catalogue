<?php

use Vico\Managers\ProductsManager;
use Vico\Auth;


Auth::check('admin');



$productsManager = new ProductsManager();
$productsManager->delete(['id' => $params['del_id']]);
header('Location: '.$router->url('admin_products_listing').'?suppr=1');
exit();
