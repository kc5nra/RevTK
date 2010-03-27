<div class="layout-home">
  <div class="col-side">
  <div id="homelinks">
    <div class="anchors">
      Contents :<br />
      <a href="#leitnersystem">Leitner's System</a>
      <a href="#gettingstarted">Getting Started</a>
      <a href="#reviewing">Reviewing</a>
      <a href="#benefits">Benefits</a>
      <a href="#faq">FAQ</a>
      <a href="#links">Links</a>
    </div>
  </div>
  </div>

  <div class="col-main">
    <div class="col-box col-box-top learn-more">

<h1>Introduction</h1><a name="intro"></a>
<div class="section">
  <p>
  "Reviewing the Kanji" is a reviewing aid that helps you keep track of, and schedule 
  reviews for over two thousand kanji. While the title is a perfectly descriptive one, it 
  is also a playful nod at the kanji study method called "Remembering the Kanji"  by James W. Heisig.
  </p>
  <p> <strong><em>Reviewing</em> the Kanji</strong> is a web site dedicated to help you
     complete the Remembering the Kanji method, and attain proficiency in recognising and writing
     2000+ Japanese characters no less!
  </p>
</div>

<h1>About "Remembering the Kanji"</h1>

<?php include_partial('home/GetTheBook', array('isHomepage' => false)) ?>

<div class="section">
<p> In addition to the many comments from reviewers at Amazon.com, you may enjoy Mary Noguchi's <a href="http://www.kanjiclinic.com/reviewheisigwiig.htm" target="_blank">thorough review</a> at the KanjiClinic.com website (highly recommended!).
  For details about the publisher, errata etc, see the <a href="http://www.nanzan-u.ac.jp/SHUBUNKEN/publications/miscPublications/Remembering_the_Kanji_1.htm" target="_blank">official page</a>.
</p>
</div>
<div class="clearboth">&nbsp;</div>


<a name="leitnersystem"></a>

<h1>About Leitner's Flashcard System</h1>
<div class="section">
  <p> <em>Reviewing the Kanji</em> uses a <strong>spaced repetition system</strong> (often called in
      short "SRS") based on the popular Leitner System:</p>
  
  <p class="quote">
In the Leitner system, flashcards are sorted into groups according to how well you know each one in the Leitner's learning box. This is how it works: you try to recall the solution written on a flashcard. If you succeed, you send the card to the next group. But if you fail, you send it back to the first group. Each succeeding group has a longer period of time before you are required to revisit the cards.
 -- Source: <a href="#">Wikipedia</a>
  </p>
    
  <p> The Leitner System helps you to:</p>
  <ul class="content">
    <li>Make sure that you review all the information that you have learned.</li>
    <li>Review at increasingly longer intervals to stimulate long term memory.</li>
    <li>Review more of the difficult flashcards, and less of those that you know well.</li>
  </ul>
  
</div>

