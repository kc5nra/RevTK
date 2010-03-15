
  <div id="homebook"<?php echo $isHomepage ? '' : ' style="margin-bottom:1em"' ?>>
    <div class="cover">
      <?php 
        $img = '<img src="/images/2.0/home/remembering_the_kanji.jpg" width="126" height="190" alt="Remembering the Kanji book cover" />';
        if (coreConfig::get('koohii_build')) {
          use_helper('__Affiliate'); echo link_to_amazon($img, 'amazon.us.rtk1', array('title' => 'See book at Amazon.com'));
        } else {
          echo $img;
        }
      ?>
    </div>
    <div class="intro">

<?php if($isHomepage): ?>

      <p> Use your imaginative memory to remember over two thousand complex Japanese characters, with James Heisig’s <strong>Remembering the Kanji</strong>.</p>

      <?php use_helper('LocalAssets'); echo get_local_content(__FILE__, 'buyBookOnAmazon'); ?>
      
      <p>Wait! I need a book for this? Yes you do! But you can start now with the <a href="http://www.nanzan-u.ac.jp/SHUBUNKEN/publications/miscPublications/pdf/RK4/RK%201_sample.pdf" class="pdf">sample chapter</a> which covers 276 kanji!</p>
      <p>Questions? Discuss RtK on our <a href="<?php echo coreConfig::get('app_forum_url') ?>/viewforum.php?id=1">community forums</a>!</p>

<?php else: ?>

      <p> This website is designed as a <em>complement</em> to the kanji learning method 
          called <strong>Remembering the Kanji</strong>, from Mr. James W. Heisig.
      </p>
      <p>
        You should learn the kanji by following the "building blocks" approach of the book,
        and then you can use this website to test yourself, improve your memory of the characters, and share stories
        with other members!
      </p>
      
      <?php use_helper('LocalAssets'); echo get_local_content(__FILE__, 'buyBookOnAmazon'); ?>
  
      <p> Can't wait to get started? You can start right now with the <a href="http://www.nanzan-u.ac.jp/SHUBUNKEN/publications/miscPublications/pdf/RK4/RK%201_sample.pdf" class="pdf">sample chapter</a> which covers 276 kanji!</p>

<?php endif ?>

    </div>
    <div class="clear"></div>
  </div>
