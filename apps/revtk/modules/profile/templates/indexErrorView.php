<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
	<div class="col-box col-box-top content">

	<h2>Member Profile</h2>

	<p> Sorry, the user <strong><?php echo escape_once($_request->getParameter('username')) ?></strong> could not be found.</p>

	<p>What's next:</p>
	
	<ul>
	  	<li><a href="javascript:history.go(-1)">Go back to previous page</a></li>
	  	<li><?php echo link_to('Go to Homepage', '@homepage') ?></li>
	</ul>

	</div>
  </div>
 
</div>
