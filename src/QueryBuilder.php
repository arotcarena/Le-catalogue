<?php 
namespace Vico;

use Vico\Connection;


/**
 * @Author Vico
 * prépare et renvoie la requête ou les résultats (seulement les query)
 */
class QueryBuilder
{

    private $pdo;

    private $params = [];

    private $action;

    private $selection;

    private $table;

    private $join;

    private $on;
    
    private $on_key_word;

    private $set;

    private $where = [];

    private $where_key_word;

    private $q_where = [];

    private $order_by;

    private $limit;

    private $fetchClass;

    


    public function __construct()
    {
        $this->pdo = Connection::getPdo();
    }

    public function select(string $selection):self 
    {
        $this->selection = $selection;
        $this->action = 'select';

        return $this;
    }
    public function from(string $table):self 
    {
        $this->table = $table;

        return $this;
    }
    public function update(string $table):self
    {
        $this->action = 'update';
        $this->table = $table;

        return $this;
    }
    public function insert_into(string $table):self 
    {
        $this->action = 'insert';
        $this->table = $table;

        return $this;
    }
    public function delete_from(string $table):self 
    {
        $this->action = 'delete';
        $this->table = $table;

        return $this;
    }
    public function join(string $join_table, ?string $option = null):self
    {
        $this->join = $option !== null ? $option . ' ' . $join_table: 'JOIN' . ' ' . $join_table;

        return $this;
    }
    public function on(string $on):self
    {
        $this->on[] = $on;

        return $this;
    }
    public function on_keyWord(string $on_key_word):self
    {
        if(!in_array($on_key_word, ['and', 'or']))
        {
            throw new \Exception("Le mot clef spécifié doit être 'AND' ou 'OR'", 1);
        }
        $this->on_key_word = strtoupper($on_key_word);

        return $this;
    }
    public function addSet(string $set):self
    {
        $this->set[] = $set;

        return $this;
    }
    public function addWhere(string $where):self
    {
        $this->where[] = $where;

        return $this;
    }
    public function where_keyWord(string $where_key_word):self
    {
        if(!in_array($where_key_word, ['and', 'AND', 'or', 'OR']))
        {
            throw new \Exception("Le mot clef spécifié doit être 'AND' ou 'OR'", 1);
        }
        $this->where_key_word = strtoupper($where_key_word);

        return $this;
    }

    public function filters(array $filters, array $allowed_fields, ?string $alias = null):self
    {
        $field_prefix = $alias !== null ? $alias.'.': null;
        $param_prefix = $alias !== null ? $alias.'_': null;
        if(count($filters) > 1 AND $key_word = null)
        {
            throw new \Exception("Vous n'avez pas spécifié de mot clef pour le filtrage (AND ou OR)", 1);
        }

        foreach($filters as $key => $value)
        {
            // ORDER BY
            if(str_contains($key, '_order') AND !empty($value)) 
            {   
                $field = str_replace('_order', '', $key);
                if(in_array($field, $allowed_fields))
                {
                    $this->orderBy($field_prefix.$field, $value);
                }
            }
            //WHERE
            // dans le cas d'un WHERE ... IN (.., .., .., )
            elseif(is_array($value) AND !empty($value))  
            {
                $in = [];
                $i = 0;
                foreach($value as $v)
                {
                    $i++;
                    $in[] = ':'.$param_prefix.$key.'_'.$i;
                    $this->params[$param_prefix.$key.'_'.$i] = $v;
                }
                $this->where[] = $field_prefix.$key.' IN ('.implode(', ', $in).')';     
            }
            // tous les autres where
            elseif($value !== '')
            {
                if(str_contains($key, '_min'))
                {
                    $operator = '>=';
                    $field = str_replace('_min', '', $key);
                }
                elseif(str_contains($key, '_max'))
                {
                    $operator = '<=';
                    $field = str_replace('_max', '', $key);
                }
                elseif(str_contains($key, '_isnot'))
                {
                    $operator = '!=';
                    $field = str_replace('_isnot', '', $key);
                }
                else
                {
                    $operator = '=';
                    $field = $key;
                }
                if(in_array($field, $allowed_fields))
                {
                    $value = $value === 'null' ? null: $value;
                    $this->where[] = $field_prefix.$field.' '.$operator.' :' .$param_prefix.$key;
                    $this->params[$param_prefix.$key] = $value;
                }
            }
        }
        return $this;
    }

    public function qSearch(string $q, array $allowed_fields, ?string $alias = null):self
    {
        $field_prefix = $alias !== null ? $alias.'.': null;

        foreach($allowed_fields as $field)
        {
            $this->q_where[] = $field_prefix.$field.' LIKE :q';
        }
        $this->params['q'] = '%'.$q.'%';

        return $this;
    }



