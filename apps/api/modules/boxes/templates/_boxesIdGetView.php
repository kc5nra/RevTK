<?php echo '<?' ?>xml version="1.0" encoding="utf-8" ?>
<cards>
<?php foreach($flashcardData as $data) { ?>
	<card id="<?php echo $data->id ?>">
		<keyword><?php echo $data->keyword ?></keyword>
		<kanji><?php echo $data->kanji ?></kanji>
		<onyomi><?php echo $data->onyomi ?></onyomi>
		<lesson-number><?php echo $data->lessonnum ?></lesson-number>
		<stroke-count><?php echo $data->strokecount ?></stroke-count>
	</card>
<?php } ?>
</cards>