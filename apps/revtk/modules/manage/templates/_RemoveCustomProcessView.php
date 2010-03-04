<?php use_helper('Form', 'Validation', 'Widgets') ?>
<?php echo form_tag('manage/RemoveCustomProcess', array('class' => 'main-form')) ?>

	<?php echo form_errors() ?>

	<p> The following <strong><?php echo $count ?></strong> kanji flashcard(s) have been removed:</p>
	
	<div style="background:#E7F5CD;color:#000;padding:5px;margin:0 0 1em;">
<?php
	$kanjis = array();
	foreach ($cards as $id)
	{
		$kanjis[] = rtkBook::getKanjiForIndex($id);
	}
	echo implode(', ', $kanjis);
?>
	</div>

	<p><a href="#" class="proceed" onclick="return ManageFlashcards.load(this,{'reset':true});">Remove more cards</a></p>

</form>
