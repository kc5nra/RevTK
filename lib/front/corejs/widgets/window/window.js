/**
 * Window implements an OS window-like overlay that can be dragged and can have a close button.  
 * 
 * Initial implementation:
 * - the window is always centered on the viewport when shown (show())
 * - any class set on container is retained on the direct parent of the hd/bd/ft divs
 * 
 * Config:
 *   width            Width as a css property, requried (eg. "200px")
 *   events           Listeners to register (see below)
 *   underlay         Set expicilty to false to disable the underlay, set to true to enable
 *                    shadow similar to YUI2 Panel, set to a string to enable custom markup
 *                    for the underlay div (eg. png drop shadow with rounded corners)
 *   
 * Methods:
 * 
 *   show()           Display window
 *   hide()           Hide window
 *   destroy()        Closes the window, remove event listeners
 * 
 *   getBody()        Returns body container element
 *   
 * Listeners:
 *   onWindowClose()   The close button has been clicked, called BEFORE destroy() method
 * 
 * Todo:
 * - custom positioning
 *  
 * @author   Fabrice Denis
 * @package  Ui/Widgets
 */
(function() {
  
  Core.Widgets.Window = Core.createClass();
  
  var Y              = Core.YUI,
      Window         = Core.Widgets.Window,
      WINDOW_CLASS   = "window",
      UNDERLAY_PAD   = 8;

  Window.prototype =
  {
    /**
     * 
     * 
     * @param {Object} el  Element or string id for source markup
     * @param {Object} options
     */
    init: function(el, config)
    {
      var srcMarkup = Core.Ui.get(el), 
          contents;

      if (!srcMarkup) {
        Core.halt("Window.init()  'el' does not appear to be a container.");
      }

      // clone content to prevent YUI from deleting the source 
      contents = srcMarkup.cloneNode(true);
      contents.id = null;
      Y.DOM.setStyles(contents, {
        visibility: "visible",
        display:    "block"
      });

      this.overlay = new Y.Overlay({
        contentBox:  contents,
        centered:    true,
        shim:        false,
        visible:     false,
        width:       config.width
      });

      this._getBoundingBox().addClass(WINDOW_CLASS);

      this.overlay.render();
      
      var contentBoxNode = this._getContentBox();
      var underlayHtml =
        '<div class="underlay">' + 
         '<table cellspacing="0" class="uiBoxRounded uiWindowUnderlay">' +
          '<tbody>' +
           '<tr class="t"><td class="l"><b/></td><td/><td class="r"><b/></td></tr>' +
           '<tr class="m"><td/><td class="c"></td><td/></tr>' +
           '<tr class="b"><td class="l"><b/></td><td/><td class="r"><b/></td></tr>' +
          '</tbody>' +
         '</table>' +
        '</div>';

      underlay = Y.Node.create(underlayHtml);
      underlay.setStyles({
        //backgroundColor: "#000000",
        opacity:    0.5,
        position:   "absolute",
        left:       "-" + UNDERLAY_PAD + "px",
        right:      "-" + UNDERLAY_PAD + "px",
        top:        "-" + UNDERLAY_PAD + "px",
        bottom:     "-" + UNDERLAY_PAD + "px",
        // screw IE6
         //width:      (parseFloat(config.width) + 6) + "px",
         //height:     (Core.Ui.get(contentBoxNode).offsetHeight + 0) + "px",
        zIndex:     0
      });
      
      contentBoxNode.setStyles({
        position:   "relative",
        zIndex:     1
      });
      
      //this._getContentBox().ancestor().append(underlay);      

      // register events
      /*
      this.eventDispatcher = new Core.Ui.EventDispatcher();
      if (config.events) {
        for (var sEvent in config.events) {
          this.eventDispatcher.connect(sEvent, config.events[sEvent]);
        }
      }
      */
    },
    
    /**
     * Returns the YUI widget's outer element.
     * 
     * @return {Object}  YUI Node
     */
    _getBoundingBox: function()
    {
      return this.overlay.get("boundingBox");
    },
    
    /**
     * Returns the YUI widget's content element (parent of hd/bd/ft content).
     * 
     * @return {Object}  YUI Node
     */
    _getContentBox: function()
    {
      return this.overlay.get("contentBox");
    },
    
    show: function()
    {
      if (Y.DD) {
        this.dd = new Y.DD.Drag({ node: this._getBoundingBox() });
        this.dd.addHandle(".yui-widget-hd"); 
      }
      
      this.overlay.show();
    },
    
    hide: function()
    {
      this.overlay.hide();
    },
    
    close: function()
    {
      this.destroy();
    },
  
    destroy: function()
    {
      if (this.dragdrop)
      {
        this.dragdrop.unreg();
      }
  
      this.evtCache.destroy();
    },
  
    getBody: function()
    {
      return this.elBody;
    }
  };

})();
