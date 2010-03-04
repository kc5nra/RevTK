	<div class="frame">
		<ul>
<?php foreach(ReviewsPeer::getRestudyKanjiList($_user->getUserId()) as $R): ?>
			<li<?php echo $R['framenum']==$framenum ? ' class="selected"' : '' ?>>
				<span><?php echo $R['framenum'] ?></span>
				<?php $kw = preg_replace('/\//', '<br/>', $R['keyword']); echo link_to($kw, 'study/edit?id='.$R['framenum']) ?>
			</li>
<?php endforeach ?>
		</ul>
		<div class="clear"></div>
	</div>
