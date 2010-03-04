<?php use_helper('Widgets') ?>
<?php use_stylesheet('/css/2.0/widgets.css') ?>

<div class="layout-home">

<?php use_helper('Date') ?>

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
	<div class="col-box col-box-top block">

		<div class="app-header">
			<h2>Profile <span>&raquo;</span> <?php echo $self_account ? 'My Profile' : $user['username'] ?></h2>
			<div class="clear"></div>
		</div>

	<table cellspacing="0" class="blocky">
		<tr class="head">
			<th colspan="2">
				<?php if ($self_account): ?>
					<div style="float:right;"><?php echo link_to('Edit','account/edit') ?></div>
				<?php endif ?>
				Member Details
			</th>
		</tr>
		<tr><td>Username :</td><td><b><?php echo escape_once($user['username']) ?></b></td></tr>
<?php if ($self_account): ?>
		<tr><td>Email :</td><td><?php echo escape_once($user['email']) ?>
		   <div style="font:11px/1em Verdana, sans-serif;color:#484;font-style:italic;text-align:center;white-space:nowrap">(your email is not visible to anyone else)</div>
		 </td></tr>
<?php endif ?>
		<tr><td>Location :</td><td><?php echo escape_once($user['location']) ?></td></tr>
		<tr><td>Timezone :</td><td><?php echo rtkTimezones::$timezones[ (string)$user['timezone'] ]    ?></td></tr>
	</table>

	<table cellspacing="0" class="blocky">
		<tr class="head">
			<th colspan="2">Member Stats</th>
		</tr>
		<tr><td>Kanji Count :</td><td><?php echo escape_once($kanji_count) ?></td></tr>
		<tr><td>Total Reviews :</td><td><?php echo escape_once($total_reviews) ?></td></tr>
		<tr><td>Joined :</td><td><?php echo date('j M Y', $user['ts_joindate']) ?></td></tr>
		<tr><td>Last Login:</td><td><?php echo time_ago_in_words($user['ts_lastlogin'], true) ?> ago</td></tr>
	</table>

<?php if ($forum_uid && $self_account): ?>
	<p><a href="<?php echo coreConfig::get('app_forum_url') ?>/profile.php?id=<?php echo $forum_uid ?>">Edit my RevTK Forum profile</a>.</p>

<?php elseif ($forum_uid): ?>
	<p><a href="<?php echo coreConfig::get('app_forum_url') ?>/profile.php?id=<?php echo $forum_uid ?>">View this member's profile in the community forums</a> for contact information,
		website address, etc. (if any of those are provided).
	</p>
<?php endif ?>

<?php if ($self_account): ?>
	<p><?php echo ui_ibtn('Edit Account', 'account/edit', array('icon' => 'edit')) ?></p>


	<p><?php echo ui_ibtn('Change Password', 'account/password', array('icon' => 'edit')) ?></p>
<?php endif ?>

	</div>
  </div>
 
</div>
