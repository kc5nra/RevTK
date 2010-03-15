/*! uiFlashcardReview (c) Fabrice Denis (http://kanji.koohii.com) */

/**
 * uiFlashcardReview is a reusable flashcard review component. It handles communication
 * with the server (ajax), caching and prefetching of flashcard data, and the state of
 * the review session (forward, backward (undo), end). Also handles shortcut keys.
 * 
 * Presentation logic is handled by the "child" class, through events that are notified
 * by uiFlashcardReview's event dispatcher.
 * 
 * 
 * Public methods:
 * 
 *   initialize(oOptions)   Constructor, pass an options object:
 *
 *       items            An array of flashcard ids (REQUIRED)
 *       ajax_url         Url of ajax action to get/post flashcard data (REQUIRED)
 *       max_undo          Maximum undo/backward level
 *       num_prefetch      How many flashcards to fetch ahead
 *       events           An object with events to register (name => function)
 *       put_request      Set to false if not using flashcard answers (defaults true), postCache
 *                        will always be empty
 *
 *   connect(sName, fnEvent)   Add a listener
 *   disconnect(sName)         Remove a listener
 *   notify(sName)             Notify listeners
 *   
 *   addShortcutKey(sKey, sActionId)
 *                        Add a keyboard shortcut for an action, the action id is passed to 'onAction' notification
 *   
 * Methods to control the review session:
 *   
 *   beginReview()        Called automatically when uiFlashcardReview is instanced.
 *   endReview()          Ends review: flushes postCache to server, then notifies "onEndReview".
 *   forward()            Advance to next flashcard
 *   backward()           Go back one card (undo), notifies "onFlashcardUndo" event, with the last flashcard answer.
 *   
 * Methods to get/put information:
 *   
 *   getOption()          Returns one of the options as passed to initialize(), TODO: DEPRECATED (bad)
 *   getPosition()        Returns current index into the flashcard items array
 *   getFlashcard()       Returns current uiFlashcard, or null
 *   getFlashcardData()    Returns json data for current flashcard
 *   getItems()           Returns array of flashcard ids, length of array = number of flashcards in session
 *   getPostCount()       Returns count of flashcard answers that need to be posted to server
 *   getNumUndos()        Returns number of undo levels currently available
 *   getFlashcardState()  Returns the current state (0 = default), or false if no current card is set
 *   setFlashcardState(n) Sets state for current flashcard, this changes the "uiFcState" class name on the flashcard
 *   answerCard(oData)    Set data to post back to server as answer for current card, either call it
 *                        for ALL cards, or never call it (option "put_request" is false)
 * 
 * Notifications:
 * 
 *   onBeginReview        When review begins, during uiFlashcardReview initialization
 *   onEndReview          When the last flashcard is answered (and the post cache flushed!), notified by endReview()
 *   onFlashcardCreate    A new flashcard is created, before json content is inserted into the card and it is shown.
 *                        Flashcard data is available and can be changed with getFlashcardData()
 *   onFlashcardState(n)  Called just after 'onFlashcardCreate' for default state 0, and then everytime the state
 *                         is set with setFlashcardState(). Argument n is the state number.
 *   onFlashcardDestroy   Before the current flashcard is destroyed
 *   onFlashcardUndo(o)   Called when user undo'es flashcard answer. Argument o is the answer data as it was passed
 *                        to answerCard(). Notified only if "put_request" is true (default).
 *   onAction             Called for click on elements with "uiFcAction-XXX" class, with "XXX" as second argument
 * 
 * Ajax RESPONSE format (in Json):
 * 
 *   get                  Array of flashcard data, one object for each id that came in request.
 *                        Properties that match elements of class "fcData-xxx" where xxx is the property name,
 *                        are automatically loaded into the element (innerHTML, html allowed), other properties
 *                        may be used by the peer class.
 *                        Returned objects must have the property "id" set to the corresponding flashcard id's.
 *   put                  Integer, should return the same number as flashcard answers that were posted to the server.
 *                        The same number of items are cleared from the postCache. If not cleared, these items will
 *                        be posted again during the next prefetchs.
 * 
 * Usage:
 * 
 *   #uiFcAjaxLoading
 *     => div with "Loading..." message shown during Ajax request.
 *   #uiFcAjaxError  
 *     => div that shows for ajax errors, or server errors, span.msg is set to error message.
 *   #uiFcReview  
 *     The main container for the flashcard review area.
 *   DIV.uiFcCard
 *     => the flashcard container
 *   .uiFcState-N
 *     => the flashcard state, where N is a number, 0 is the default state, use with css rules to set
 *        visibility of various information on the card depending on state (eg. 0 = question, 1 = answer)
 *   .fcData
 *   .fcData-xxx
 *     => Element's inner html is set to xxx, where xxx is a property of the flashcard data in the Ajax responses.
 *     
 *   a.uiFcAction-XXXX
 *     Links that trigger an action (answer buttons), calls event "onAction" with "XXXX" as second argument
 * 
 * 
 * @author   Fabrice Denis
 */



