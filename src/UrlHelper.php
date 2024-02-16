<?php
namespace Vico;

/**
 * Pour les modifications sur $_GET, on ne modifie jamais directement la variable.
 * A la place, on crée une copie $get a partir du paramètre $url et on retourne l'url target avec les nouvelles valeurs $get
 * 
 */
class UrlHelper
{
    /**
     * @param string $url
     * @return string 
     */
    
    public function modif_get(?string $target = null, ?array $add = null, ?array $del = null, ?array $keep = null):string 
    {
        $target = $target ?: $_SERVER['REQUEST_URI'];
        $get = $_GET;
        foreach($get as $key => $value)
        {
            if(($keep !== null AND !in_array($key, $keep)) OR ($del !== null AND in_array($key, $del)))
            {
                unset($get[$key]);
            }
        }
        if($add !== null)
        {
            $get = array_merge($get, $add);
        }
        return explode('?', $target)[0] . $this->buildGetString($get);
    }
    public function del_get(?string $target = null)
    {
        $target = $target ?: $_SERVER['REQUEST_URI'];
        return explode('?', $target)[0];
    }
    public function explode_get(?string $url = null):array
    {
        $url = $url ?: $_SERVER['REQUEST_URI'];
        $get_data = [];
        if(isset(explode('?', $url)[1]))
        {
            foreach(explode('&', explode('?', $url)[1]) as $eq)
            {
                if(isset(explode('=', $eq)[1]))
                {   
                    $get_data[explode('=', $eq)[0]] = explode('=', $eq)[1];
                }
            }
        }
        return $get_data;
    }

    public function getPositiveInt(string $key, int $default):int
    {
        if(isset($_GET[$key]) AND ($_GET[$key] <= 1 OR !filter_var($_GET[$key], FILTER_VALIDATE_INT)))
        {
            header('Location: '.self::modif_get(null, null, [$key]));
            exit();
        }
        return $_GET[$key] ?? $default;
    }


    public function match(string $url_1, string $url_2):bool 
    {
        $part_1 = explode('?', $url_1)[0];
        $name_1 = explode('/', $part_1)[1] ?? null;

        $part_2 = explode('?', $url_2)[0];
        $name_2 = explode('/', $part_2)[1] ?? null;

        return $name_1 === $name_2;
    }
    public function match_2(string $url_1, string $url_2):bool 
    {
        $part_1 = explode('?', $url_1)[0];
        $name_1_1 = explode('/', $part_1)[1] ?? null;
        $name_1_2 = explode('/', $part_1)[2] ?? null;

        $part_2 = explode('?', $url_2)[0];
        $name_2_1 = explode('/', $part_2)[1] ?? null;
        $name_2_2 = explode('/', $part_2)[2] ?? null;

        return ($name_1_1 === $name_2_1 AND $name_1_2 === $name_2_2);
    }
    public function match_3(string $url_1, string $url_2):bool 
    {
        $part_1 = explode('?', $url_1)[0];
        $name_1_1 = explode('/', $part_1)[1] ?? null;
        $name_1_2 = explode('/', $part_1)[2] ?? null;
        $name_1_3 = explode('/', $part_1)[3] ?? null;

        $part_2 = explode('?', $url_2)[0];
        $name_2_1 = explode('/', $part_2)[1] ?? null;
        $name_2_2 = explode('/', $part_2)[2] ?? null;
        $name_2_3 = explode('/', $part_2)[3] ?? null;

        return ($name_1_1 === $name_2_1 AND $name_1_2 === $name_2_2 AND $name_1_3 === $name_2_3);
    }
    public function match_4(string $url_1, string $url_2):bool 
    {
        $part_1 = explode('?', $url_1)[0];
        $name_1_1 = explode('/', $part_1)[1] ?? null;
        $name_1_2 = explode('/', $part_1)[2] ?? null;
        $name_1_3 = explode('/', $part_1)[3] ?? null;
        $name_1_4 = explode('/', $part_1)[4] ?? null;

        $part_2 = explode('?', $url_2)[0];
        $name_2_1 = explode('/', $part_2)[1] ?? null;
        $name_2_2 = explode('/', $part_2)[2] ?? null;
        $name_2_3 = explode('/', $part_2)[3] ?? null;
        $name_2_4 = explode('/', $part_2)[4] ?? null;

        return ($name_1_1 === $name_2_1 AND $name_1_2 === $name_2_2 AND $name_1_3 === $name_2_3 AND $name_1_4 === $name_2_4);
    }

    /**
     * @param array $get
     * @return string $get_string la string à placer derrière le ? dans l'url
     */
    private function buildGetString(array $get):string
    {
        if(empty($get))
        {
            return '';
        }
        $get_string = '';
        foreach($get as $key => $value)
        {
            $get_string .= $key.'='.$value.'&';
        }
        return '?' . trim($get_string, '&');
    }
}