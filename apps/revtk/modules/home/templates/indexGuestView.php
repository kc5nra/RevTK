<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
    <div class="col-box col-box-top">

<!-- begin Homepage v1.5 -->
<div id="homev15">
  <h2>You can learn the <em>meaning</em> and <em>writing</em><br/>of <em>two thousand</em> Japanese characters.</h2>

  <div class="step step-one"><span>Remember!</span></div>

<?php include_partial('GetTheBook', array('isHomepage' => true)) ?>

  <div class="step step-two"><span>Review, Share and Improve!</span></div>

  <div id="homescreens">
    <table cellspacing="0">
      <tr>
        <td>
          <div class="screen s1"></div>
          <p>
            <span>See your progress</span>
            Visualize your progress as stacks of flashcards. Reviews are automatically scheduled for you based on your results.
          </p>
        </td>
        <td>
          <div class="screen s2"></div>
          <p>
            <span>Review the kanji</span>
            Review the kanji online. Repeat more of the difficult characters, and less of those that you know well.
          </p>
        </td>
        <td>
          <div class="screen s3"></div>
          <p>
            <span>Share MNEMONICS</span>
            Feeling stuck? Share stories with other RtK learners. Find help and encouragement on the community forums!
          </p>
        </td>
      </tr>
    </table>
  </div>


  <div id="homeaction">
    <?php echo link_to('<img src="/images/2.0/home/button-register.gif" width="199" height="55" alt="Register (it&quot;s free!)" />', 'account/create') ?>
    <?php echo link_to('Learn More', '@learnmore') ?>
  </div>

</div>
<!-- end Homepage v1.5 -->


  </div>


  <div class="col-box col-box-bis">
    <h2>Site News</h2>
    <?php include_partial('news/list', array('newsPosts' => SitenewsPeer::getMostRecentPosts())) ?>
    ...more in the <?php echo link_to('news archive','news/index') ?>.
  </div>

  </div>

</div>
