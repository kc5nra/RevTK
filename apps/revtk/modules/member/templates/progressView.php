<?php use_helper('Widgets', 'Gadgets') ?>
<?php use_stylesheet('/css/2.0/widgets.css') ?>
<?php slot('inline_styles') ?>
#progress-chart .active td { background-color:#E5F4A3; color:#000; }
<?php end_slot() ?>
<div class="layout-default">

  <div class="col-main col-box">

    <div class="app-header">
      <h2><?php echo link_to('Home','@homepage') ?> <span>&raquo;</span> Check your progress</h2>
      <div class="clearboth"></div>
    </div>
  
    <p> This chart represents all the lessons from Remembering the Kanji Volume 1, including the number of kanji
      required to complete each lesson. &nbsp;
      <?php echo link_to('Read related article.','@news_by_id?id=30', array('class' => 'link-article')) ?></p>
  
    <?php if ($activeLessons === 0): ?>
      <div class="confirmwhatwasdone">
        <p>You have not added flashcards yet. <?php echo link_to('Add flashcards', '@manage') ?>.
      </div>
    <?php else: ?>
    
      <?php if ($currentLesson): ?>
        <p> <strong>Your current goal is to complete <?php echo ($currentLesson < 57) ? 'lesson '.$currentLesson : 'RtK Volume 3' ?>.</strong></p>
      <?php else: ?>
        <p> <strong>Congratulations! You have completed Remembering the Kanji, Volume 1.</strong></p>
      <?php endif ?>
    
    <?php endif; ?>
  
    <?php #progress chart table ?>
    <table id="progress-chart" class="uiTabular" cellspacing="0">
     <thead>
      <tr>
        <th style="width:15%;"><span class="hd">Lesson</span></th>
        <th><span class="hd">Status</span></th>
        <th style="width:15%;">&nbsp;</th>
      </tr>
     </thead>
     <tbody>
     <?php $i=1; foreach ($lessons as $lkey => $less): ?>
      <tr<?php 
        $cssActive = ($lkey == $currentLesson) ? 'active' : ''; echo ($i++ % 2) ? " class=\"${cssActive} odd\"" : ''; 
       ?>>
        <td><?php echo $less['label'] ?></td>
        <td><?php
        if ($less['totalCards'] > 0) {
          # Show failed cards also:
          # array('value' => $less['testedCards'], 'label' => $less['passValue'].' passed card(s)' , 'class' => 'g'),
          # array('value' => $less['failValue'], 'label' => $less['failValue'].' failed card(s)', 'class' => 'r')
          # Show just progress (reduce info overload, focus on progres)
          echo ui_progress_bar(array(
            array('value' => $less['testedCards'], 'label' => $less['testedCards'].' flashcard(s)' , 'class' => 'g'),
          ), $less['maxValue']);
        }
        else {
          echo 'Not yet started';
        }?></td>
        <td class="center"><?php echo $less['testedCards'].' of '.$less['maxValue'] ?></td>
      </tr>
     <?php endforeach; ?>
     </tbody>
    </table>

  </div>

</div>
