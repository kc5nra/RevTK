/**
 * Kanji Labs (experimental features)
 * 
 * @author   Fabrice Denis
 * @package  RevTK
 */

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
      'onFlashcardUndo':   this.onFlashcardUndo.bind(this),
      'onAction':          this.onAction.bind(this)
    };

    this.oReview = new uiFlashcardReview(options.fcr_options);
    
    this.oReview.addShortcutKey('f', 'flip');
    this.oReview.addShortcutKey(' ', 'flip');
    this.oReview.addShortcutKey('n', 'no');
    this.oReview.addShortcutKey('y', 'yes');
    this.oReview.addShortcutKey('u', 'undo');

    // flashcad container
    this.elFlashcard = $$('div.uiFcCard')[0];

    // undo action
    this.elUndo = $$('.uiFcReview a.uiFcAction-undo')[0];
    // stats panel
    this.elStats = $$('.uiFcReview .uiFcStats')[0];
    this.elCount = this.elStats.select('.count'); // array
    this.elProgressBar = $('review-progress').getElementsByTagName('span')[0];
    // answer stats
    this.elAnswerPass = this.elStats.select('p.pass')[0];
    this.elAnswerFail = this.elStats.select('p.fail')[0];
    this.countYes = 0;
    this.countNo  = 0;
    
    // end review div
    this.elFinish = this.elStats.select('.finish')[0];
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
    
    this.updateStatsPanel();

    // set form data and redirect to summary with POST
    var elFrm = $('uiFcRedirectForm');
    elFrm.method = 'post';
    elFrm.elements['fc_pass'].value = this.countYes;
    elFrm.elements['fc_fail'].value = this.countNo;
    elFrm.submit();
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
    this.elUndo.style.display = this.oReview.getNumUndos() ? 'block' : 'none';

    this.updateStatsPanel();
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

  onFlashcardUndo:function(oAnswer)
  {
  //  uiConsole.log('onFlashcardUndo(%o)', oAnswer);
    
    // correct the Yes / No totals
    this.updateAnswerStats(oAnswer.r>1 ? -1 : 0, oAnswer.r===1 ? -1 : 0);
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
      case 'flip':
        if (this.oReview.getFlashcardState() == 0) 
        {
          this.oReview.setFlashcardState(1);
        }
        return;
        break;
        
      case 'undo':
        if (this.oReview.getNumUndos() > 0) 
        {
          this.oReview.backward();
        }
        return;
        break;
      
      case 'end':
        // this will notify onEndReview()
        this.oReview.endReview();
        return;
        break;
      
      case 'no':
        cardAnswer = 1;
        break;
      case 'yes':
        cardAnswer = 2;
        break;
    }

    // check if flashcard is flipped yet
    if (cardAnswer && this.oReview.getFlashcardState())
    {
      //var oCardData = this.oReview.getFlashcardData();
      /* no state
      var oAnswer = {
        id: oCardData.id,
        r:  cardAnswer
      };
      this.oReview.answerCard(oAnswer);
      */
      
      this.updateAnswerStats(cardAnswer>1 ? 1 : 0, cardAnswer===1 ? 1 : 0);
      
      this.oReview.forward();
      return;
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
    this.elCount[0].innerHTML = iPosition;
    this.elCount[1].innerHTML = iTotal;
    
    // update progress bar
    var pct = iPosition > 0 ? Math.ceil(iPosition * 100 / iTotal) : 0;
    pct = Math.min(pct, 100);
    this.elProgressBar.style.width = (pct > 0 ? pct : 0) + '%';
  },

  updateAnswerStats:function(iYes, iNo)
  {
    this.countYes += iYes;
    this.countNo  += iNo;
    this.elAnswerPass.innerHTML = this.countYes;
    this.elAnswerFail.innerHTML = this.countNo;
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
}