/**
 * 
 * 
 */
var uiFlashcardReview = Class.create();
uiFlashcardReview.prototype =
{
  // flashcard selection as an array of flashcard ids
  items: null,

  // review position, from 0 to items.length-1
  position: null,
  
  // cache of flashcard data, goes back at least max_undo positions
  // an array of objects, that maps review positions
  // flashcard data that is no longer needed is deleted,
  // cacheStart and cacheEnd indicate the range of valid flashcard data
  // in the cache array.
  cache: null,
  cacheStart: null,
  cacheEnd: null,

  // the next position at which to prefetch new flashcard data
  // is recalculated on server reply, based on number of items server returned
  prefetchPos: null,

  // max items to cache for undo
  max_undo: null,
  // current undo level (number of steps backward)
  undoLevel: null,
  
  // how many items to preload
  num_prefetch: null,
  
  // event dispatcher for notifications
  eventDispatcher: null,
  
  // array of answer data for flashcards that is not posted yet
  // the data is freeform, the property _id corresponds to a flashcard id
  postCache: null,
  
  // Active uiAjaxRequest instance or null
  ajaxRequest: null,
  

  /**
   * Initialize the front end Flashcard Review component.
   * 
   * @param {Object} options
   */  
  initialize:function(oOptions)
  {
    uiConsole.log('uiFlashcardReview::initialize()');

    // set options and fix defaults
    this.options = oOptions;
    this.options.max_undo = oOptions.max_undo || 3;
    this.options.num_prefetch = oOptions.num_prefetch || 10;
    this.options.put_request = oOptions.put_request===false ? false : true;

    // set options and make proxies
    this.items        = this.options.items;
    this.max_undo     = this.options.max_undo;
    this.num_prefetch = this.options.num_prefetch;
    
    // 
    if (!this.items || !this.items.length) {
      alert("No flashcard items in this selection.");
      return;
    }

    // register listeners
    this.eventDispatcher = new uiEventDispatcher();
    for (var sEvent in oOptions.events) {
      this.eventDispatcher.connect(sEvent, oOptions.events[sEvent]);
    }

    this.configureAjaxIndicator();

    this.configureDialogs();
    
    // event dispatcher for buttons and other custom actions
    this.initializeActionEvents();

    // initialize shortcuts and keyboard handler
    this.oKeyboard = new uiKeyboard();

    // 
    this.postCache = [];
    this.undoLevel = 0;
    this.ajaxRequest = null;
    
    this.curCard  = null;
//    this.ofs_prefetch = Math.floor(this.num_prefetch);
    
    this.beginReview();
  },

  /**
   * Configure error messages.
   * 
   */
  configureDialogs:function()
  {
    this.elAjaxError = $('uiFcAjaxError');
    var elAction = this.elAjaxError.select('a.uiFcAction-reconnect')[0];
    elAction.observe('click', this.dialogReconnectEvent.bindAsEventListener(this));
  },

  /**
   * Add or remove onbeforeunload event to warn user of loosing
   * flashcard answers.
   * 
   */
  updateUnloadEvent:function()
  {
    var $this = this;
    
    if (this.getPostCount()) {
      window.onbeforeunload = function()
      {
        return "WAIT! You may lose a few flashcard answers if you leave the page now.\r\n" +
               "Select CANCEL to stay on this page, and then click the END button to\r\n" +
               "complete this review session.";
      };
    }
    else {
      window.onbeforeunload = null;
    }
    
  },

  /**
   * Attach events to dispatch listener for action buttons.
   * 
   * @note   This sucks... needs to rewrite with YUI and uiEventDispatcher
   */
  initializeActionEvents:function()
  {
    var elFcReview = $$('.uiFcReview')[0];
    var aLinks = elFcReview.select('a');
    var i;
    for (i = 0; i < aLinks.length; i++)
    {
      var el = aLinks[i];
      if (/uiFcAction-(\w+)/.test(el.className))
      {
        el.observe('click', this.onActionEvent.bindAsEventListener(this, RegExp.$1));
      }
    }
  },
  
  /**
   * The event listener bound to html elements that use "uiFcAction-XXX" class names. 
   * 
   * Makes sure to stop the mouse click event, to prevent page from jumping.
   * 
   * @param {Object} oEvent    Prototype extended event object
   * @param {Object} sAction   The XXX in "uiFcAction-XXX" class name
   */
  onActionEvent: function(oEvent, sAction)
  {
    this.notify('onAction', sAction);
    oEvent.stop();
    return false;
  },
  

  /**
   * uiEventDispatcher proxies
   * 
   */
  connect: function(sName, fnEvent)
  {
    this.eventDispatcher.connect(sName, fnEvent);
  },

  disconnect: function(sName, fnEvent)
  {
    this.eventDispatcher.disconnect(sName, fnEvent);
  },

  notify:function()
  {
    var args = $A(arguments), sName = args.shift();
    this.eventDispatcher.notify(sName, args);
  },


  /**
   * Initialize Ajax loading indicator for all Prototype Ajax Requests
   *
   */
  configureAjaxIndicator:function()
  {
    var that = this;

    this.ajaxIndicator = $('uiFcAjaxLoading');

    Ajax.Responders.register(
    {
      onCreate:function()
      {
        that.ajaxIndicator.setStyle({
          position: 'absolute',
          zIndex:   1000,
          display:  'block'
        });
      },
      onComplete:function()
      {
        that.ajaxIndicator.setStyle({
          display:  'none'
        });
      }
    });
  },

  beginReview:function()
  {
    this.notify('onBeginReview');
    
    this.position = -1;
    this.cache = [];
    this.cacheStart = 0;
    this.cacheEnd = -1;
    this.prefetchPos = 0;    // when to prefetch new cards, updated by each ajax response
    
    this.forward();
  },

  /**
   * If no flashcards were reviewed, "end" the review by redirecting to the previous page (back_url)
   * 
   * Otherwise flush the post cache, and then notify "onEndReview".
   * 
   */
  endReview:function()
  {
    var that = this;

    if (this.position<=0)
    {
      // redirect to back_url
      if (this.options.back_url) {
        window.location.href = this.options.back_url;
        return;
      }
      else {
        return;
      }
    }


    function endReviewAjaxReady()
    {
      if (that.ajaxRequest) {
      //console.log('endReviewAjaxReady');
        // wait for last ajax request to end (eg. prefetching cards)
        that.connect('onAjaxResponseSuccess', endReviewAjaxReady);
        return;
      }

      that.disconnect('onAjaxResponseSuccess');

      // flush any items remaining in postCache
      that.sendReceive(true);
      
      endReviewPostReady();
    }

    function endReviewPostReady()
    {
    //console.log('endReviewPostReady');
      // wait if there was something in the postCache to "flush"
      if (that.ajaxRequest) {
        that.connect('onAjaxResponseSuccess', endReviewPostReady);
        return;
      }
      
      that.disconnect('onAjaxResponseSuccess');

      that.notify('onEndReview');
    }
  
    // clear last card from display
    this.destroyCurCard();

    // @todo: show "Ending review..."

    endReviewAjaxReady();
  },

  forward:function()
  {
    this.position++;

    if (this.undoLevel > 0) {
      this.undoLevel--;
    }

    // destroy previous card, so it doesn't show while loading next card (if not prefetched)
    this.destroyCurCard();

    // all cards done?
    if (this.position >= this.items.length)
    {
      this.endReview();
      return;
    }

    // wait for card data
    this.connect('onWaitCache', this.cardReady.bind(this));

    this.sendReceive();

    // clear backwards cache
    this.cleanCache();

    // if card is already prefetched, handle it!
    if (this.cacheEnd >= this.position)
    {
      this.cardReady();
    }
  },
  
  /**
   * Undo (go backwards)
   * 
   * To allow undo we always keep a number of answers in the postCache (max_undo).
   * When sendReceive() does a prefetch, only the answers that are before max_undo items
   * backwards are posted. Only at the end of the review are the last answers in the
   * "ungo range" flushed out to the server.
   * 
   */
  backward:function()
  {
    // assertion
    if (this.undoLevel >= this.max_undo) {
      throw new Error("uiFlashcardReview::backward() undoLevel >= max_undo");
    }

    if (this.position <= 0) {
      return;
    }

    // assertion
    if (this.cacheStart >= this.position) {
      throw new Error("uiFlashcardReview::backward() on empty cache");
    }

    this.destroyCurCard();
    this.undoLevel++;

    // go back one step and clear postCache at that position
    this.position--;
    
    // notify and pass the last flashcard answer before it is cleared from postCache
    if (this.options.put_request) {
      this.notify('onFlashcardUndo', this.unanswerCard());
    }
    
    this.cardReady();
  },

  /**
   * This function is called only when the current flashcard
   * data is available in the cache.
   */
  cardReady:function()
  {
    // clear event
    this.disconnect('onWaitCache');

    // we have a cached item for current position
    var oItem = this.getFlashcardData();

    this.curCard = new uiFlashcard(this);
    this.notify('onFlashcardCreate');
    this.curCard.setContent(oItem);
    this.setFlashcardState(0);
    this.curCard.display(true);
  },

  /**
   * Clears current flashcard, so that it disappears
   * until the next one is ready.
   */
  destroyCurCard:function()
  {
    if (this.curCard) {
      this.notify('onFlashcardDestroy');
      this.curCard.destroy();
      this.curCard = null;
    }
  },

  /**
   * Check if there are cards to prefetch, and/or answers to post.
   * 
   * @param boolean  bFlushData  When review ends before last card is answered (endReview()),
   *                             force flush all remaining items in postCache.
   */
  sendReceive:function(bFlushData)
  {
    var oJsonData = {};
    var iCacheFrom = false;

    // avoid sending more than one request at once (could happen if undo + forward quickly)
    if (this.ajaxRequest) {
      return;
    }

    // any cards to fetch ?
    if ((this.cacheEnd < this.items.length - 1) &&
      (this.position >= this.prefetchPos))
    {
      var from = this.cacheEnd + 1;
      var to   = Math.min(from + this.num_prefetch, this.items.length) - 1;
      oJsonData.get = this.items.slice(from, to+1);
      iCacheFrom = from;
    }

    // any answers to post?
    if (this.options.put_request && (oJsonData.get || bFlushData))
    {
      // if flush, post all, otherwise don't post all, leave some cards behind to allow client ot re-answer (undo)
      var aPostData; 

      if (bFlushData)
      {
        aPostData = this.postCache;
      }
      else
      {
        var i, numToPost = this.getPostCount() > this.max_undo ? this.getPostCount() - this.max_undo : 0;
        aPostData = [];
        for (i = 0; i < numToPost; i++) {
          aPostData.push(this.postCache[i]);
        }
      }
      
      if (aPostData.length > 0) 
      {
      //  uiConsole.log('POSTING %d (%o)', numToPost, aPostData);
        oJsonData.put = aPostData;
      }
    }

    uiConsole.log('uiFlashcardReview::sendReceive(%o)...', oJsonData);

    if (oJsonData.get || oJsonData.put)
    {
      this.ajaxRequest = new uiAjaxRequest(this.options.ajax_url,
      {
        method:     'post',
        parameters: { json: Object.toJSON(oJsonData) },
        onSuccess:  this.fetchCardsResponse.bind(this, iCacheFrom),
        onFailure:  this.handleFailure.bind(this),
        onTimeout:  this.handleFailure.bind(this)
      });
      
      return true;
    }
    
    return false;
  },

  /**
   * Cache items returned by the server,
   * determine next position to start prefetch based on how many items were received.
   * 
   * @param {Object} iFrom
   * @param {Object} oAjaxResponse
   */
  fetchCardsResponse:function(iCacheFrom, oAjaxResponse)
  {
    var i;

  //  uiConsole.log('uiFlashcardReview::fetchCardsResponse(%o)', arguments);

    var oJson = this.handleJsonResponse(oAjaxResponse);
    if (oJson)
    {
      // cache cards if any
      if (oJson.get && oJson.get.length > 0)
      {
        // add cards to cache
        this.cacheEnd = iCacheFrom + oJson.get.length - 1;
        
        // next prefetch at based on number of items received
        this.prefetchPos = iCacheFrom + Math.floor(oJson.get.length/2) + 1;
    
        // cache items
        for (i = 0; i < oJson.get.length; i++)
        {
          this.cacheItem(oJson.get[i]);
        }
        
        this.notify('onWaitCache');
      }
      
      // clear answers from cache, that were handled succesfully by the server
      if (oJson.put)
      {
        // the number of answers that the server received can now be cleared from the postCache
        if (oJson.put > 0)
        {
        //  uiConsole.log("RESPONSE PUT, CLEAR %d FROM %d", oJson.put, this.postCount);
  
          for (i = 0; i < oJson.put; i++)
          {
            this.postCache.shift();
          }
          
          this.updateUnloadEvent();          
        }
      }
    }

    // indicate that ajax request is free, and notify if someone is waiting
    this.ajaxRequest = null;
    
    // notifies ajax request finished and handled succesfully
    this.notify('onAjaxResponseSuccess');
  },

  cacheItem:function(oItem)
  {
    this.cache[oItem.id] = oItem;
  },

  /**
   * Clear flashcard display data for items behind, to free some
   * resources, we only need as much flashcard data behind as needed
   * for undo.
   * 
   */
  cleanCache:function()
  {
    while (this.cacheStart < this.position - this.max_undo)
    {
      var id = this.items[this.cacheStart];
      delete this.cache[id];
      this.cacheStart++;
    }
  },

  /**
   * Getters
   */
  getOption:function(sName)
  {
    return this.options[sName];
  },

  getPosition:function()
  {
    return this.position;
  },

  getFlashcard:function()
  {
    return this.curCard;
  },

  getFlashcardData:function()
  {
    var id = this.items[this.position];
    return id ? this.cache[id] : null;
  },

  getPostCount:function()
  {
    return this.postCache.length;
  },
  
  getNumUndos:function()
  {
    return Math.min(this.position, this.max_undo - this.undoLevel);
  },
  
  getItems:function()
  {
    return this.items;
  },

  setFlashcardState:function(iState)
  {
    if (this.curCard) {
      this.curCard.setState(iState);
    }
  },
  
  getFlashcardState:function()
  {
    return this.curCard ? this.curCard.getState() : false;
  },

  /**
   * Parse the response as a JSON object, and returns a Javascript Object.
   * 
   * If the response is not JSON, assume the server errored, and log out
   * the response text somewhere for debugging purposes.
   * 
   * @param   oAjaxResponse    Prototype Ajax.Response
   * @return  Object           Json data as native Object, or false on failure
   */
  handleJsonResponse:function(oAjaxResponse)
  {
    var oJson;
    
    uiConsole.log('uiFlashcardReview::handleJsonResponse(%o)', oAjaxResponse);
    
    if (oAjaxResponse.status!==200)
    {
      return false;
    }

    try {
      oJson = oAjaxResponse.responseText.evalJSON(true);
    }
    catch(msg)
    {
         // Badly formed JSON string: '...'
      alert('Oops! Invalid JSON response.');
//      this.debugAjaxResponse(oAjaxResponse);
      return false;
    }

    return oJson;
  },

  debugAjaxResponse:function(oAjaxResponse)
  {
    var elDbgDiv = $('uiFcDebugResponse');
    if (!elDbgDiv)
    {
      elDbgDiv = new Element('div', { id: 'uiFcDebugResponse' });
      elDbgDiv.setStyle({
        background:'#C00',
        color:     '#fff',
        padding:   '10px',
        font:      '11px/1.1em "Courier New"'
      });
      $(document.body).insert({top: elDbgDiv});
    }
    elDbgDiv.update('<pre>' + oAjaxResponse.responseText + '</pre>');
  },

  /**
   * 
   * @see    http://www.prototypejs.org/api/ajax/response
   * 
   * @param   oAjaxResponse    Prototype Ajax.Response
   */
  handleFailure:function(oAjaxResponse)
  {
    uiConsole.log('uiFlashcardReview::handleFailure(%o)', oAjaxResponse);

    this.ajaxRequest = null;

    if (oAjaxResponse.request.timeout)
    {
      // show the timeout message
      this.ajaxErrorDialog('Oops! Connection timed out.');
      return;
    }

    var sErrorMessage = oAjaxResponse.getHeader('RTK-Error');

    if (sErrorMessage!==null)
    {
      //alert('Oops! The server returned an error: "'+sErr500Message+'"');

      // show neat message from custom server-side ajax exception
      this.ajaxErrorDialog(sErrorMessage);
    }
    else
    {
      sErrorMessage = oAjaxResponse.status + ' ' + oAjaxResponse.statusText;
      alert('Oops! The server returned a "'+sErrorMessage+'" error.');
    }

    /* obsolete?
    if (oAjaxResponse.responseText!=='')
    {
      this.debugAjaxResponse(oAjaxResponse);
    }*/
  },

  /**
   * Timeout dialog.
   * 
   * Retry to connect to server.
   */
  dialogReconnectEvent:function(e)
  {
    this.ajaxErrorDialog(false);
    this.sendReceive();
    Event.stop(e);
  },

  /**
   * Show or hide the Ajax error message.
   * 
   * @param {mixed} sErrorMessage    String or false to hide the message.
   */
  ajaxErrorDialog:function(sErrorMessage)
  {
    if (sErrorMessage===false) {
      this.elAjaxError.hide();
    }
    else {
      var el = this.elAjaxError.select('.msg')[0];
      el.innerHTML = sErrorMessage;
      this.elAjaxError.show();
    }
  },

  /**
   * Register a shortcut key for an action id. Pressing the given key
   * will notify 'onAction' with the given action id. Lowercase letters will match
   * the uppercase letter.
   * 
   * @param {String} sKey  Shortcut key, should be lowercase, or ' ' for spacebar
   * @param {String} sActionId  Id passed to the 'onAction' event when key is pressed
   */
  addShortcutKey:function(sKey, sActionId)
  {
    if (!this.eventDispatcher.hasListeners('onAction'))
    {
      alert('uiFlashcardReview::addShortcutKey() Adding shortcut key without "onAction" listener');
    }

    this.oKeyboard.addListener(sKey, this.shortcutKeyListener.bindAsEventListener(this, sActionId));
  },

  shortcutKeyListener:function(oEvent, sActionId)
  {
  //  uiConsole.log('uiFlashcardReview::shortcutKeyListener("%s")', sActionId);
    this.notify('onAction', sActionId);
  },

  /**
   * Store answer and any other custom data for the current card,
   * to be posted on next ajax request.
   * 
   */
  answerCard:function(oData)
  {
  //  uiConsole.log('uiFlashcardReview::answerCard(%o)', oData);

    // cache answer
    var id = this.items[this.position];
    oData._id = id;
    this.postCache.push(oData);
    this.updateUnloadEvent();
  },

  /**
   * Cleans up the answer of current flashcard (when going backwards),
   * just in case the new answer doesn't set the same kind of data/properties.
   * 
   * @return  {Object}   Returns flashcard answer data (cf. answerCard()) that is being cleared
   */
  unanswerCard:function()
  {
  //  uiConsole.log('uiFlashcardReview::unanswerCard()');
    var oData;

    if (this.getPostCount()<=0)
    {
      throw new Error();
    }

    oData = this.postCache.pop();

    this.updateUnloadEvent();
    
    return oData;
  }
  
}

