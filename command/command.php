<?php
require 'vendor/autoload.php';
use Vico\Connection;







//$pdo = Connection::getPdo();

//    mail('guy@gmail.com', 'salut', "<strong>ca marche</strong> \n c trop cool");
 //     ini_set('SMTP', 'localhost');
 //     ini_set('smtp_port', '25');

/*

for ($i=1; $i <= 30; $i++) 
{ 
    $img_ids = [];
    while(count($img_ids) < 4)
    {
        $id = random_int(1, 7);
        if(!in_array($id, $img_ids))
        {
            $img_ids[] = $id;
        }
    }
    foreach($img_ids as $img_id)
    {
        $pdo->exec('INSERT INTO product_img SET product_id = '.$i.', img_id = '.$img_id);
    }
}






$computers = [
    'hp' => ['740 G1', 'Pavillon 15', '820 G3', '840 G2'], 
    'Acer' => ['Aspire 5', 'Aspire C'],
    'Toshiba' => ['Satellite Pro', 'Satellite A100'],
    'Asus' => ['Vivobook', 'P1500', 'E403SA']
];

$phones = [
    'iPhone' => ['8', 'X', '8s', 'Xs'], 
    'Samsung' => ['S10', 'A6', 'A40', 'Galaxy Note'],
    'Nokia' => ['Lumia S', '1.4'],
    'Huawei' => ['P30', 'P9']
];

$consoles = [
    'Play Station' => ['ps4', 'ps5', 'ps6'],
    'Xbox' => ['Adventure', 'Explore'],
    'Nintendo' => ['switch', 'ds']
];


$products = [];


foreach($computers as $brand => $models)
{
    foreach($models as $model)
    {
        $products[] = [
            'brand' => $brand,
            'model' => $model,
            'description' => 'Un ordinateur pratique',
            'price' => random_int(350, 1500),
            'img' => 'default',
            'category_id' => 1
        ];
    }
}


foreach($phones as $brand => $models)
{
    foreach($models as $model)
    {
        $products[] = [
            'brand' => $brand,
            'model' => $model,
            'description' => 'Un telephone pratique',
            'price' => random_int(100, 1000),
            'img' => 'default',
            'category_id' => 2
        ];
    }
}

foreach($consoles as $brand => $models)
{
    foreach($models as $model)
    {
        $products[] = [
            'brand' => $brand,
            'model' => $model,
            'description' => 'Une console pratique',
            'price' => random_int(100, 1000),
            'img' => 'default',
            'category_id' => 3
        ];
    }
}



foreach($products as $product)
{
    $insert = $pdo->prepare('INSERT INTO products SET brand = :brand, model = :model, description = :description, price = :price, img = :img, category_id = :category_id');
    $insert->execute($product);
}

*/