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
?>
<div id="eglike-<?php echo $module->id; ?>" class="eglikemod<?php echo $modsfx; ?>">
<ul>
<?php foreach($items as $item) : ?>
	<li class="<?php echo $item->numclass; ?>">
		<a href="<?php echo $item->link; ?>" >
			<?php echo $item->title; ?> 
		<span class="likenum">
			 <?php echo $item->sum; ?>
		</span>
		</a> 
	</li>
<?php endforeach; ?>
</ul>
</div>
