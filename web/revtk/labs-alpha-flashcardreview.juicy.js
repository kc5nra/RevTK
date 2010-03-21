/**
 * Labs "alpha" (experimental features) -- Simple random test of vocabulary
 * 
 * Juicer build:
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/revtk/labs-alpha.juicy.js
 * 
 * Minification:
 *  java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar web/revtk/labs-alpha.juiced.js -o web/revtk/labs-alpha.min.js
 *   
 * @author  Fabrice Denis
 * @date    March 2010
 */

/* =require from "%WEB%" */
/* =require "/revtk/bundles/flashcardreview-1.0.juicy.js" */

var labsReview = 
{
  initialize:function(options)
  {
    // set options
    this.options = options;
    
    options.fcr_options.events =
    {
      'onBeginReview':     this.onBeginReview.bind(this),
      'onEndReview':       this.onEndReview.bind(this),
      'onFlashcardCreate': this.onFlashcardCreate.bind(this),
      'onFlashcardDestroy':this.onFlashcardDestroy.bind(this),
      'onFlashcardState':  this.onFlashcardState.bind(this),
      'onAction':          this.onAction.bind(this)
    };

    this.oReview = new uiFlashcardReview(options.fcr_options);
    
    this.oReview.addShortcutKey('f', 'flip');
    this.oReview.addShortcutKey(' ', 'flip');
    this.oReview.addShortcutKey('b', 'back');

    // back button
    this.elBack = $$('a.uiFcAction-back')[0];

    // flashcad container
    this.elFlashcard = $$('div.uiFcCard')[0];

    // stats panel
    this.elStats = $$('.uiFcReview .uiFcStats')[0];
    this.elCount = this.elStats.select('.count'); // array
    this.elProgressBar = $('review-progress').getElementsByTagName('span')[0];  
  },
  
  /**
   * Returns an option value
   * 
   * @param  String   Option name
   */
  getOption:function(name)
  {
    return this.options[name];
  },
  
  onBeginReview:function()
  {
    //uiConsole.log('labsReview.onBeginReview()');
  },

  /**
   * Update the visible stats to the latest server hit,
   * and setup form data for redirection to the Review Summary page.
   * 
   */
  onEndReview:function()
  {
    //uiConsole.log('labsReview.onEndReview()');
    window.location.href = this.options.back_url;
  },

  onFlashcardCreate:function()
  {
    uiConsole.log('labsReview.onFlashcardCreate()');

    // Show panels when first card is loaded
    if (this.oReview.getPosition()==0)
    {
      this.elStats.style.display = 'block';
    }

    // Show undo action if available
    this.elBack.style.display = this.oReview.getPosition() > 0 ? 'block' : 'none';

    this.updateStatsPanel();

    // set the google search url
    var searchTerm = this.oReview.getFlashcardData().compound;
    var searchUrl = 'http://www.google.co.jp/search?hl=ja&q=' + encodeURIComponent(searchTerm);
    $('search-google-jp').href = searchUrl;
  },

  /**
   * Hide buttons until next card shows up.
   * 
   */
  onFlashcardDestroy:function()
  {
  //  this.setButtonState($('uiFcButtons0'), false);
    $('uiFcButtons0').hide();
    $('uiFcButtons1').hide();
  },

  onFlashcardState:function(iState)
  {
  //  uiConsole.log('labsReview.onFlashcardState(%d)', iState);
    
    if (iState===0)
    {
    //  this.setButtonState($('uiFcButtons0'), true);
      $('uiFcButtons0').show();
      $('uiFcButtons1').hide();
    }
    else {
      $('uiFcButtons0').hide();
      $('uiFcButtons1').show();
    }
  },

  onAction:function(sActionId)
  {
    var cardAnswer = false;

    uiConsole.log('labsReview.onAction(%o)', arguments);

    // flashcard is loading or something..
    if (!this.oReview.getFlashcard()) {
      return;
    }

    switch (sActionId)
    {
      case 'back':
        if (this.oReview.getPosition() > 0)
        {
          this.oReview.backward();
        }
        break;
      
      case 'flip':
        if (this.oReview.getFlashcardState() == 0)
        {
          this.oReview.setFlashcardState(1);
        }
        else
        {
          this.oReview.forward();
        }
        break;

      case 'search-google-jp':
        break;
    }

    return;
  },

  updateStatsPanel:function()
  {
  //  uiConsole.log('labsReview.updateStatsPanel()');
    var items = this.oReview.getItems(),
    num_items = items.length,
    position  = this.oReview.getPosition();

    this.updateProgress(position, num_items);
  },

  updateProgress:function(iPosition, iTotal)
  {
    // update review count
    this.elCount[0].innerHTML = iPosition + 1;
    this.elCount[1].innerHTML = iTotal;
    
    // update progress bar
    var pct = iPosition > 0 ? Math.ceil(iPosition * 100 / iTotal) : 0;
    pct = Math.min(pct, 100);
    this.elProgressBar.style.width = (pct > 0 ? pct : 0) + '%';
  },

  /**
   * Sets buttons (children of element) to default state, or disabled state
   * 
   */
  setButtonState:function(elParent, bEnabled)
  {
    var elButtons = $(elParent).select('a.uiIBtn');
    elButtons.each(function(el){
      if (bEnabled) {
        el.removeClassName('uiFcBtnDisabled');
      }
      else {
        el.addClassName('uiFcBtnDisabled');
      }
    });
  }
};

