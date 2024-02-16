<?php
namespace Vico\Managers;

use Vico\Helper;
use Vico\Pagination;
use Vico\QueryBuilder;

Abstract class Manager
{

    /**
     * @var Helper
     */
    protected $helper;


    public function __construct(?Helper $helper = null)
    {
        $this->helper = $helper;
    }
    
    public function findOneOrNull(array $filters, ?string $key_word = null):?Object
    {
        $queryBuilder = $this->createQueryBuilder()
                        ->select('*')
                        ->from($this->table)
                        ->filters($filters, $this->fields);
        if($key_word !== null)
        {
            $queryBuilder->where_keyWord($key_word);
        }
            $result = $queryBuilder->setFetchClass($this->class)
                        ->fetch();
        return $result ?: null;
    }
    public function findAll(?array $filters = null, ?string $key_word = null):array
    {
        $queryBuilder = $this->createQueryBuilder()
                            ->select('*')
                            ->from($this->table);
        if(!empty($filters))
        {
            $key_word = $key_word ?: 'and';
            $queryBuilder->filters($filters, $this->fields)
                            ->where_keyWord($key_word);
        }
        if(!empty($filters['q']))
        {
            $queryBuilder->qSearch($filters['q'], $this->q_search_fields);
        }
        return $queryBuilder->setFetchClass($this->class)
                            ->fetchAll();
    }

    public function findPaginated(?array $filters = null, ?string $key_word = 'and'):Pagination    
    {
        $queryBuilder = $this->createQueryBuilder()
                            ->select('*')
                            ->from($this->table);
        if(!empty($filters))
        {
            $queryBuilder->filters($filters, $this->fields);
            $queryBuilder->where_keyWord($key_word);
            if(!empty($filters['q']))
            {
                $queryBuilder->qSearch($filters['q'], $this->q_search_fields);
            }
        }
        
        $per_page = 5;
        if(!empty($filters['per_page']))
        {
            $per_page = $filters['per_page'];
        }
       return $this->createPagination($queryBuilder->getQuery(), $per_page)
                    ->fetchClass($this->class);
    }

    public function exists(array $filters, ?string $key_word = null):bool 
    {
        return ($this->findOneOrNull($filters, $key_word) === null) ? false: true;
    }

    public function count(?array $filters = null, ?string $key_word = null):int 
    {
        $queryBuilder = $this->createQueryBuilder()
                            ->select('*')
                            ->from($this->table);
        if(!empty($filters))
        {
            $queryBuilder->filters($filters, $this->fields);
        }
        if(!empty($key_word))
        {
            $queryBuilder->where_keyWord($key_word);
        }
        return $queryBuilder->count();
    }


    /**
     * @param Object $object
     * update si l'objet a un id et cet id est déjà en base de donnée
     * sinon insert
     * @return int $id
     */
    public function persist(Object $object):int
    {
        $queryBuilder = $this->createQueryBuilder();
        if(!empty($object->getId()) AND $this->exists(['id' => $object->getId()]))
        {
            $queryBuilder->update($this->table)
                        ->addWhere('id = :id')
                        ->setParams(['id' => $object->getId()]);
        }
        else
        {
            $queryBuilder->insert_into($this->table);
        }
        foreach($this->fields as $field)
        {
            $method = 'get'.ucfirst($field);
            $value = $object->$method();
            
            $queryBuilder->addSet($field.' = :set_'.$field)
                        ->setParams(['set_'.$field => $value]);
        }
        return $queryBuilder->execute();
    }
    

    public function delete(int $id):void
    {
        $this->deleteAll(['id' => $id]);
    }
      
    public function deleteAll(?array $filters = null, ?string $key_word = null):void
    {
        $queryBuilder = $this->createQueryBuilder()
                                ->delete_from($this->table);
        if(!empty($filters))
        {
            $key_word = $key_word ?: 'and';
            $queryBuilder->filters($filters, $this->fields)
                            ->where_keyWord($key_word);
        }
        $queryBuilder->execute();
    }



/*
//////////////////////////A SUPPRIMER//////////////////////////////////////////////
    /**
     * @param array|Object $data;
     */
/*
    public function insert($data, ?array $filters = null, ?string $key_word = ''):?int
    {
        $this->params = [];
        $sql_where = '';
        if($filters !== null)
        {
            $action = 'UPDATE';
            $sql_where = $this->sql_filters($filters, $key_word);
        }
        else
        {
            $action = 'INSERT INTO'; 
        }
        $sql = $action.' '.$this->table.' '.$this->sql_set($data).' '.$sql_where;
        $insert = $this->pdo->prepare($sql);
        $result = $insert->execute($this->params);
        if(!$result)
        {
            throw new \Exception("L'élément n'a pas pu être ajouté / mis à jour", 1);
        }
        
        return $filters === null ? $this->pdo->lastInsertId(): null;
    }

    public function delete(array $filters, ?string $key_word = ''):void 
    {
        $this->params = [];
        $sql = 'DELETE FROM '.$this->table . ' ' . $this->sql_filters($filters, $key_word); 
        $delete = $this->pdo->prepare($sql);
        $result = $delete->execute($this->params);
        if(!$result)
        {
            throw new \Exception("L'élément n'a pas pu être supprimé", 1);
        }
    }
*/

    protected function createQueryBuilder():QueryBuilder
    {
        return new QueryBuilder();
    }
    protected function createPagination(array $query, ?int $perPage = null):Pagination
    {
        return new Pagination($query, $this->helper->get('UrlHelper'), $perPage);
    }
    protected function getQ_search_fields(string $class):array 
    {
        return (new $class())->q_search_fields;
    }
    protected function get_fields(string $class):array 
    {
        return (new $class())->fields;
    }
    

///////////////////////////////A SUPPRIMER//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  
/*
    /**
     * @param Object|array $data;  (tableau ou objet de la classe $this->class)
     */
/*
    protected function sql_set($data):string
    {
        $sql_set = [];
        foreach($this->fields as $field)
        {
            if(is_object($data))
            {
                $method = 'get'.ucfirst($field);
                $value = $data->$method();
                if(isset($this->to_hash) AND in_array($field, $this->to_hash))
                {
                    $value = password_hash($value, PASSWORD_BCRYPT);
                }
                if($value !== '')
                {
                    $value = $value === 'null' ? null: $value;
                    $sql_set[] = $this->table.'.'.$field.' = :set_'.$this->table.'_'.$field;
                    $this->params['set_'.$this->table.'_'.$field] = $value;
                }
            }
            elseif(is_array($data))
            {
                if(isset($data[$field]) AND $data[$field] !== '')
                {
                    $value = $data[$field]; 
                    $value = $value === 'null' ? null: $value;
                    if(isset($this->to_hash) AND in_array($field, $this->to_hash))
                    {
                        $value = password_hash($value, PASSWORD_BCRYPT);
                    }
                    $sql_set[] = $this->table.'.'.$field.' = :set_'.$this->table.'_'.$field;
                    $this->params['set_'.$this->table.'_'.$field] = $value;
                }
            }
        }
        return 'SET ' . implode(', ', $sql_set);
    }

    protected function sql_filters(array $filters, ?string $key_word = ''):string 
    {   
        $where = [];
        $order = '';
        foreach($filters as $key => $value)
        {
            // ORDER BY
            if(strpos($key, '_order') AND in_array($value, ['asc', 'desc'])) 
            {   
                $field = str_replace('_order', '', $key);
                
                if(in_array($field, $this->fields))
                {
                    $order = 'ORDER BY '.$field.' '.strtoupper($value);
                }
            }
            //WHERE
            //dans le cas d'une recherche q sur tous les fields en même temps
            if($key === 'q' AND $value !== '')
            {
                foreach($this->fields as $field)
                {
                    if(in_array($field, $this->q_search_fields))
                    {
                        $where[] = $this->table.'.'.$field.' LIKE :'.$this->table.'_'.$field;
                        $this->params[$this->table.'_'.$field] = '%'.$value.'%';
                    }
                }
            }
            //dans le cas d'une recherche q sur un field particulier
            elseif(strpos($key, '_q') AND $value !== '')
            {
                $field = str_replace('_q', '', $key);
                $this->params[$this->table.'_'.$key] = '%'.$value.'%';
                $where[] = $this->table.'.'.$field.' LIKE :'.$this->table.'_'.$key;
            }
            // dans le cas d'un WHERE ... IN (.., .., .., )
            elseif(is_array($value) AND !empty($value))  
            {
                $in = [];
                foreach($value as $k => $v)
                {
                    $in[] = ':'.$this->table.'_'.$k;
                    $this->params[$this->table.'_'.$k] = $v;
                }
                $where[] = $this->table.'.'.$key.' IN ('.implode(', ', $in).')';     
            }
            // tous les autres cas
            elseif($value !== '')
            {
                if(strpos($key, '_min'))
                {
                    $operator = '>=';
                    $field = str_replace('_min', '', $key);
                }
                elseif(strpos($key, '_max'))
                {
                    $operator = '<=';
                    $field = str_replace('_max', '', $key);
                }
                elseif(strpos($key, '_isnot'))
                {
                    $operator = '!=';
                    $field = str_replace('_isnot', '', $key);
                }
                else
                {
                    $operator = '=';
                    $field = $key;
                }
                if(in_array($field, $this->fields))
                {
                    $value = $value === 'null' ? null: $value;
                    $where[] = $this->table.'.'.$field.' '.$operator.' :' . $this->table.'_'.$key;
                    $this->params[$this->table.'_'.$key] = $value;
                }
            }

        }
        $sql_where = !empty($where) ? 'WHERE ' . implode(' '.strtoupper($key_word).' ', $where): '';

        return $sql_where.' '.$order;
    }

    protected function sql_join_filters(Object $join_manager, array $filters, ?string $key_word = ''):string 
    {   
        $where = [];
        $order = '';
        foreach($filters as $key => $value)
        {
            // ORDER BY
            if(strpos($key, '_order') AND in_array($value, ['asc', 'desc'])) 
            {   
                $field = str_replace('_order', '', $key);
                
                if(in_array($field, $this->fields))
                {
                    $order = 'ORDER BY '.$field.' '.strtoupper($value);
                }
            }
            //WHERE
            //dans le cas d'une recherche q sur tous les fields en même temps
            if($key === 'q' AND $value !== '')
            {
                $this->params['q'] = '%'.$value.'%';
                foreach($this->fields as $field)
                {
                    if(in_array($field, $this->q_search_fields))
                    {
                        $where[] = $this->table.'.'.$field.' LIKE :q';
                    }
                }
                foreach($join_manager->fields as $field)
                {
                    if(in_array($field, $join_manager->q_search_fields))
                    {
                        $where[] = $join_manager->table.'.'.$field.' LIKE :q';
                    }
                }
            }
            //dans le cas d'une recherche q sur un field particulier
            elseif(strpos($key, '_q') AND $value !== '')
            {
                $field = str_replace('_q', '', $key);
                $key = str_replace('.', '_', $key);
                $this->params[$key] = '%'.$value.'%';
                $where[] = $field.' LIKE :'.$key;
            }
            // dans le cas d'un WHERE ... IN (.., .., .., )
            elseif(is_array($value) AND !empty($value))  
            {
                $field = $key;
                $key = str_replace('.', '_', $key);
                $in = [];
                foreach($value as $k => $v)
                {
                    $in[] = ':'.$key.'_'.$k;
                    $this->params[$key.'_'.$k] = $v;
                }
                $where[] = $field.' IN ('.implode(', ', $in).')';     
            }
            // tous les autres cas
            elseif($value !== '' AND strpos('.', $key))
            {
                if(strpos($key, '_min'))
                {
                    $operator = '>=';
                    $field = str_replace('_min', '', $key);
                }
                elseif(strpos($key, '_max'))
                {
                    $operator = '<=';
                    $field = str_replace('_max', '', $key);
                }
                elseif(strpos($key, '_isnot'))
                {
                    $operator = '!=';
                    $field = str_replace('_isnot', '', $key);
                }
                else
                {
                    $operator = '=';
                    $field = $key;
                }
                if((in_array(explode('.', $field)[1], $this->fields) AND explode('.', $field)[0] == $this->table) OR (in_array(explode('.', $field)[1], $join_manager->fields) AND explode('.', $field)[0] == $join_manager->table))
                {
                    $key = str_replace('.', '_', $field);
                    $value = $value === 'null' ? null: $value;
                    $where[] = $field.' '.$operator.' :' .$key;
                    $this->params[$key] = $value;
                }
            }

        }
        $sql_where = !empty($where) ? 'WHERE ' . implode(' '.strtoupper($key_word).' ', $where): '';

        return $sql_where.' '.$order;
    }
    */
}