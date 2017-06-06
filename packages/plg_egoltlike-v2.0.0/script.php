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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
 
class plgcontentEgoltLikeInstallerScript
{
	function preflight( $type, $parent ) {
		if ( $type == 'update' ) {
			$jversion = new JVersion();
	 
			// get new version
			$newRelease = $parent->get( "manifest" )->version;
			
			// get old version
			$oldRelease = $this->getParam('version');
			
			// abort if the plugin being installed is not newer
			if ( version_compare( $newRelease, $oldRelease, 'gt' ) ) {
				if ( version_compare( $oldRelease, '2.0.0', 'lt' ) )
				{
					$db = JFactory::getDbo();
					
					$query =
					"CREATE TABLE IF NOT EXISTS `#__egoltlike` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `service` int(3) NOT NULL,
					  `cid` int(11) NOT NULL,
					  `lastip` varchar(50) NOT NULL,
					  `pos` int(10) NOT NULL DEFAULT '0',
					  `neg` int(10) NOT NULL DEFAULT '0',
					  `sum` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `egoltlike_idx` (`cid`)
					);
					";
					$db->setQuery($query);
					$db->execute();	

					$query =
					"ALTER TABLE  `#__egoltlike` ADD  `lastdate` DATETIME NOT NULL AFTER `lastip`;
					";
					$db->setQuery($query);
					$db->execute();
					
					$query =
					"UPDATE  `#__egoltlike` SET  `lastdate` =  NOW();
					";
					$db->setQuery($query);
					$db->execute();
					
					$query =
					"CREATE TABLE IF NOT EXISTS `#__egoltlike_logs` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `service` int(3) NOT NULL,
					  `cid` int(11) NOT NULL,
					  `logip` varchar(50) NOT NULL,
					  `logdate` datetime NOT NULL,
					  `rate` int(2) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `egoltlike_idx` (`cid`)
					);
					";
					$db->setQuery($query);
					$db->execute();
					
					$query =
					"INSERT INTO `#__egoltlike_logs` (`service`, `cid`, `logip`, `logdate`, `rate`) 
					SELECT `service`, `cid`, `lastip`, `lastdate`, `sum` from `#__egoltlike`;
					";
					$db->setQuery($query);
					$db->execute();
					
					JFactory::getApplication()->enqueueMessage('Database Structures Updated!');
				}
			}
		}
	}
	
	// get a variable from the manifest file (actually, from the manifest cache)
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element="egoltlike" and folder="content" ');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
	
}
