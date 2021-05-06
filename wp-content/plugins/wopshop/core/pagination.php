<?php
class Pagination
{

	public $limitstart = null;

	public $limit = null;

	public $total = null;

	public $pagesStart;

	public $pagesStop;

	public $pagesCurrent;

	public $pagesTotal;

	protected $viewall = false;

	protected $additionalUrlParams = array();

	protected $app = null;

	protected $data;

	public function __construct($total, $limitstart, $limit, $app = null)
	{
		// Value/type checking.
		$this->total = (int) $total;
		$this->limitstart = (int) max($limitstart, 0);
		$this->limit = (int) max($limit, 0);
		$this->app = $app ? $app : Factory::getApplication();

		if ($this->limit > $this->total)
		{
			$this->limitstart = 0;
		}

		if (!$this->limit)
		{
			$this->limit = $total;
			$this->limitstart = 0;
		}

		/*
		 * If limitstart is greater than total (i.e. we are asked to display records that don't exist)
		 * then set limitstart to display the last natural page of results
		 */
		if ($this->limitstart > $this->total - $this->limit)
		{
			$this->limitstart = max(0, (int) (ceil($this->total / $this->limit) - 1) * $this->limit);
		}

		// Set the total pages and current page values.
		if ($this->limit > 0)
		{
			$this->pagesTotal = ceil($this->total / $this->limit);
			$this->pagesCurrent = ceil(($this->limitstart + 1) / $this->limit);
		}

		// Set the pagination iteration loop values.
		$displayedPages = 10;
		$this->pagesStart = $this->pagesCurrent - ($displayedPages / 2);

		if ($this->pagesStart < 1)
		{
			$this->pagesStart = 1;
		}

		if ($this->pagesStart + $displayedPages > $this->pagesTotal)
		{
			$this->pagesStop = $this->pagesTotal;

			if ($this->pagesTotal < $displayedPages)
			{
				$this->pagesStart = 1;
			}
			else
			{
				$this->pagesStart = $this->pagesTotal - $displayedPages + 1;
			}
		}
		else
		{
			$this->pagesStop = $this->pagesStart + $displayedPages - 1;
		}

		// If we are viewing all records set the view all flag to true.
		if ($limit == 0)
		{
			$this->viewall = true;
		}
	}


	public function setAdditionalUrlParam($key, $value)
	{
		// Get the old value to return and set the new one for the URL parameter.
		$result = isset($this->additionalUrlParams[$key]) ? $this->additionalUrlParams[$key] : null;

		// If the passed parameter value is null unset the parameter, otherwise set it to the given value.
		if ($value === null)
		{
			unset($this->additionalUrlParams[$key]);
		}
		else
		{
			$this->additionalUrlParams[$key] = $value;
		}

		return $result;
	}


	public function getAdditionalUrlParam($key)
	{
		$result = isset($this->additionalUrlParams[$key]) ? $this->additionalUrlParams[$key] : null;

		return $result;
	}


	public function getRowOffset($index)
	{
		return $index + 1 + $this->limitstart;
	}


	public function getData()
	{
		if (!$this->data)
		{
			$this->data = $this->_buildDataObject();
		}

		return $this->data;
	}


	public function getPagesLinks()
	{
		// Build the page navigation list.
		$data = $this->_buildDataObject();

		$list = array();
		$list['prefix'] = $this->prefix;

		$itemOverride = false;
		$listOverride = false;

		// Build the select list
		if ($data->all->base !== null)
		{
			$list['all']['active'] = true;
			$list['all']['data'] = $this->_item_active($data->all);
		}
		else
		{
			$list['all']['active'] = false;
			$list['all']['data'] = $this->_item_inactive($data->all);
		}

		if ($data->start->base !== null)
		{
			$list['start']['active'] = true;
			$list['start']['data'] = $this->_item_active($data->start);
		}
		else
		{
			$list['start']['active'] = false;
			$list['start']['data'] = $this->_item_inactive($data->start);
		}

		if ($data->previous->base !== null)
		{
			$list['previous']['active'] = true;
			$list['previous']['data'] = $this->_item_active($data->previous);
		}
		else
		{
			$list['previous']['active'] = false;
			$list['previous']['data'] = $this->_item_inactive($data->previous);
		}

		// Make sure it exists
		$list['pages'] = array();

		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null)
			{
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = $this->_item_active($page);
			}
			else
			{
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null)
		{
			$list['next']['active'] = true;
			$list['next']['data'] = $this->_item_active($data->next);
		}
		else
		{
			$list['next']['active'] = false;
			$list['next']['data'] = $this->_item_inactive($data->next);
		}

		if ($data->end->base !== null)
		{
			$list['end']['active'] = true;
			$list['end']['data'] = $this->_item_active($data->end);
		}
		else
		{
			$list['end']['active'] = false;
			$list['end']['data'] = $this->_item_inactive($data->end);
		}

