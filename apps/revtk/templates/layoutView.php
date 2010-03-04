<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_http_metas() ?>
<?php include_metas() ?>
<?php include_title() ?>
<?php include_stylesheets() ?>
<?php include_javascripts() ?>
	<link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
<?php if(has_slot('inline_styles')): ?>
	<style type="text/css">
<?php include_slot('inline_styles') ?>
	</style>
<?php endif ?>
</head>
<body class="<?php echo $_request->getParameter('module').'-'.$_request->getParameter('action'); ?>">

<?php if (0 && CORE_ENVIRONMENT !== 'prod'): ?>
	<div style="position:absolute;z-index:100;left:50%;top:0;opacity:0.6;width:100px;margin-left:-66px;background:#040;color:#fff;font:bold 16px Arial;padding:8px 16px;">
		<?php echo CORE_ENVIRONMENT ?>
	</div>
<?php endif ?>

<!--[if IE]><div id="ie"><![endif]--> 

<?php
function nav_active($nav_id)
{
	$_request = coreContext::getInstance()->getRequest();
	$_page_id = $_request->getParameter('module').'-'.$_request->getParameter('action');
	return ($nav_id==$_page_id) ? true : false;
}
function nav_item($nav_id, $text, $internal_uri, $options = array())
{
	$css_class = array();
	// 'icon' option sets a class name on the LI tag instead of the link itself
	if (isset($options['icon']))
	{
		array_push($css_class, $options['icon']);
		unset($options['icon']);
	}
	$active = nav_active($nav_id);
	if ($active)
	{
		$link = content_tag('span', $text, $options);
	}
	else
	{
		$link = link_to($text, $internal_uri, $options);
	}
	return '<li'.($active ? ' class="active"' : '').'>'.$link.'</li>';
}
?>

<div id="header">
	<h1><img src="/images/2.0/header/header-title.gif" alt="Reviewing the Kanji FORUM" width="194" height="20"></h1>

	<?php echo link_to('<img src="/images/2.0/header/header-kanji.gif" alt="Home">', '@homepage', array('class' => 'header-home')) ?>

	<div class="signin">
		<div class="r"></div>
<?php if(!$_user->isAuthenticated()): ?>
		<div class="m">Already registered? <?php echo link_to('Sign in', 'home/login', array('class' => 'hi')) ?></div>
<?php else: ?>
		<div class="m">Signed in: <strong><?php echo $_user->getUsername() ?></strong><?php echo link_to('Sign out','home/logout') ?></div>
<?php endif ?>
		<div class="l"></div>
	</div>

	<div class="links">
		<?php echo link_to('Learn More', 'about/learnmore') ?>|<?php echo link_to('Members', 'home/memberslist') ?>|<?php echo link_to('Donate', 'about/support', 'class="donate"') ?><?php if (null !== ($forum_url = coreConfig::get('app_forum_url'))): ?>|<a href="<?php echo $forum_url ?>">Forums</a><?php endif ?>
	</div>
	
	<div class="primary">
		<ul>
			<?php echo nav_item('home-index', 'Home', '@homepage') ?>
<?php if (!$_user->isAuthenticated()): ?>
			<?php echo nav_item('about-learnmore', 'Learn More', 'about/learnmore') ?>
			<?php echo nav_item('account-create', 'Register', 'account/create', array('class' => 'profile')) ?>
<?php else: ?>
			<?php echo nav_item('study-index', 'Study', 'study/index') ?>
			<?php #echo nav_item('review-flashcardlist', 'Flashcards', 'review/flashcardlist') ?>
			<?php echo nav_item('manage-index', 'Manage', '@manage') ?>
			<?php echo nav_item('review-index', 'Review', '@overview') ?>
			<?php echo nav_item('misc-reading', 'Reading', 'misc/reading') ?>
			<?php echo nav_item('profile-index', 'Profile', 'profile/index', array('class' => 'profile')) ?>
<?php endif; ?>
<?php if ($_user->hasCredential('admin') && (null !== ($backend_url = coreConfig::get('app_backend_url')))): ?>
			<li><a href="<?php echo $backend_url ?>" style="background:url(/images/backend/icons/brick_go.png) no-repeat 10px 50%;padding-left:32px">Backend</a>
<?php endif; ?>
		</ul>
	</div>
	<div class="clear"></div>
</div>


	<div id="main" class="minwidth">
<?php echo $core_content ?>

			<div id="footer">
				<p>
				<?php echo link_to('home', '@homepage') ?>&nbsp;|&nbsp;
<?php if(coreContext::getInstance()->getController()->getModuleName()=='home'): ?>
				<?php echo link_to('about', 'about/index') ?>&nbsp;|&nbsp;
				<?php echo link_to('contact', '@contact') ?>&nbsp;|&nbsp;
<?php else: ?>
				This website uses the <?php echo link_to('JMDICT', 'about/acknowledgments') ?> and <?php echo link_to('KANJIDIC', 'about/acknowledgments') ?> files.&nbsp;|&nbsp;
<?php endif; ?>
				<span id="dbgtime"><?php echo coreContext::getInstance()->getConfiguration()->profileEnd() ?> sec</span>
				</p>
			</div>

		<div class="clearboth"></div>
	</div>

<!--[if IE]></div><![endif]--> 


<?php // Print a debug log of all SQL queries run on the page 
if (0 && CORE_ENVIRONMENT === 'dev') {
	$_db = coreContext::getInstance()->getDatabase(); $sqlLog = $_db->getDebugLog(); 
?>
	<div style="background:#800;padding:5px 10px;border:1px solid #c80;font:14px/1.2em Consolas, Courier New;color:#fff;">
	  <div style="color:yellow;font-weight:bold;"><?php echo count($sqlLog) ?> querries:</div>
	  <?php echo implode("<br/>\n", $sqlLog); ?>
	</div>
<?php } ?>


<?php if (coreConfig::get('koohii_build')) { use_helper('__Affiliate'); echo analytics_tracking_code(); } ?>

</body>
</html>
