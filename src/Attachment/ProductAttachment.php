<?php
namespace Vico\Attachment;

use Vico\Config;
use Vico\Models\Product;
use Intervention\Image\ImageManager;

class ProductAttachment
{
    /**
     * @var ImageManager
     */
    private static $imageManager;

    
    public const EXTENSION = '.jpg';

    private const FORMATS = [
        'maxi' => [
            'width' => 600,
            'height' => null
        ],
        'medium' => [
            'width' => null,
            'height' => 350
        ],
        'mini' => [
            'width' => null,
            'height' => 175
        ],
        'nano' => [
            'width' => null,
            'height' => 100
        ]
    ];

    /**
     * sauvegarde les images téléchargées et hydrate le product avec les images_name
     */
    public static function uploadImages(Product $product):void
    {
        /** @var array 'tmp_name'[] */
        $images = $product->getImages();

        self::verifyOrCreate_directory(Config::product_image_path());
        $images_name = [];
        foreach($images as $image)
        {
            if(!empty($image))
            {
                $name = uniqid('', true);
                self::save($image, $name);
                $images_name[] = $name;
            }
        }
        if(empty($product->getFirst_image_name()) AND !empty($images_name[0]))
        {
            $product->setFirst_image_name($images_name[0]);
            unset($images_name[0]);
        }
        $product->addOther_images_name($images_name);
    }

    /**
     * appelé en cas de mise a jour 
     */
    public static function updateImages(Product $product):void 
    {
        //on supprime les fichiers des toDelete_images 
        self::deleteImages($product->getToDelete_images());
        //on modifie firstImage_name dans product
        if(!empty($product->getFirst_image_choice()) AND $product->getFirst_image_choice() !== $product->getFirst_image_name())
        {
            $product->changeFirst_image($product->getFirst_image_choice());
        }
        //on supprime les toDelete_images dans product
        $product->removeImages_name($product->getToDelete_images());
        
        self::uploadImages($product);
    }

    /**
     * FONCTION A APPELER EN CAS DE SUPPRESSION D UN PRODUIT
     * @param Product $product
     */
    public static function delete(Product $product):void 
    {
        self::deleteImages($product->getImages_name());
        //a compléter si nécessaire pour supprimer d'autres attachments autres que les images
    }

    /**
     * FONCTION A APPELER EN CAS D UPDATE POUR SUPPRIMER CERTAINES IMAGES
     * @param array 'image_names'[] 
     */
    public static function deleteImages($names):void
    { 
        foreach($names as $name)
        {
            if(!empty($name))
            {
                $image_path = Config::product_image_path() . DIRECTORY_SEPARATOR . $name;
                foreach(self::FORMATS as $format => $values)
                {
                    if(file_exists($image_path . '_' . $format . self::EXTENSION))
                    {
                        unlink($image_path . '_' . $format . self::EXTENSION);
                    }
                }
            }
        }
    }

    private static function verifyOrCreate_directory(string $directory_path):void
    {
        if(!file_exists($directory_path))
        {
            mkdir($directory_path, 0777, true);
        }
    }
    /**
     * @param array|null $formats  (par défault tous les formats)
     */
    private static function save(string $image_path, string $image_name, ?array $formats = ['nano', 'mini', 'medium', 'maxi']):void
    {
        foreach($formats as $format)
        {
            self::getImageManager()
                ->make($image_path)
                ->resize(self::FORMATS[$format]['width'], self::FORMATS[$format]['height'], function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(Config::product_image_path() . DIRECTORY_SEPARATOR . $image_name . '_' . $format . self::EXTENSION);
        }                      
    }
    private static function getImageManager():ImageManager
    {
        if(self::$imageManager === null)
        {
            self::$imageManager = new ImageManager(['driver' => 'gd']);
        }
        return self::$imageManager;
    }
}