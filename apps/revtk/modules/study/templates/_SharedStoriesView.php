	<div id="sharedstories-new">
		<div class="title JsNewest">
			Newest and updated stories (<span><?php echo count($new_stories) ?></span>)
		</div>
<?php foreach($new_stories as $R): ?>
		<div class="sharedstory">
			<div class="meta">
				<span class="author"><?php echo $R['author'] ?></span>
				<span class="lastmodified">Last edited: <?php echo $R['lastmodified'] ?></span>
			</div>
			<div class="rtkframe">
				<div class="action" id="story-<?php echo $R['userid'] ?>-<?php echo $R['framenum'] ?>" appv1="<?php echo $R['stars'] ?>" appv2="<?php echo $R['kicks'] ?>">
					<span></span>
					<a href="#" class="star"><?php echo $R['stars'] ?>&nbsp;</a><a href="#" class="report"><?php echo $R['kicks'] ?>&nbsp;</a><a href="#" class="copy">&nbsp;</a>
				</div>
				<div class="bookstyle"><?php echo $R['text'] ?></div>
			</div>
		</div>
<?php endforeach ?>
	</div>

	<div id="sharedstories-fav">
		<div class="title">
			Favourite stories (<span><?php echo count($old_stories) ?></span>)
		</div>
<?php foreach($old_stories as $R): ?>
		<div class="sharedstory">
			<div class="meta">
				<span class="author"><?php echo $R['author'] ?></span>
				<span class="lastmodified">Last edited: <?php echo $R['lastmodified'] ?></span>
			</div>
			<div class="rtkframe">
				<div class="action" id="story-<?php echo $R['userid'] ?>-<?php echo $R['framenum'] ?>" appv1="<?php echo $R['stars'] ?>" appv2="<?php echo $R['kicks'] ?>">
					<span></span>
					<a href="#" class="star"><?php echo $R['stars'] ?>&nbsp;</a><a href="#" class="report"><?php echo $R['kicks'] ?>&nbsp;</a><a href="#" class="copy">&nbsp;</a>
				</div>
				<div class="bookstyle"><?php echo $R['text'] ?></div>
			</div>
		</div>
<?php endforeach ?>
	</div>