<a name="gettingstarted"></a>
<h1>Getting Started</h1>

  <div class="section">
    <h2>Quick Summary</h2>
    
    <p>  Once you have logged in, you will see a quick summary of your progress : current lesson &amp; number of kanji
      remaining in this lesson, number of expired flashcards, number of "failed" flashcards. Each summary corresponds
      to an area of the website explained below.
    </p>
  
    <img class="inset" src="/images/1.0/doc/welcome-back.gif" width="508" height="195" alt="The home page after you have logged in" />

  </div>

  <div class="section">
    <h2>Adding Flashcards</h2>
    <p> Whenever you study with the book and have learned new kanji, simply enter 
      the number of new kanji that you have studied and click 'Ok' :
      <?php echo image_tag('/images/1.0/doc/addflashcards.gif', 'class="doc2" size="668x77" border="0"') ?>
    </p>
    <p> A confirmation dialog appears, showing you the actual frame number range that corresponds to the newly added cards :
      <?php echo image_tag('/images/1.0/doc/addconfirm.gif', 'class="doc2" size="668x109" border="0"') ?>
    </p>
    <p> It is assumed that you are studying the kanji in the same "frame number" ordering that is presented in the book.
      New flashcards are always added in sequential order.
    </p>
  </div>

  <h2>Checking your progress</h2>
  <div class="section">
  <p> This is how the Leitner cardboard box looks like in Reviewing the Kanji :
  <!--<img class="doc2" src="/images/1.0/doc/leitner.gif" width="666" height="369" border="0" alt="" />-->
  </p>
  <img class="inset" style="border:none;padding:1px;" src="/images/1.0/doc/leitner-boxes-flashcards.gif" width="508" height="279" alt="" />
  
  <p>  There are five compartments, displayed from left to right. Each compartment corresponds to a level of knowledge.</p>
  <p>  Coloured bars in each compartment represent stacks of cards.</p>
  <p> Clicking any of the coloured bars on the graph will take you to the reviewing screen.</p>
  
  <div class="stacks_legend">
    <span>Stacks in the first compartment :</span>
      <dl>
    <dt class="failed">&nbsp;</dt>
    <dd><b>Failed cards.</b> The red stack shows cards which have not been answered correctly.
      The kanji in this stack likely needs more work on the stories/mnemonics.
    </dd>
    <dt class="untested">&nbsp;</dt>
    <dd><b>Untested cards.</b>
      The blue stack shows cards that have not been tested yet.
      Below the graph there is a blue link, clicking the blue link is the same as clicking the blue stack.
      The blue link simply gives you more detail, it tells you which was the latest pack of cards that 
      were added, when they were added, and how many cards remain in that pack of cards.
      Each time you add new cards, they go to the top of the blue stack.
      When you click the blue stack you get to review the most recently added cards first.
    </dd>
    </dl>
    <span>Stacks in the other compartments :</span>
    <dl>
    <dt class="expired">&nbsp;</dt>
    <dd><b>Expired cards.</b>
      An orange stack indicates cards which have reached their scheduled
      reviewing date. These are the cards you will want to review most of the time. Keep in mind
      that expired cards in the second and third stacks are more critical to review than those 
      in the last compartments, since they have been added recently and have been reviewed only
      once or twice.
    </dd>
    <dt class="unexpired">&nbsp;</dt>
    <dd><b>Non expired cards.</b>
      Cards in the green stack are scheduled for review, but have not expired yet.
      In other words, they are still 'fresh' in your memory, so they don't need your attention yet.
      You can review unexpired cards if you click on the green stacks, but this is not recommended.
      If you review cards ahead of time, you are encouraging your memory to store the information in
      short term memory instead of long term memory, thus defeating the purpose of the review scheduling.
    </dd>
      </dl>
  </div>

  <p> A card that is answered correctly will be promoted to the next compartment. Since it also gets
    scheduled for review, it will also always move to the green stack.
  </p>
  <p> <b>When a card is not answered correctly it will move back to the first compartment!</b>
    This is why you can gauge your current level of knowledge just by looking at the count of
    cards in each compartment : cards in the last compartment have not only been tested four
    times or more, they also have passed the test at least four times <i>in a row</i>.
    Thus, the cards in the last compartments correspond to the kanji you know best.
  </p>
  </div>

  <h2>Scheduling</h2>
  <div class="section">
  <p>  When a card has been tested, it is scheduled for review in a number of days corresponding
    to which compartment it is moving to :
  </p>
  <table cellspacing="0" class="blocky">
    <tr class="head">
      <th>Cards moving to compartment...</th>
      <th>Are scheduled for review in...</th>
    </tr>
    <tr><td>1</td><td>0 days<br />(incorrectly answered cards)</td></tr>
    <tr><td>2</td><td>3 days</td></tr>
    <tr><td>3</td><td>7 days</td></tr>
    <tr><td>4</td><td>14 days</td></tr>
    <tr><td>5</td><td>30 days</td></tr>
    <tr><td>6</td><td>60 days</td></tr>
    <tr><td>7</td><td>120 days</td></tr>
    <tr><td>8*</td><td>240 days</td></tr>
  </table>
  
  <p> <b>*</b> : cards tested succesfully in the last box remain in the last box
    and are scheduled again at the maximum time interval.<br />
    <br />
    There is also an amount of <em>variance</em> added to the interval to help
    shuffle the flashcards over time. It is roughly one sixth of the interval
    so for example, a card going on a 30 day interval may be scheduled anywhere
    from 25 days to 35 days.<br />
    <br />
    Also note that the last box on the graph on the Review page shows the total
    of cards from the 5th, 6th and 7th boxes together.
  </p>
  
  </div>

