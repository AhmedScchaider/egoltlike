<?php
/**
 * @package   	Egolt Like
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt - www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 *
 * Name:		Egolt Like
 * License:		GNU/GPL 2
 * Product:		http://www.egolt.com/products/egoltlike
 */

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

// Check Egolt Framework
// defined('_EGOINC') or die('Egolt Framework not installed!');

require_once(JPATH_SITE.'/components/com_content/helpers/route.php');

class modEgoltLikeHelper
{
	var $_params;

	function __construct($params)
	{		
		$this->_params	= $params;
	}

    public function getList()
    {		
		$qt		= $this->_params->get('egqt');
		$showneg = $this->_params->get('egshowneg', 1);
		$db		= JFactory::getDBO();

		// Get Likes
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__egoltlike');
		// $query->where('cid = '.$db->quote($id));
		$query->where('service = '.$db->quote(1));
		$query->order('sum DESC');	
		if(!$showneg)
			$query->where('sum >= '.$db->quote(0));
		
		$db->setQuery( (string)$query, 0, $qt);
		$items = $db->loadObjectList();
		
		foreach ($items as $item) 
		{
			$cid = $item->cid;
			if($item->sum >0)
			{
				$item->sum = '+' . $item->sum;
				$item->numclass = 'posnum';
			}
			elseif($item->sum <0)
			{
				$item->numclass = 'negnum';			
			}
			else
			{
				$item->numclass = 'nutnum';			
			}
			
			$query	= $db->getQuery(true);
			$query->select('a.*');
			$query->from('#__content as a');
			$query->where('id = '.$db->quote($cid));
			$db->setQuery( (string)$query );
			$content = $db->loadObject();
			
			$item->title = $content->title;
			$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($cid, $content->catid));	
		}
		
		return $items;
    }

}
