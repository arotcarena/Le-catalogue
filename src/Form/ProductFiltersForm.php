<?php
namespace Vico\Form;

use Vico\Models\ProductFilter;

class ProductFiltersForm extends Form
{

    /**
     * @param Category[] $categories
     * @param array $brands
     */
    public function __construct(array $categories, array $brands, ?ProductFilter $product_filter)
    {
        parent::__construct($product_filter);
        $category_ids = [];
        $category_names = [];
        foreach($categories as $category)
        {
            $category_ids[] = $category->getId();
            $category_names[] = $category->getName();
        }
        $this->getBuilder()
            ->setMethod('get')
            ->addSelect('category_id', 'Filtrer par catégorie', $category_ids, $category_names)
            ->addSelect('brand', 'Filtrer par marque', $brands, $brands)
            ->addSelect('price_order', 'Filtrer par ordre de prix', ['asc', 'desc'], ['du - cher au + cher', 'du + cher au - cher'])
            ->addInput('number', 'price_max', 'prix max.')
            ->addInput('number', 'price_min', 'prix min.')
            ->addSelect('per_page', 'Résultats par page', [1, 2, 3, 4, 5], ['1', '2', '3', '4', '5'])
            ->addInput('text', 'q', 'Recherche par marque, modèle, catégorie...')
            ->addButton('Lancer la recherche', 'btn-primary');

    }


}
