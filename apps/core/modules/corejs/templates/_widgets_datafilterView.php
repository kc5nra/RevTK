<?php use_helper('Widgets') ?>

<?php
	$links = array(
		'lorem' => array(
			'name' 		    => 'Most Lorem',
			'internal_uri'	=> '@homepage'
		),
		'ipsum' => array(
			'name' 		    => 'Most Ipsum',
			'options'       => array('onclick' => 'alert("Hi!")')
		),
		'picks' => array(
			'name' 		    => 'Staff Picks',
			'options'       => array('query_string' => 'i_like_apples=yes')
		)
	);

?>

<h2>DataFilter</h2>

<p style="color:red">TODO: rename to "Ribbon"</p>

<p> The uiDataFilter class represents a list of links (urls or javascript) that
	let the user filter the associated content, there is always an active link
	that corresponds to the current view "filter".

<?php echo ui_data_filter('View:', $links) ?>

<p>
<p> Without caption, different container width:

<div style="width:70%">
  <?php echo ui_data_filter(false, $links) ?>
</div>