		if ($this->total > $this->limit)
		{
			return $this->_list_render($list);
		}
		else
		{
			return '';
		}
	}


	public function getPaginationPages()
	{
		$list = array();

		if ($this->total > $this->limit)
		{
			// Build the page navigation list.
			$data = $this->_buildDataObject();

			// All
			$list['all']['active'] = (null !== $data->all->base);
			$list['all']['data']   = $data->all;

			// Start
			$list['start']['active'] = (null !== $data->start->base);
			$list['start']['data']   = $data->start;

			// Previous link
			$list['previous']['active'] = (null !== $data->previous->base);
			$list['previous']['data']   = $data->previous;

			// Make sure it exists
			$list['pages'] = array();

			foreach ($data->pages as $i => $page)
			{
				$list['pages'][$i]['active'] = (null !== $page->base);
				$list['pages'][$i]['data']   = $page;
			}

			$list['next']['active'] = (null !== $data->next->base);
			$list['next']['data']   = $data->next;

			$list['end']['active'] = (null !== $data->end->base);
			$list['end']['data']   = $data->end;
		}

		return $list;
	}

	protected function _list_render($list)
	{
		// Reverse output rendering for right-to-left display.
		$html = '<ul>';
		$html .= '<li class="pagination-start">' . $list['start']['data'] . '</li>';
		$html .= '<li class="pagination-prev">' . $list['previous']['data'] . '</li>';

		foreach ($list['pages'] as $page)
		{
			$html .= '<li>' . $page['data'] . '</li>';
		}

		$html .= '<li class="pagination-next">' . $list['next']['data'] . '</li>';
		$html .= '<li class="pagination-end">' . $list['end']['data'] . '</li>';
		$html .= '</ul>';

		return $html;
	}
	protected function _item_active(PaginationObject $item)
	{
		$title = '';
		$class = '';

		if (!is_numeric($item->text))
		{
			$title = ' title="' . $item->text . '"';
		}

		return '<a' . $title . ' href="' . $item->link . '" class="' . $class . 'pagenav">' . $item->text . '</a>';
	}

	protected function _item_inactive(PaginationObject $item)
	{
            return '<span class="pagenav">' . $item->text . '</span>';
	}

	protected function _buildDataObject()
	{
            $controller =  Request::getString('controller')? Request::getString('controller') : 'productlist';
            $task = Request::getString('task')? Request::getString('task') : 'display';
		$data = new stdClass;

		// Build the additional URL parameters string.
		$params = '';

		if (!empty($this->additionalUrlParams))
		{
			foreach ($this->additionalUrlParams as $key => $value)
			{
				$params .= '&' . $key . '=' . $value;
			}
		}

		$data->all = new PaginationObject(_('JLIB_HTML_VIEW_ALL'), $this->prefix);

		if (!$this->viewall)
		{
			$data->all->base = '0';
			$data->all->link = SEFLink('controller='.$controller.'&task=' . $task . $params .'&limitstart=');
		}

		// Set the start and previous data objects.
		$data->start = new PaginationObject(_WOP_SHOP_START, $this->prefix);
		$data->previous = new PaginationObject(_WOP_SHOP_PREV, $this->prefix);

		if ($this->pagesCurrent > 1)
		{
			$page = ($this->pagesCurrent - 2) * $this->limit;

			// Set the empty for removal from route
			// @todo remove code: $page = $page == 0 ? '' : $page;

			$data->start->base = '0';
			$data->start->link = SEFLink('controller='.$controller.'&task=' . $task . $params.'&limitstart=0');
			$data->previous->base = $page;
			$data->previous->link = SEFLink('controller='.$controller.'&task=' . $task . $params.'&limitstart=' . $page);
		}

		// Set the next and end data objects.
		$data->next = new PaginationObject(_WOP_SHOP_NEXT, $this->prefix);
		$data->end = new PaginationObject(_WOP_SHOP_END, $this->prefix);

		if ($this->pagesCurrent < $this->pagesTotal)
		{
			$next = $this->pagesCurrent * $this->limit;
			$end = ($this->pagesTotal - 1) * $this->limit;

			$data->next->base = $next;
			$data->next->link = SEFLink('controller='.$controller.'&task=' . $task . $params .'&limitstart=' . $next);
			$data->end->base = $end;
			$data->end->link = SEFLink('controller='.$controller.'&task=' . $task . $params.'&limitstart=' . $end);
		}

		$data->pages = array();
		$stop = $this->pagesStop;

		for ($i = $this->pagesStart; $i <= $stop; $i++)
		{
			$offset = ($i - 1) * $this->limit;

			$data->pages[$i] = new PaginationObject($i, $this->prefix);

			if ($i != $this->pagesCurrent || $this->viewall)
			{
				$data->pages[$i]->base = $offset;
				$data->pages[$i]->link = SEFLink('controller='.$controller.'&task=' . $task . $params.'&limitstart=' . $offset);
			}
			else
			{
				$data->pages[$i]->active = true;
			}
		}

		return $data;
	}

}
