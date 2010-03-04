<?php use_helper('Form', 'Validation') ?>

<?php slot('inline_styles') ?>
.col-main form { margin:0 0 2em; }
.col-main textarea { width:500px; margin:0 0 0.5em; }
.col-main form .buttons { text-align:right; }
.col-main p.j { width:100%; font:20pt "MS Gothic", sans-serif; border-top:1px solid #dad8bd; padding-top:0.5em; }
.col-main a.j { font-style:normal; font-weight:normal; color:blue; text-decoration:none; }
.col-main a.j:hover { background:#F0EED9; color:#000; }
/* fading tooltips */
div#toolTip { position:absolute;z-index:1000;background:#fff;border:1px solid #a0a0a0;padding:4px 8px;min-height:1em;}
div#toolTip p { margin:0;padding:0;color:#444;font:14px Georgia, Times New Roman, serif; }
<?php end_slot() ?>

<div class="layout-home">

<?php include_partial('home/homeSide') ?>

  <div class="col-main">
  	<div class="col-box col-box-top">

		<div class="app-header">
			<h2>Kanji sight-reading practice</h2>
			<div class="clearboth"></div>
		</div>
		
		<div id="form" style="display:<?php echo $display_form ? 'block':'none' ?>">

			<p>	Copy and paste japanese text into the form below, then click "Show".
				All the kanji for which you have added flashcards will appear in a <span class="j">different</span> color.</p>

			<?php echo form_errors() ?>

			<?php echo form_tag('misc/reading') ?>
				<?php echo textarea_tag('jtextarea', '', 'rows=6 cols=78') ?>
				<div class="buttons">
					<?php echo submit_tag('Show') ?>
					<?php echo tag('input', array('type'=>'button', 'value'=>'Clear', 'onclick'=>'clearit()')) ?>
				</div>
			</form>

			<div id="introduction" class="content">
				<h3>Purpose of this page</h3>
		
				<p> In "Remembering the Kanji", the Japanese characters are studied and reviewed
				    <i>from the keyword to the kanji</i>.
				    In this sight-reading section, you can test your memory the other way round, all the while
				    seeing the characters <i>in context</i>.
				</p>
	
				<p> With very basic grammar you can locate compound words made of two or more kanji. You may be
					able to guess the meaning of some words based on the meaning of the characters.
				</p>
				
				<h3>Resources</h3>
				
				<ul>
					<li>Japanese text: <a href="http://www.yomiuri.co.jp" target="_blank">Yomiuri Online</a>,
						<a href="http://www.geocities.co.jp/HeartLand-Gaien/7211/" target="_blank">Old Stories of Japan</a>,
						<a href="http://www.aozora.gr.jp/" target="_blank">Aozora Bunko</a>.
					</li>
					<li><a href="http://www.kanji.org/kanji/japanese/writing/outline.htm" target="_blank">Guide to the Japanese Writing System</a> by Jack Halpern</li>
					<li>Lookup Japanese words in <a href="http://www.mozilla.org/" target="_blank">Firefox</a> with <a href="http://moji.mozdev.org/" target="_blank">moji extension</a>.</li>
				</ul>
			</div>
		</div>
		
		<div id="results" style="display:<?php echo $display_kanji ? 'block' : 'none' ?>">
			<p> <?php echo link_to('^ Enter more japanese text.','@sightreading', array('id'=>'toggle-form')) ?></p>
			<p>	If you can not remember a keyword, move the mouse pointer over the colored kanji. A 'tooltip'
			will appear, showing the english keyword. Have fun!</p>
			<p class="j" lang="ja" xml:lang="ja"><?php echo $kanji_text ?></p>
		</div>

	</div>
  </div>

</div>
