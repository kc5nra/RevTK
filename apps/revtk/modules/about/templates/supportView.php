<div class="layout-home">
<?php include_partial('home/homeSide') ?>

  <div class="col-main">
	  <div class="col-box col-box-top">

	<h2>Support Reviewing the Kanji</h2>
	
	<p>
		My goal is to be able to devote more of my time to improving and expanding the website,
		because <strong>this is what I love to do</strong>. I work on the website in my spare time.
		Your donations will help me towards this goal.<br />
		<br />
		<strong>Thank you for your support and encouragement!</strong>
	</p>
	
	<h2>MAKE A DONATION</h2>

  <?php use_helper('LocalAssets'); echo get_local_content(__FILE__, 'donateForm'); ?>
  
	
	<h2>Make a gift</h2>
	
	<table class="layout" cellspacing="0">
	<tr>
		<td width="100" class="ta-center">
			<a href="http://www.amazon.com/gp/registry/wishlist/33UT82YOX1B2S" title="Go to Fabrice's Amazon.com Wish List" target="_blank"><img src="/images/1.0/extern/amazon_wishlist.gif" width="74" alt="Fabrice's Amazon.com Wish List" height="42" border="0" /></a>
		</td>
		<td>&nbsp;&nbsp;</td>
		<td width="80%">
			If you like the warm and fuzzy feeling of making a gift, have a look at my <a href="http://www.amazon.com/gp/registry/wishlist/33UT82YOX1B2S" target="_blank">Amazon.com wish list</a>.<br />
			<br/>
		</td>
	</tr>
	<tr>
		<td class="ta-center">
			<a href="http://www.amazon.co.uk/gp/registry/BHIJXT6E4U0N" title="Go to Fabrice's Amazon.co.uk Wish List" target="_blank"><img src="/images/1.0/extern/amazon_wishlist_uk.gif" width="74" alt="Fabrice's Amazon.co.uk Wish List" height="42" border="0" /></a>
		</td>
		<td>&nbsp;&nbsp;</td>
		<td width="80%">
			Because I live in Belgium you may want to use the <a href="http://www.amazon.co.uk/gp/registry/BHIJXT6E4U0N" target="_blank">Amazon UK Wish List</a> instead, the shipping costs should be lower.
		</td>
	</tr>
	</table>
	
	
	<h2>Questions, other ways of support</h2>
	
	<p>
		Feel free to <?php echo link_to('contact Fabrice', '@contact') ?> if you have any questions regarding the donations and other ways
		for supporting the website. Thank you!
	</p>

    </div>
  </div>

</div>
