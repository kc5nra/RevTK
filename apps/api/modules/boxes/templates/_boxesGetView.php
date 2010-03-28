<?php echo '<?' ?>xml version="1.0" encoding="utf-8" ?>
<boxes untestedCount="<?php echo $untestedCardsTotal ?>">
<?php for($boxId = 0; $boxId < count($boxes); $boxId++) { ?>
	<box id="<?php echo $boxId + 1; ?>">
		<expired-cards><?php echo $boxes[$boxId]['expired_cards']; ?></expired-cards>
		<fresh-cards><?php echo $boxes[$boxId]['fresh_cards']; ?></fresh-cards>
		<total-cards><?php echo $boxes[$boxId]['total_cards']; ?></total-cards>
	</box>
<?php } ?>
</boxes>