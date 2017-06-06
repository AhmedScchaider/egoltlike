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

// Set Lanuage
$language = JFactory::getLanguage();
$lang->load('plg_content_egoltlike', JPATH_ADMINISTRATOR);

// Set Helper
require_once (dirname(__FILE__).'/helper.php');
$helper	= new modEgoltLikeHelper($params);
$items	= $helper->getList();

// Module Params
$modsfx		= $params->get('moduleclass_sfx');
$template	= $params->get('template', 'default');

// Component Params
// $cmparams	= $helper->getCMParams();

// Load Style-sheets
$document = JFactory::getDocument();
$csspath	= 'media/mod_egoltlike/css/';
$lang 		= JFactory::getLanguage();
$isRTL		= $lang->isRTL();
		
JHTML::_('stylesheet', $csspath . 'mod_egoltlike.css');
if($isRTL) 
{
	JHTML::_('stylesheet', $csspath . 'mod_egoltlike.rtl.css');	
}

// Set Icons
$modid = $module->id;
$ldset = $params->get('ldicons', 1);
$like_img = JURI::root(true).'/media/egoltlike/images/sets/'.$ldset.'/like.png';
$dislike_img = JURI::root(true).'/media/egoltlike/images/sets/'.$ldset.'/dislike.png';
$css_desc = 
".eglikemod#eglike-{$modid} ul li.posnum, .eglikemod ul li.nutnum {
	background-image: url('{$like_img}');
}
.eglikemod#eglike-{$modid} ul li.negnum{
	background-image: url('{$dislike_img}');
}";
$document->addStyleDeclaration($css_desc);

// Set Layout
require (JModuleHelper::getLayoutPath('mod_egoltlike', $template.'/default'));