/**
 * uiFlashcard handles display of a flashcard and its contents.
 * 
 */
uiFlashcard = Class.create();
uiFlashcard.prototype =
{
  initialize:function(oReview)
  {
  //  uiConsole.log('uiFlashcard::initialize()');

    // FlashcardReview parent object
    this.oFR = oReview;
    this.elFlashcard = $$('div.uiFcCard')[0];
  },

  setContent:function(cardData)
  {
  //  uiConsole.log('uiFlashcard::setContent(%o)', cardData);
    
    var elems = $(this.elFlashcard).select('.fcData');
    
    for (i = 0; i < elems.length; i++)
    {
      if (/fcData-(\w+)/.test(elems[i].className))
      {
        var sProp = RegExp.$1;
        elems[i].innerHTML = cardData[sProp] || '';
      }
    }
    
  },

  /**
   * Sets the current flashcard state with a class applied to the
   * flashcard container element. This class can be used by css rules
   * to set visible or hidden status of flashcard information.
   * 
   * @param {Number} iState
   */
  setState:function(iState)
  {
    var aClassNames = this.elFlashcard.className.replace(/uiFcState-\w+/, '').split(/\s+/);
    var sClass = 'uiFcState-' + iState;
    aClassNames.push(sClass);
    this.elFlashcard.className = aClassNames.join(' ');

    this.iState = iState;
    
    this.oFR.notify('onFlashcardState', iState);
  },

  getState:function()
  {
    return this.iState;
  },

  display:function(bDisplay)
  {
  //  uiConsole.log('uiFlashcard::display(%o)', bDisplay);
    if (bDisplay)
      $(this.elFlashcard).show(); //style.visibility = 'visible';
    else
      $(this.elFlashcard).hide(); //style.visibility = 'hidden';
  },
  
  /**
   * Cleanup events, hide the flashcard, reset html template
   * 
   */
  destroy:function()
  {
  //  uiConsole.log('uiFlashcard::destroy()');
    this.display(false);
  }
};
