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

jimport('joomla.plugin.plugin');

class plgContentEgoltLike extends JPlugin
{
	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		
		// Load language strings
		$this->loadLanguage();
	}

	public function onContentPrepare ($context, &$article, &$params, $limitstart)
	{
		$document = JFactory::getDocument();
		
		// Add stylesheet
		$document->addStyleSheet(JURI::root(true).'/media/egoltlike/css/egoltlike.css');
		$ldset = $this->params->get('ldicons', 1);
		$ldcss = JURI::root(true).'/media/egoltlike/images/sets/'.$ldset.'/style.css';
		$ldfile = JPATH_SITE.'/media/egoltlike/images/sets/'.$ldset.'/style.css';
		if(file_exists($ldfile))
			$document->addStyleSheet($ldcss);
		

		// Add ajax script processor
		$document->addScript(JURI::root(true).'/media/egoltlike/js/ajax.js');	

		$document->addScriptDeclaration( "var rooturi = '".JURI::base(true)."';");
		$document->addScriptDeclaration( "var eg_th_str = '". JText::_('PLG_CONTENT_EGOLTLIKE_THANKS') ."';");
		$document->addScriptDeclaration( "var eg_vt_str = '". JText::_('PLG_CONTENT_EGOLTLIKE_VOTED_BEFORE') ."';");
		$document->addScriptDeclaration( "var eg_ac_str = '". JText::_('PLG_CONTENT_EGOLTLIKE_NOACCESS') ."';");
	}

	public function EgoltLikeModeling($article, $params) {
		// Get article id
		$id = $article->id;
		
		// Select like status of content
		$db	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__egoltlike');
		$query->where('cid = '.$db->quote($id));
		$query->where('service = '.$db->quote(1));
		$db->setQuery( (string)$query );
		
		// Load result
		$res = $db->loadObject();
		
		// Set default value for new items
		if(empty($res))
		{
			$res = new stdClass();
			$res->pos = 0;
			$res->neg = 0;
			$res->sum = 0;		
		}
		
		return $res;
	}
		
	public function EgoltLikePrepare ($article, $params)
	{
		$input	= JFactory::getApplication()->input;
		$id = $article->id;
		
		// View Restriction
		$view = $input->get('view');
		$showinart = $this->params->get('showinart', 1);
		$showincat = $this->params->get('showincat', 1);
		if( ($view == 'article') and (!$showinart) )
			return false;
		if( ($view == 'category') and (!$showincat) )
			return false;	

		// Categories and Articles filter
		if($this->params->get('encats') or $this->params->get('discats') or $this->params->get('disarts'))
		{
			$db	= JFactory::getDBO();
			if( $this->params->get('disarts') and (in_array($id, $this->params->get('disarts'))) )
			{
				return false;	
			}
				
			if($this->params->get('discats') or $this->params->get('encats'))
			{
				// get article category
				$query = $db->getQuery(true);
				$query->select('id, catid');
				$query->from('#__content');
				$query->where('id = '.$db->quote($id));
				$db->setQuery( (string)$query );
				$cnres = $db->loadObject();
				$catid = $cnres->catid;
				
				if( $this->params->get('discats') and (in_array($catid, $this->params->get('discats'))) )
				{
					return false;
				}
				
				if( $this->params->get('encats') and (!in_array($catid, $this->params->get('encats'))) )
				{
					return false;		
				}			
			}	
		}			
		
		// Geting data needed
		$res = $this->EgoltLikeModeling($article, $params);
		$ldset = $this->params->get('ldicons', 1);
		
		// Start of output
		$output  = '<!-- Egolt Like -->';
		$output .= '<!-- More Info: http://www.egolt.com/products/egoltlike -->';
		
		$output .= '<div class="egoltlike egalign-'. $this->params->get('alignment', 'right') .'" id="egoltlike_' . $id . '">';

		$output .= '<div class="eglike_act" >';
		// Like value
		if ($this->params->get('sval_show', 1))
		{
			$output .= '<div class="grid" id="pos_grid">';
			$output .= '<div class="elike_val" id="elike_val_' . $id . '">';
			if($this->params->get('zval_show', 0) OR ($res->pos))
				$output .= $res->pos;
			$output .= '</div>';
			$output .= '</div>';
		}
		
		// Like button
		$output .= '<div class="grid">';
		$output .= '<div class="elike">';
		$output .= '<a
						href="javascript:void(null)" ';
		if($this->params->get('sval_show', 1))
			$output .= '	onclick="javascript:EgoltLike('.$id.', 1, '.$res->pos.', '.$res->neg.', true);" ';
		else
			$output .= '	onclick="javascript:EgoltLike('.$id.', 1, '.$res->pos.', '.$res->neg.', false);" ';			
		$output .= '	title="'. JText::_('PLG_CONTENT_EGOLTLIKE_LIKE') .'"
					>';
		$output .= '<img src="'.JURI::root(true).'/media/egoltlike/images/sets/'.$ldset.'/like.png" border="0" />';
		$output .= '</a>';
		$output .= '</div>';
		$output .= '</div>';
		
		// Results summuray
		if($res->sum > 0) {
			$sumclass = 'pos';
			$sumpref = '+';
		}
		else if($res->sum < 0) {
			$sumclass = 'neg';
		}
		else {
			$sumclass = 'neu';
		}		
		$output .= '<div class="grid" id="sum_grid">';
		$output .= '<div class="esum '.$sumclass.'" id="esum_' . $id . '">';
		$output .= @$sumpref . $res->sum;
		$output .= '</div>';
		$output .= '</div>';
		
		// Dislike button
		$output .= '<div class="grid">';
		$output .= '<div class="edislike">';
		$output .= '<a
						href="javascript:void(null)" ';
		if($this->params->get('sval_show', 1))
			$output .= '	onclick="javascript:EgoltLike('.$id.', 2, '.$res->pos.', '.$res->neg.', true);" ';
		else
			$output .= '	onclick="javascript:EgoltLike('.$id.', 2, '.$res->pos.', '.$res->neg.', false);" ';			
		$output .= '	title="'. JText::_('PLG_CONTENT_EGOLTLIKE_DISLIKE') .'"
					>';
		$output .= '<img src="'.JURI::root(true).'/media/egoltlike/images/sets/'.$ldset.'/dislike.png" border="0" />';
		$output .= '</a>';
		$output .= '</div>';
		$output .= '</div>';

		// Dislike value
		if ($this->params->get('sval_show', 1))
		{		
			$output .= '<div class="grid" id="neg_grid">';
			$output .= '<div class="edislike_val" id="edislike_val_' . $id . '">';
			if($this->params->get('zval_show', 0) OR ($res->neg))
				$output .= $res->neg;
			$output .= '</div>';
			$output .= '</div>';
		}
		
		$output .= '<div class="clear"> </div>';
		$output .= '</div>';
		
		// Loading Info block
		$output .= '<div class="eglike_info">';
		$output .= '<div class="eloading" id="eloading_' . $id . '">';
		$output .= ' ';
		$output .= '</div>';
		$output .= '</div>';
		
		// End of output
		$output .= '</div>';
				
		return $output;	
	}
	
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 1)
	{
		if($this->params->get('pos_show', 'beforec') == 'beforec')
		{
			return $this->EgoltLikePrepare($article, $params);
		}
 	}

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart = 1)
	{
		if($this->params->get('pos_show', 'beforec') == 'afterc')
		{
			return $this->EgoltLikePrepare($article, $params);		
		}
	}	
	
}
