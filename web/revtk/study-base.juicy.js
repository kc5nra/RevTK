
/**
 * Study page - includes (Still using old prototype-based javascript)
 * 
 * Juicer build:
 *  php lib/juicer/JuicerCLI.php -v --webroot web --config apps/revtk/config/juicer.config.php --infile web/revtk/study-base.juicy.js
 * 
 * Minification:
 *  java -jar batch/tools/yuicompressor/yuicompressor-2.4.2.jar web/xyz.js -o web/xyz.min.js
 *   
 * @package RevTK
 * @author  Fabrice Denis
 */

/* =require from "%FRONT%" */
/* =require "/lib/prototype.min.js" */
/* =require "/scripts/autocomplete.js" */

/* =require from "%WEB%" */
/* =require "/js/ui/uibase.js" */
/* =require "/js/2.0/study/keywords.js" */
/* =require "/js/2.0/study/EditStoryComponent.js" */

/**
 *
 * Options:
 *   URL_SEARCH
 *   URL_SHAREDSTORIES
 */
var StudyPage =
{
  initialize:function(options)
  {
    // otpions & php constants
    this.options = options;
    
    // references
    this.elSearch = $('txtSearch');
    
    // quick search autocomplete
    var actb1 = this.actb1 = new actb(this.elSearch, kwlist);
    actb1.onChangeCallback = this.quicksearchOnChangeCallback.bind(this);
    actb1.onPressEnterCallback = this.quicksearchEnterCallback.bind(this);
    actb1.actb_extracolumns = function(iRow) {
      return '<span class="framenum">'+(iRow+1)+'</span><span class="k">&#'+kklist.charCodeAt(iRow)+';</span>';
    }

    // clicking in quick search box selects the text
    this.elSearch.onfocus = function()
    {
      if (this.value!=='')
      {
        this.select();
      }
    }

    // auto focus search box
    if (this.elSearch && this.elSearch.value==='')
    {
      this.elSearch.focus();
    }
    
    if ($('EditStoryComponent'))
    {
      this.editStoryComponent = new EditStoryComponent($('EditStoryComponent'));
    }

    if ($('SharedStoriesComponent'))
    {
      this.sharedStoriesComponent = new SharedStoriesComponent($('SharedStoriesComponent'));
    }
  },
  
  onSearchBtn: function(e)
  {
    var text = this.elSearch.value;
    this.quicksearchOnChangeCallback(text);
    Event.stop(e);
    return false;
  },

  /**
   * Auto-complete onchange callback, fires after user selects
   * something from the drop down list.
   * 
   * @param  string  text  String typed into the searchbox
   * 
   * @see    autocomplete.js
   */
  quicksearchOnChangeCallback:function(text)
  {
    if (text.length > 0)
    {
      // Replace slash with underscore for URL routing
      text = text.replace(/\//, '_');
      window.location.href = this.options.URL_SEARCH + '/' + encodeURIComponent(text);
      return true;
    }
  },

  /**
   * Auto-complete ENTER key callback.
   * 
   * @see    autocomplete.js
   */
  quicksearchEnterCallback:function(text)
  {
    this.quicksearchOnChangeCallback(text);
  }
  
};

/**
 * SharedStoriesComponent
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

var SharedStoriesComponent = Class.create();
SharedStoriesComponent.prototype =
{
  initialize:function(elContainer)
  {
    this.evtCache = new uiEventCache();
    this.evtCache.addEvents(elContainer, ['mouseover','mouseout','click'], this.evEventHandler.bind(this));
    
    this.ajaxPanel = new uiAjaxPanel(elContainer, 
    {
      post_url: StudyPage.options.URL_SHAREDSTORIES,
      events: {
        onContentInit:    this.onContentInit.bind(this),
        onContentDestroy: this.onContentDestroy.bind(this)
      },
      bUseShading: true
    });
  },
  
  destroy:function()
  {
    this.evtCache.destroy();
  },
  
  reload:function()
  {
    var params = { framenum: document.forms['EditStory'].elements['framenum'].value };
    this.ajaxPanel.get(params);
  },
  
  onContentInit:function()
  {
  },

  onContentDestroy:function()
  {
  },
  
  evEventHandler:function(oEvent)
  {
    var elem = oEvent.element();
    var parentDiv = oEvent.findElement('div');
    
    if (!parentDiv)
      return;
    
    // show/hide newest stories
    if (parentDiv.className.indexOf('JsNewest')>=0)
    {
      var ofs = oEvent.type==='mouseover' ? -33 : 0;
      parentDiv.style.backgroundPosition = '0 '+ofs+'px';
      if (oEvent.type==='click')
      {
        var newestStoriesDiv = $('sharedstories-new');
        if (!this.hideStories) {
          $(newestStoriesDiv).addClassName('JsHide');
          this.hideStories = true;
        }
        else {
          $(newestStoriesDiv).removeClassName('JsHide');
          this.hideStories = false;
        }
      }
      
      oEvent.stop();
      return false;
    }
    
    if (parentDiv.className!=='action')
      return;

    var span = parentDiv ? parentDiv.getElementsByTagName('span')[0] : null;
    switch(oEvent.type)
    {
      case 'mouseover':
        var tooltips = {
        /*  undo:  'Cancel your vote',*/
          star:  'Star this story',
          report:  'Report this story',
          copy:  'Copy this story'
        };
        var s_msg = tooltips[elem.className] || '';
        if (s_msg) {
          span.className = '';
          span.innerHTML = s_msg;
        }
        break;
      case 'mouseout':
        if (span) {
          var s = span.getAttribute('lastmsg') || '';
          span.className = s ? 'msg' : '';
          span.innerHTML = s;
        }
        break;
      case 'click':
        var ids = parentDiv.id.split('-');
        var nowclick = (new Date()).getTime();
        var nowsecs = this.lastclick ? (nowclick - this.lastclick) : 1000;
        this.lastclick = nowclick;

        if (nowsecs < 300 || this.ajaxRequest)
        {
          span.className = 'err';
          span.innerHTML = 'Not too fast buddy!';
          break;
        }

        var params = {
          uid:    ids[1],
          sid:    ids[2]
        };

        if (/star/.test(elem.className))
        {
          params.request = 'star';
        }
        else if (/report/.test(elem.className))
        {
          params.request = 'report';
        }
        else if (/copy/.test(elem.className))
        {
          params.request = 'copy';
        }

        if (params.request)
        {
          var that = this;
          options = {
            method:     'post',
            parameters:  params,
            onSuccess:  this.handleResponse.bind(this),
            onComplete: function() { that.ajaxRequest = null; }
          };
          this.ajaxRequest = new uiAjaxRequest(StudyPage.options.URL_SHAREDSTORIES, options);

          span.className = '';
          span.innerHTML = '...';
        }
        break;
    }
    
    oEvent.stop();
    return false;
  },

  handleResponse:function(oAjaxResponse)
  {
    var data = oAjaxResponse.responseText.evalJSON();
    if (data)
    {
      // copy & edit story
      if (data.text) 
      {
        var frmEditStory = document.forms['EditStory'];
        if (frmEditStory) 
        {
          frmEditStory.elements['chkPublic'].checked = false;
          
          // scroll to top of window
          var dx = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0;
          window.scrollTo(dx, 0);
          
          StudyPage.editStoryComponent.editStory(data.text);
        }
        return;
      }

      var div = $('story-'+data.uid+'-'+data.sid);
      var span = div.getElementsByTagName('span')[0];

      if (data.vote>=0)
      {
        var anchors = div.getElementsByTagName('a');

        if (!data.vote && data.lastvote){
          var s = 'Vote cancelled';
        }
        else if (data.vote==1){
          var s = 'Starred!';
        }
        else if (data.vote==2){
          var s = 'Reported';
        }

        // update counts
        var stars = div.getAttribute('appv1') || '0';
        var kicks = div.getAttribute('appv2') || '0';
        stars = parseInt(stars) + parseInt(data.stars);
        kicks = parseInt(kicks) + parseInt(data.kicks);
        div.setAttribute('appv1',stars);
        div.setAttribute('appv2',kicks);
        anchors[0].innerHTML = stars ? stars+'&nbsp;' : '&nbsp;';
        anchors[1].innerHTML = kicks ? kicks+'&nbsp;' : '&nbsp;';
        
        // feedback
        if (s) {
          span.innerHTML = s;
          span.className = 'msg';
          span.setAttribute('lastmsg',s);
        }
      }
      else {
        var s = 'No self vote!';
        span.innerHTML = s;
      }
    }
  }
};

