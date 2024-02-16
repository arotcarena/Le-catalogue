<?php

namespace Vico;

use \PDO;
use Vico\QueryBuilder;
use Vico\Managers\Manager;

class Tools
{
    public static function getQueryBuilder():QueryBuilder
    {
        return new QueryBuilder();
    }

    public static function completeAssetsPath(string $css_path, string $request_uri):string
    {
			$n = substr_count($request_uri, '/') - 1; 
			$add = '';
			for ($i=0; $i < $n ; $i++) { 
				$add .= '../';
			}
			return $add.$css_path;
    }
    public static function slugify(string $string):string 
    {
        return strtolower(implode('-', explode(' ', $string)));
    }

    public static function hydrate(Object $object, array $data):Object
    {
        foreach($data as $key => $value)
        {
            if(!empty($value))
            {
                $method = 'set'.ucfirst($key);
                if(method_exists($object, $method))
                {
                    $object->$method($value);
                }
                if(str_contains($key, '_'))
                {
                    $parts = explode('_', $key);
                    $parts = array_map(function ($part) {
                        return ucfirst($part);
                    }, $parts);
                    $method = implode('', $parts);
                    if(method_exists($object, $method))
                    {
                        $object->$method($value);
                    }
                }
            }
        }
        return $object;
    }

    public static function token(int $length):string
    {
        return \substr(\str_shuffle(\str_repeat('0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN', 100)), 0, $length); 
    }
    public static function numeric_code(int $length):string
    {
        return \substr(\str_shuffle(\str_repeat('0123456789', 100)), 0, $length); 
    }


    
    /**
     * @param Object|Object[] $data (object ou tableau d'objets)
     * @param array $fields (Les propriétés de l'objet qu'on veut encoder)
     * La forme d'encodage est la suivante 'valeur1objet1 & valeur2objet1 & valeur3objet1 | valeur1objet2 & valeur2objet2 etc...' (pas d'espaces dans la réalité)
     */
    public static function encode($data, array $fields):string
    {
        $string = '';
        if(is_array($data))
        {
            foreach($data as $d)
            {
                foreach($fields as $field)
                {
                    $method = 'get'.ucfirst($field);
                    if(\method_exists($d, $method))
                    {
                        $string .= $field.'#'.$d->$method().'&';
                    }
                }
                $string = trim($string, '&');
                $string .= '|';
            }
            return trim($string, '|');
        }
        else
        {
            foreach($fields as $field)
            {
                $method = 'get'.ucfirst($field);
                if(\method_exists($data, $method))
                {
                    $string .= $field.'#'.$data->$method().'&';
                }
            }
            return trim($string, '&');
        }
    }

    public static function decode(?string $encoded):array
    {
        if(empty($encoded))
        {
            return [];
        }
        $data = [];
        foreach(explode('|', $encoded) as $object)
        {
            $object_data = [];
            foreach(explode('&', $object) as $field)
            {
                $object_data[explode('#', $field)[0]] = explode('#', $field)[1];
            }
            $data[] = $object_data;
        }
        return $data;
    }
    /**
     * @return string (date formatée pour sql : Y-m-d H:i:s)
     */
    public static function getAddDate(string $add):string 
    {
        $date = strtotime((new \DateTime())->format('Y-m-d'). ' '.$add);
        return (new \DateTime(date('Y-m-d', $date)))->format('Y-m-d H:i:s');
    }

    /**
     * @
     * @param string $option ('date_time' pour Date et heure, 'date' pour la date, et 'time' pour l'heure)
     */
    public static function format_sql_date(?string $date, ?string $option = 'date_time'):?string
    {
        if($date === null OR !in_array($option, ['date', 'time', 'date_time']))
        {
            return null;
        }
        switch ($option) {
            case 'date_time':
                $format = 'd/m/Y à H\hi';
                break;
            case 'date':
                $format = 'd/m/Y';
                break;
            case 'time':
                $format = 'H\hi\m\i\ns\s';
                break;
        }
        return (new \DateTime($date))->format($format);
    }

    /**
     * @param Object[] $receivers
     * @param string $injected_class
     */
    public static function inject(array $receivers, string $injected_objectManager, string $receiver_property):void
    {
        $injected_objects = (new $injected_objectManager())->findAll();

        $inj_obj_by_id = [];
        foreach($injected_objects as $injected_object)
        {
            $inj_obj_by_id[$injected_object->getId()] = $injected_object;
        }
        foreach($receivers as $receiver)
        {
            $setInjectedObject = 'set'.ucfirst($receiver_property);
            $getInjectedId = 'get'.ucfirst($receiver_property).'_id';
            $receiver->$setInjectedObject($inj_obj_by_id[$receiver->$getInjectedId()]);
        }
    }
}