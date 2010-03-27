<?php use_helper('Form', 'CJK', 'Links', 'Widgets') ?>

<?php #DBG::request() ?>

<?php echo form_tag('study/MyStoriesTable') ?>

  <?php echo ui_select_pager($pager) ?>

  <ul class="stories-list">
<?php foreach($rows as $S): ?>
    <li class="rtkframe">
      <a class="wrapper" href="<?php echo url_for('@study_edit?id='.$S['framenum']) ?>">
        <span class="frame"><?php echo $S['framenum'] ?></span>
        <span class="votes"><?php echo $S['stars'] ?><?php echo $S['kicks'] ?></span>
        <span class="keywo"><?php echo $S['keyword'] ?></span>
        <span class="kanji"><?php echo cjk_lang_ja($S['kanji']) ?></span>
        <span class="bookstyle"><?php echo $S['story'] ?></span>
        <span class="laste">Last edited: <?php echo $S['dispdate'] ?></span>
      </a>
    </li>
<?php endforeach ?>
  </ul>

  <?php echo ui_select_pager($pager) ?>

</form>
