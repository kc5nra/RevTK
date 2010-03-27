<div id="sitenews" class="content">
  <dl>
  <?php foreach($newsPosts as $post): ?>  
    <dt><span class="newshead"><?php echo $post->subject ?></span><span class="newsdate"><?php echo $post->date ?></span></dt>
    <dd><?php echo $post->text ?></dd>
  <?php endforeach ?>
  </dl>
</div>