    public function setParams(array $params):self
    {
        $this->params = array_merge($this->params, $params);
        
        return $this;
    }
    
    /**
     * @param string $dir 
     */
    public function orderBy(string $field, string $dir):self 
    {
        if(!in_array($dir, ['asc', 'desc']))
        {
            throw new \Exception("La direction spécifiée en 2eme parametre du queryBuilder->orderBy() doit être 'asc' ou 'desc", 1);
        }
        $this->order_by = 'ORDER BY '.$field.' '.strtoupper($dir);

        return $this;
    }
    public function limit(int $limit):self 
    {
        $this->limit = 'LIMIT '.$limit;

        return $this;
    }


    public function toSql(bool $count = false):string 
    {
        $this->check_errors();

        if($count)
        {
            $sql = 'SELECT count('.$this->selection.') FROM '.$this->table;
        }
        else
        {
            switch ($this->action) {
                case 'select':
                    $sql = 'SELECT '.$this->selection.' FROM '.$this->table;
                    break;
                case 'insert':
                    $sql = 'INSERT INTO '.$this->table;
                    break;
                case 'update':
                    $sql = 'UPDATE '.$this->table;
                    break;
                case 'delete':
                    $sql = 'DELETE FROM '.$this->table;
                    break;
                default:
                    $sql = 'SELECT * FROM '.$this->table;
            }
        }

        if($this->join !== null)
        {
            $sql .= ' '.$this->join;
            if(!empty($this->on))
            {
                $sql .= ' ON '. implode(' '.$this->on_key_word.' ', $this->on);
            }
        }
        if(!empty($this->set))
        {
            $sql .= ' SET '.implode(', ', $this->set);
        }
        if(!empty($this->where) OR !empty($this->q_where))
        {
            $where = [];
            if(!empty($this->where))
            {
                $where[] = '('. implode(' '.$this->where_key_word.' ', $this->where) .')';
            }
            if(!empty($this->q_where))
            {
                $where[] = '('. implode(' OR ', $this->q_where). ')';
            }
            $sql .= ' WHERE '.implode(' '.$this->where_key_word.' ', $where);
        }
        if(!$count)
        {
            $sql .= ' '.$this->order_by.' '.$this->limit;
        }
        return $sql;
    }
    


    public function getParams():array 
    {
        return $this->params;
    }

    /**
     * @return array [string $sql, string $params]
     */
    public function getQuery():array
    {
        return ['sql' => $this->toSql(), 'params' => $this->params];
    }

    public function setFetchClass(string $class):self
    {
        $this->fetchClass = $class;

        return $this;
    }


    public function fetch():?Object
    {
        $query = $this->pdo->prepare($this->toSql());
        $query->execute($this->params);
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->fetchClass);
        return $query->fetch() ?: null;
    }
    public function fetchAll():Array
    {
        $query = $this->pdo->prepare($this->toSql());
        $query->execute($this->params);
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->fetchClass);
    }

    public function fetchAssoc():?array
    {
        $query = $this->pdo->prepare($this->toSql());
        $query->execute($this->params);
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        return $query->fetch() ?: null;
    }

    public function fetchAllAssoc():Array
    {
        $query = $this->pdo->prepare($this->toSql());
        $query->execute($this->params);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count():int
    {
        $count = $this->pdo->prepare($this->toSql(true));
        $count->execute($this->params);
        return $count->fetch()[0];
    }

    /**
     * @return int|null $id ou null
     */
    public function execute() 
    {
        $this->pdo->prepare($this->toSql())
                    ->execute($this->params);
        return $this->pdo->lastInsertId() ?? null;
    }



    private function check_errors():void 
    {
        if($this->action === 'select' AND $this->selection === null)
        {
            throw new \Exception("vous n'avez spécifié aucun champ à sélectionner", 1);
        }
        if($this->table === null)
        {
            throw new \Exception("vous n'avez spécifié aucune table sur laquelle travailler", 1);
        }
        if(count($this->where) > 1 AND $this->where_key_word === null)
        {
            throw new \Exception("Vous avez défini plusieurs conditions sans définir le mot clef 'OR' ou 'AND'", 1);
        }
        if(!empty($this->on) AND $this->join === null)
        {
            throw new \Exception("Vous avez utilisé un 'ON' sans 'JOIN'", 1);
        }
        if($this->selection !== null AND $this->action === 'DELETE')
        {
            throw new \Exception("Vous avez spécifié des champs à sélectionner pour un requête delete", 1);
        }
    }


}