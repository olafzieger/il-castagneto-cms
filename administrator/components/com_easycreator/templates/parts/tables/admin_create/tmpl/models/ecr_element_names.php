<?php
##*HEADER*##

jimport('joomla.application.component.model');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class _ECR_COM_NAME_sModel_ECR_ELEMENT_NAME_s extends JModel
{
    var $_data;

    var $_total = null;

    var $_pagination = null;

    public function __construct()
    {
        parent::__construct();

        $application = JFactory::getApplication();
        $option = JRequest::getCmd('option');

        $this->filter_order_Dir	= $application->getUserStateFromRequest($option
        .'.filter_order_Dir', 'filter_order_Dir', '', 'word');
        ##ECR_MAT_ORDERING_MODEL1##
        $this->filter_order	= $application->getUserStateFromRequest($option
        .'.filter_order', 'filter_order',	'ordering', 'cmd');

        $this->search = $application->getUserStateFromRequest("$option.search", 'search', '', 'string');
        $this->search = JString::strtolower($this->search);

        $limit		= $application->getUserStateFromRequest('global.list.limit'
        , 'limit', $application->getCfg('list_limit'), 'int');

        $limitstart	= $application->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    function getData()
    {
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    private function _buildQuery()
    {
        $where = array();

        if($this->search)
        {
            $where[] = 'LOWER(name) LIKE \''.$this->search.'\'';
        }

        $where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

        ##ECR_MAT_FILTER_MODEL1##
        /*
        if (($this->filter_order != 'title') && ($this->filter_order != 'type_id')
        && ($this->filter_order != 'description') && ($this->filter_order != 'e.ordering')
        && ($this->filter_order != 'id') && ($this->filter_order != 'min')
        && ($this->filter_order != 'def') && ($this->filter_order != 'max')) $this->filter_order = '';*/
        if(($this->filter_order) && ($this->filter_order_Dir))
        $orderby 	= ' ORDER BY '.$this->filter_order.' '.$this->filter_order_Dir;

        $this->_query = ' SELECT *'
        .' FROM #___ECR_TABLE_NAME_'
        .$where
        .$orderby;

        return $this->_query;
    }

    function getList()
    {
        // table ordering
        $lists['order_Dir']	= $this->filter_order_Dir;
        $lists['order']		= $this->filter_order;

        // search filter
        $lists['search'] = $this->search;

        return $lists;
    }

    function getTotal()
    {
        // Load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    function getPagination()
    {
        // Load the content if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal()
            , $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }
}//class
