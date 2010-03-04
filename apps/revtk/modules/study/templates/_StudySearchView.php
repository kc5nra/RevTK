<h3>Browse</h3>

<div id="browse">
	<span class="note">Number, kanji or keyword:</span>
	<!--form action="/study/" method="get"-->
		<table cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<?php echo input_tag('search', '', 'class="textfield" maxlength="32" id="txtSearch"') ?>
			</td>
			<td><?php
				$prev = $framenum > 1 ? $framenum - 1 : 1;
				echo link_to('<span>Previous</span>', 'study/edit?id='.$prev, array('class' => 'btn_prev', 'accesskey' => 'p'))
				?></td>
			<td><?php
				$next = $framenum < rtkBook::MAXKANJI_VOL3 ? $framenum + 1 : 1;
				echo link_to('<span>Next</span>', 'study/edit?id='.$next, array('class' => 'btn_next', 'accesskey' => 'n'))
				?></td>
		</tr>
		</table>
	<!--form-->
</div>
