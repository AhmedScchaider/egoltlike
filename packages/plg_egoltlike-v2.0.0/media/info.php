<?php
$prname = 'egoltlike';
$prlabel = 'Egolt Like';
$prslogan = 'Let the users like your contents!';
$prfree = '1';

// Set flag that this is a parent file
define('_JEXEC', 1);

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

$input	= JFactory::getApplication()->input;
if(!$input->get('prv'))
	exit();


$lang = JLanguage::getInstance(JComponentHelper::getParams('com_languages')->get('administrator'));
$isRTL = $lang->isRTL();

$option = $prname;
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('manifest_cache');
$query->from('#__extensions');
$query->where($query->qn('type') . ' = ' . $db->quote('package'));
$query->where($query->qn('element') . ' = ' . $db->quote('pkg_' . $option));
$db->setQuery($query);
$res	= $db->loadResult();
$info	= json_decode($res);
$version = @$info->version;	
		
$dtext = file_get_contents('intro.txt');
?>
<div style="display:none" >
	<?php echo $dtext; ?>
</div>
<link rel="stylesheet" href="<?php echo JUri::root(); ?>css/bootstrap.eg.css" type="text/css" />
<div class="egolt-admin <?php echo ($isRTL) ? 'egrtl' : ''; ?>">
		<div class="row-fluid">
			<div class="span3">
				<center>
					<br>
					<img class="egprlogo" src="../media/<?php echo $prname; ?>/images/about/<?php echo $prname; ?>_big.png">
						<div class="jpane-slider content">
					<div class="inner-about">
						<div class="aversion">Version <?php echo $version; ?></div>
						
						<div class="abuypro"><a href="http://www.egolt.com/go-pro/subscribe/levels?prd=<?php echo $prname; ?>" target="_blank">Buy Subscription</a></div>
						
						<div class="adownload"><a href="http://www.egolt.com/products/<?php echo $prname; ?>#download" target="_blank">Downloads</a></div>
						
						<div class="asupport"><a href="http://www.egolt.com/go-pro/subscribe/levels?prd=<?php echo $prname; ?>" target="_blank">Support</a></div>
						
						<div class="adocumentation"><a href="http://www.egolt.com/userguide/<?php echo $prname; ?>" target="_blank">User Guide</a></div>
						
						<div class="ademo"><a href="http://www.egolt.com/products/<?php echo $prname; ?>#demo" target="_blank">Demo</a></div>
						
						<div class="alicense">
							Licensed under <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL 2</a>
						</div>
						
					</div>
				</div>
				</center>
			</div>
			<div class="span6">
				<div class="panel" style="padding: 5px;">
					<h2 class="jpane-toggler title" id="cpanel-panel-popular"><span><?php echo $prlabel; ?></span></h2>
					<h4><?php echo $prslogan; ?></h4>
					<p class="aboutp">
					<?php echo nl2br($dtext); ?>
					</p>
				</div>
			</div>
			<div class="span3 aboutcl">
				<center>
				<img class="egprlogo" src="../media/<?php echo $prname; ?>/images/about/egolt_big.png">
				<div style="clear:both;"></div>
				<br><br>
				&copy; Powered by <a href="http://www.egolt.com" target="_blank">Egolt</a>
				<br><br>
				<a href="http://www.egolt.com" target="_blank">www.egolt.com</a><br><br>
				2012 - <?php echo date('Y'); ?><br>
				</center>
			</div>
			<div style="clear:both;"></div>
		</div>	
</div>
