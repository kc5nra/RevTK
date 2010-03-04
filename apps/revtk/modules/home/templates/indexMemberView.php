<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  	<div class="col-box col-box-top">

		<h2>Welcome back, <?php echo $_user->getUserName() ?>!</h2>
		
		<div id="home-qs">
			<table cellspacing="0">
			<tr>
				<th><?php 
				if ($curFrameNum >= rtkBook::MAXKANJI_VOL1) {
					echo 'RtK Volume 1<br />completed!';
				} else {
					echo 'Lesson <strong>'.$progress->curLessonNum.' of 56</strong><br />'.$progress->kanjiToGo.' kanji to go';
				}
				?></th>
				<td class="vspacer" rowspan="2"></td>
				<th><?php 
					if ($countExpired<=0) {
						echo 'No expired kanji';
					} else {
						echo '<strong>'.$countExpired.'</strong> '.link_to('expired kanji', '@review', array('class' => 'expired', 'query_string' => 'type=expired&box=all', 'title' => 'Review expired kanji'));
					}
				?></th>
				<td class="vspacer" rowspan="2"></td>
				<th><?php 
					if ($countFailed<=0) {
						echo 'No restudy kanji';
					} else {
						echo '<strong>'.$countFailed.'</strong> '.link_to('study kanji', 'study/failedlist', array(/*'query_string' => 'mode=failed',*/ 'class' => 'failed', 'title' => 'Restudy forgotten kanji'));
					}
				 ?></th>
			</tr>
			<tr>
				<td><?php echo link_to('Progress chart','@progress', array('class' => 'button')) ?></td>
				<td><?php echo link_to('Review','@overview', array('class' => 'button')) ?></td>
				<td><?php echo link_to('Study','study/index', array('class' => 'button')) ?></td>
			</tr>
			</table>

		</div>

<?php if (TrinityAlphaUsersPeer::isUserRegistered($_user->getUserId())): ?>
      <div style="background:#E7FFBD;padding:5px 10px;text-align:center;font-size:85%;line-height:1.4em;">
        <p style="padding:0 0 8px;margin:0;">
	        <strong>Dear Trinity(alpha) users:</strong><br/>
	        As of November 2009 update, Trinity is no longer available.<br/>
	        Please see this <?php echo link_to("forum topic", "http://forum.koohii.com/viewtopic.php?id=4460") ?> for more information and questions.
	      </p>
	      <p style="font-size:13px;line-height:1em;margin:0;padding:5px 0;">
          Download your flashcards: <?php echo link_to("Vocabulary", "member/ExportTrinity?mode=vocab") ?> - <?php echo link_to("Sentences", "member/ExportTrinity?mode=sentences") ?>
        </p> 
      </div>
<?php endif ?>


	</div>

	<div class="col-box col-box-bis">
		
		<h2>Site News</h2>
		
		<?php include_partial('news/list', array('newsPosts' => SitenewsPeer::getMostRecentPosts())) ?>
		
		...more in the <?php echo link_to('news archive','news/index') ?>.
	
	</div>

  </div>

</div>
