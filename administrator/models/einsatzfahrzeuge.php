<?php
/**
 * @version     3.15.0
 * @package     com_einsatzkomponente
 * @copyright   Copyright (C) 2017 by Ralf Meyer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ralf Meyer <ralf.meyer@mail.de> - https://einsatzkomponente.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
/**
 * Methods supporting a list of Einsatzkomponente records.
 */
class EinsatzkomponenteModeleinsatzfahrzeuge extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'ordering', 'a.ordering',
                'name', 'a.name',
                'detail1_label', 'a.detail1_label',
                'detail1', 'a.detail1',
                'detail2_label', 'a.detail2_label',
                'detail2', 'a.detail2',
                'detail3_label', 'a.detail3_label',
                'detail3', 'a.detail3',
                'detail4_label', 'a.detail4_label',
                'detail4', 'a.detail4',
                'detail5_label', 'a.detail5_label',
                'detail5', 'a.detail5',
                'detail6_label', 'a.detail6_label',
                'detail6', 'a.detail6',
                'detail7_label', 'a.detail7_label',
                'detail7', 'a.detail7',
                'department', 'a.department',
                'ausruestung', 'a.ausruestung',
                'link', 'a.link',
                'image', 'a.image',
                'desc', 'a.desc',
                'state', 'a.state',
                'created_by', 'a.created_by',
            );
        }
        parent::__construct($config);
    }
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
        
        
        
		// Load the parameters.
		$params = JComponentHelper::getParams('com_einsatzkomponente');
		$this->setState('params', $params);
		// List state information.
		parent::populateState('a.ordering', 'asc');
	}
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');
		return parent::getStoreId($id);
	}
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__eiko_fahrzeuge AS a');
		// Join over the foreign key 'auswahl_orga'
		$query->select('#__eiko_ausruestung_1662678.name AS ausruestung_name_1662678');
		$query->join('LEFT', '#__eiko_ausruestung AS #__eiko_ausruestung_1662678 ON #__eiko_ausruestung_1662678.id = a.ausruestung');
		// Join over the user field 'created_by'
		$query->select('created_by.name AS created_by');
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
        $query->where('a.state = '.(int) $published);
    } else if ($published === '') {
        $query->where('(a.state IN (0, 1,2))');
    }
    
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.name LIKE '.$search.'  OR  a.detail1 LIKE '.$search.'  OR  a.detail2 LIKE '.$search.' )');
			}
		}
        
        
        
        
		// Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }
		return $query;
	}
	
    public function getItems() {
        $items = parent::getItems();
        
		foreach ($items as $oneItem) {

			if (isset($oneItem->ausruestung)) {
				$values = explode(',', $oneItem->ausruestung);

				$textValue = array();
				foreach ($values as $value){
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
							->select('name')
							->from('#__eiko_ausruestung')
							->where('id = ' . $db->quote($db->escape($value)));
					$db->setQuery($query);
					$results = $db->loadObject();
					if ($results) {
						$textValue[] = $results->name;
					}
				}

			$oneItem->ausruestung = !empty($textValue) ? implode(', ', $textValue) : $oneItem->ausruestung;

			}


		}
        return $items;
    }
	
}
