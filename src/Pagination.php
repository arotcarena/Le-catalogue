<?php
namespace Vico;

use \PDO;
use Vico\UrlHelper;
use Vico\Connection;
use Vico\QueryBuilder;




class Pagination
{
	private $items = [];

    private $pdo;

	private $sql;

	private $perPage;

	public $count;

	private $pages;

	private $offset;

	private $currentPage;

	private $params;

	private $url_helper;

	public function __construct(array $query, UrlHelper $url_helper, int $perPage = 5)
	{
		$this->pdo = Connection::getPdo();
		$this->url_helper = $url_helper;
		$this->sql = $query['sql'];
		$this->params = $query['params'];
		$this->perPage = $perPage;
		$this->currentPage = $this->url_helper->getPositiveInt('p', 1);
		$this->offset = $this->perPage * $this->currentPage - $this->perPage;
		$this->setCount()
			->setPages();
	}

	public function fetchClass(string $class):self
	{
        $request = $this->pdo->prepare($this->sql.' LIMIT '.$this->perPage.' OFFSET '.$this->offset);
        $request->execute($this->params);
        $this->items = $request->fetchAll(\PDO::FETCH_CLASS, $class);

		return $this;
	}

	public function fetchAssoc():self 
	{
		$request = $this->pdo->prepare($this->sql.' LIMIT '.$this->perPage.' OFFSET '.$this->offset);
        $request->execute($this->params);
        $this->items = $request->fetchAll(\PDO::FETCH_ASSOC);

		return $this;
	}

	public function getItems():array 
    {
        return $this->items;
    }

	public function getCount():int 
	{
		return $this->count;
	}

	public function getCountFormated():string 
	{
		$s = $this->count > 1 ? 's': '';
		return '<strong>'.$this->count.'</strong> résultat'.$s.' de recherche.';
	}
	
	public function links():?string 
	{
		if($this->count === 0)
		{
			return null;
		}
		$previous_href = $this->url_helper->modif_get(null, ['p' => $this->currentPage - 1], ['suppr', 'logout', 'login']);
		$next_href = $this->url_helper->modif_get(null, ['p' => $this->currentPage + 1], ['suppr', 'logout', 'login']);
		
		$previous_class = $this->currentPage <= 1 ? 'disabled': '';
		$next_class = $this->currentPage >= $this->pages ? 'disabled': '';

		return <<<HTML

		<nav aria-label="Page navigation example">
		<ul class="pagination">
			<li class="page-item $previous_class">
			<a class="page-link" href="$previous_href" aria-label="Previous">
				<span aria-hidden="true">&laquo;</span>
				<span class="sr-only"></span>
			</a>
			</li>
			<li class="page-item"><a class="page-link" href="">$this->currentPage / $this->pages</a></li>
			<li class="page-item $next_class">
			<a class="page-link" href="$next_href" aria-label="Next">
				<span aria-hidden="true">&raquo;</span>
				<span class="sr-only"></span>
			</a>
			</li>
		</ul>
		</nav>

		HTML;
	}

	


    private function setPages():self
	{
		$this->pages = (int)ceil($this->count / $this->perPage);
		if($this->pages <= 0)
		{
			$this->pages = 1;
		}
		if($this->currentPage > $this->pages)
		{
			throw new \Exception("La page demandée n existe pas", 1);
		}
		return $this;
	}
	private function setCount():self
	{
		/*probablement inutile
        $select = explode('FROM', $this->sql)[0];
        $selection = trim($select, 'SELECT ');
		if(strpos($selection, '.'))
		{
			$id = explode('.', $selection)[0] .'.id';
		}
		else
		{
			$id = 'id';
		}
		*/
	    $sqlCount = 'SELECT count(*) FROM' . explode('FROM', $this->sql)[1];
        $request = $this->pdo->prepare($sqlCount);
		$request->execute($this->params);
		$this->count = (int)$request->fetch()[0];
		return $this;
	}

	
}