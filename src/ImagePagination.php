<?php
namespace Vico;

use Vico\Attachment\ProductAttachment;
use Vico\UrlHelper;
use Vico\Models\Product;




class ImagePagination
{
	private $images_name = [];

	private $count;

	private $perPage = 1;

	private $currentPage;

	private $offset;

	/**
	 * @var UrlHelper
	 */
	private $url_helper;

	public function __construct(Product $product, UrlHelper $url_helper)
	{
		$this->url_helper = $url_helper;
		$this->currentPage = $this->url_helper->getPositiveInt('img', 1);

		$i = 1;
		$this->images_name[$i] = $product->getFirst_image_name() ?: 'default';
		foreach($product->getOther_images_name() as $name)
		{
			if(!empty($name))
			{
				$i++;
				$this->images_name[$i] = $name;
			}
		}
		$this->count = $i;

		$this->offset = $this->perPage * $this->currentPage - $this->perPage;
	}

	
	public function view():string
	{
		if($this->count <= 1)
		{
			return <<<HTML
					<div style="text-align: center;" class="row mb-4 align-items-center">
						{$this->image_view(($this->offset + 1), 'medium', true)}
					</div>
					HTML;
		}

		return <<<HTML
				<div style="text-align: center;" class="row mb-4 align-items-center">
					<div class="col-3" style="text-align: end;">
						{$this->previousLink()}
					</div>
					<div class="col-6">
						{$this->image_view(($this->offset + 1), 'medium', true)}
					</div>
					<div class="col-3" style="text-align: start;">
						{$this->nextLink()}
					</div>
				</div>
				
				<div style="text-align: center;">
					{$this->image_view(($this->offset), 'nano')}
					{$this->image_view(($this->offset + 1), 'mini')}
					{$this->image_view(($this->offset + 2), 'nano')}
				</div>
				HTML;
	}

	/**
	 * @param bool|null $first
	 */
	private function image_view(int $offset, string $format, $first = false):string 
	{
		if($offset <= 0 OR $offset > $this->count OR !isset($this->images_name[$offset]))
		{
			$name = 'black';
			$link = false;
		}
		else
		{
			$name = $this->images_name[$offset];
			$link = true;
		}
		$img = '<img style="max-width: 100%;" src="'.Config::PRODUCT_IMAGE_URL . $name . '_' . $format . ProductAttachment::EXTENSION.'">';
		if($link)
		{
			$href = $this->url_helper->modif_get(null, ['img' => $offset]);
			if($first)
			{
				$href = Config::PRODUCT_IMAGE_URL . $name . '_maxi' . ProductAttachment::EXTENSION;
			}
			return '<a href="'.$href.'">'.$img.'</a>';
		}
		return $img;
	}
	
	private function previousLink():string 
	{
		$previous_href = $this->url_helper->modif_get(null, ['img' => $this->currentPage - 1]);
		$previous_class = $this->currentPage <= 1 ? 'disabled': '';
		return <<<HTML
			<span class="page-item $previous_class">
				<a style="display: inline;" class="page-link" href="$previous_href" aria-label="Previous" $previous_class>
					<span aria-hidden="true">&laquo;</span>
					<span class="sr-only"></span>
				</a>
			</span>
			HTML;
	}

	private function nextLink():string
	{	
		$next_href = $this->url_helper->modif_get(null, ['img' => $this->currentPage + 1]);
		$next_class = $this->currentPage >= $this->count ? 'disabled': '';
		return <<<HTML
			<span class="page-item $next_class">
				<a style="display: inline;" class="page-link" href="$next_href" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
					<span class="sr-only"></span>
				</a>
			</span>
			HTML;
	}

}