<a name="reviewing"></a>
  <h2>Reviewing</h2>
  <div class="section">
  <p>  Clicking any of the stacks in the Leitner graph will take you to the reviewing screen :
  <img class="doc2" src="/images/1.0/doc/review_a.gif" width="668" height="449" border="0" alt="" />
  </p>
  <p> Depending on how many cards are in the stack the reviewing session could be very short or very long.
    Keep in mind that you can test as many or as few cards as you like, and you may leave the Review screen whenever you want!
  </p>
  <p>  Every time you answer a card, that card's status is updated. When you click the "Finish" button to skip to the Review
    Summary screen, the remaining cards that were not reviewed simply stay in the stack, and can be tested when you have more time.
  </p>
  <p> When you test one of the expired stacks (orange), you get cards in order of their expiry date, starting with the least recently expired ones, i.e. first come the cards that expired first.</p>
  <p> When you test the untested stack, it works the other way round. Cards that were the most recently added, get tested first. This lets you review immediately newly added cards, regardless of how many untested cards were already on the stack.</p>
  <p> Cards are always shuffled when they were added or expired on the same date. In other words, during review you get the cards in the order explained above, and within this order, groups of cards that fall on the same date get shuffled together.</p>
  <p>  Reviewing is done from the keyword to the kanji, and not the other way around. As recommended in James Heisig's method, you should write down the characters while reviewing. Since the book teaches you the stroke order of all the components of the japanese characters, being able to recall the kanji from the keyword means you are able to write every one of the kanji from memory. There is no planned support for testing kanji the other way round (there is however some sight-reading test/games planned).</p>
  <p>  Write down the character on a sheet of paper, or trace it in the palm of your hand, then press the <span class="keyboard">Spacebar</span> key
    or click "Show Kanji" to verify your answer :</p>
  <img class="doc2" src="/images/1.0/doc/review_b.gif" width="668" height="449" border="0" alt="" />
  <p>  The kanji is now displayed, along with the frame number and stroke count.</p>
  <p> The stroke count is useful to speed up reviews : count strokes as you write the kanji and then compare
    with the correct answer. This is easier and faster than comparing your writing with the character on the screen.</p>
  
  <p> If you were correct, answer "Yes" otherwise answer "No".
      Answering "Easy" will increase the interval by 50% compared to the "Yes" answer.
      You can answer by clicking the buttons or using the <span class="keyboard">Y</span>, <span class="keyboard">N</span> and <span class="keyboard">E</span> keys.
  </p>
  
  <p>  Correctly answered cards will be promoted to the next card box, incorrectly answered cards will return to the red stack in box one. It is highly suggested that you do not settle for half answers, if you forgot even just a small part of the writing of the kanji, answer "No". You are your own judge, but keep in mind that it is is not a race. Also realise that because many kanji look similar, forgetting "just one small stroke" here or there can make the difference between one kanji and another.</p>
  <p>  The "Stats" panel shows you how many kanji you have been testing in this session so far, how many were answered correctly, and how many were answered incorrectly.</p>
  <p>  At the end of the session, or when you click the "Skip to summary" button, you will be taken to the <b>Review Summary</b> screen :
  </p>
  
  <img class="inset" src="/images/1.0/doc/review-summary.gif" width="508" height="375" border="0" alt="Review Summary" />

  <p>  The Review Summary lists the kanji that were not answered correctly during the review session.</p>
  <p> The table can be sorted on any column by clicking on the column headers. In the example image above
    the review summary is sorted on the frame numbers.</p>
  <p>  Clicking any of the keywords will take you to the corresponding kanji in the Study area, where you can
    check your mnemonics, adapt them, or maybe use a mnemonic shared by another member if yours wasn't working
    so well.</p>
  </div>

  <h2 id="study">Study &amp; Share Stories</h2>
  <div class="section">
  <p>  The Study area is the most active area of the website, after the flashcard reviews : this is where
    you can enter your stories (as per Remembering the Kanji's method) and share them with other members :
  </p>
    
  <img class="inset" src="/images/1.0/doc/study-shared-stories.gif" width="508" height="450" border="0" alt="Study and share stories with other members" />

  <p> There are two ways to enter the Study area : click the "Study" link in the main navigation bar,
    which will show you an introductory text with some hints for editing your stories. The second way
    is when you click the red stack representing your "failed" flashcards, this gives you the opportunity
    to rework stories that didn't work well, see what new ideas have been shared by other members, and
    eventually click the "Learned" button to move the flashcard back into the review cycle.
  </p>

  <p>  If you choose to publicly share your story, it will appear in the list below. You can vote for stories that
    work well, copy a story from another member (you can use it as is, or you may want to edit it).
  </p>
  </div>

<a name="benefits"></a>
<h1>Benefits</h1>
<div class="section">
  <ul>
  <li>With the Leitner system, each cardbox represents a level of knowledge of the kanji. You can get a rapid estimate of your current progress simply by checking how many cards are in each box.</li>
  <li>You are able to set your own priorities simply by choosing the card box you want to work on. If you feel tired or you don't have enough time, review the higher compartments. If you are ready to tackle difficult kanji, work on the lower compartments.</li>
  <li>Too many reviews in a short period is a waste of time, as the information learned will remain in short term memory. Wait too long before reviewing, and you have lost the information. The scheduling system in "Reviewing the Kanji" uses increasingly longer spaced reviews, in order to promote long-term memory retention.</li>
  <li>You can optimize your reviewing time thanks to the scheduling system. There will be lots of reviews early on, but once your cards spread into the higher compartments, they will be scheduled for longer intervals, during which you can focus on the kanji that needs more attention.</li>
  </ul>
</div>

<a name="faq"></a>
<h1>FAQ</h1>
<div class="section">
  <p>
  Q. I can not see japanese characters in my browser.<br />
  A. You have to enable East Asian languages support in Windows in order to see the kanji on this website.
     See <a href="http://greggman.com/japan/xp-ime/xp-ime.htm" target="_blank">Installing Japanese Support</a> for
     a detailed how-to on installing East Asian language characters and the Input Method Editor (IME) which lets
     you type in Japanese.
  </p>
  <p>
  Q. What about paper flashcards ?<br />
  A. Paper flashcards allow you to review wherever you go, without the need of a computer.
  It is not necessary to create your own paper flashcards when studying with Remembering the Kanji,
   but it is still recommended. The time spent creating the cards helps during the study.
   Creating your own flashcards can be time consuming however. If you like, you can print some 
   flashcards (<a href="http://www.polarcloud.com/kanji" target="_blank">Printable flashcards</a>).
   You can also buy the "Kanji Study Cards" from Japan Publications (which also have the kanji compounds, pronunciations, etc. covered in RTK Volume II).
  </p>
  <p>
  Q. Do I really have to write the characters down when reviewing ?<br />
  A. No, but it's highly recommended to do it : </p>
  <p class="quote">
During later randomized review with flashcards, learners also write the characters. When review starts with a native-language definition and proceeds backward along the retrieval path to the character, writing the character gives a clear indication of how well it has been recalled. It also seems, however, that this motor element can make a contribution to character memorization and recognition, as many scholars have suggested.
  </p>
  <p>Source: <a href="http://www.fask.uni-mainz.de/inst/chinesisch/hanzirenzhi_papers_richardson.htm" target="_blank">article by Dr. Timothy W. Richardson</a></p>

</div>

<a name="links"></a>
<h1>External links</h1>
<div class="section">
  <ul>
  <li><a href="http://www.nanzan-u.ac.jp/SHUBUNKEN/publications/miscPublications/Remembering_the_Kanji_1.htm" target="_blank">Remembering the Kanji</a></li>
  <li><a href="http://www.flashcardexchange.com/leitner.php" target="_blank">Sebastian Leitner's flashcard system</a></li>
  <li><a href="http://www.fask.uni-mainz.de/inst/chinesisch/hanzirenzhi_papers_richardson.htm" target="_blank">Chinese Character Memorization and Literacy</a></li>
  <li><a href="http://www.supermemo.com/articles/theory.htm" target="_blank">Theoretical aspects of spaced repetition in learning</a></li>
  <li><a href="http://www.kanjiclinic.com/" target="_blank">KanjiClinic.com</a></li>
  </ul>
</div>

    </div>
  </div>
</div>

