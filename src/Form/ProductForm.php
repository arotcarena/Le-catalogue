<?php
namespace Vico\Form;

use Vico\Models\Product;
use Vico\Form\FormView\FormView;
use Vico\Managers\CategoryManager;
use Vico\Validators\ProductValidator;

class ProductForm extends Form
{
    public function __construct(CategoryManager $categoryManager, ?Product $product = null)
    {
        parent::__construct($product);
        $this->validator = new ProductValidator();

        $categories = $categoryManager->findAll();
        $category_ids = [];
        $category_names = [];
        foreach($categories as $category)
        {
            $category_ids[] = $category->getId();
            $category_names[] = $category->getName();
        }

        $this->getBuilder()
                ->setEnctype('multipart/form-data')
                ->addFile('images', 'Ajouter des images (max. 7)', true, 5)
                ->addInput('text', 'brand', 'Marque', 6)
                ->addInput('text', 'model', 'Modèle', 7)
                ->addSelect('category_id', 'Catégorie', $category_ids, $category_names, 'aucune', 8)
                ->addInput('number', 'price', 'Prix', 9)
                ->addTextarea('description', 'Description', 10)
                ->addInput('number', 'stock', 'En stock', 11);
    }
}
