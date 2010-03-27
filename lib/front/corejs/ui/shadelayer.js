/*jslint forin: true */
/*global window, Core, App, YAHOO, alert, console, document */
/**
 * ShadeLayer creates a absolutely positioned div that covers an area of the page,
 * with a solid color and a level of transparency. Typical use is to show an
 * area as "disabled" while a dialog is on, or while content is loading with ajax.
 * 
 * Options
 *   element                   If set, the layer is positioned to cover the element's area.
 *   pos
 *     left, top               Must be set if element is not specified.
 *   size
 *     width, height           Must be set if element is not specified. 
 *   color                     The solid color to use, in CSS format ('#rgb' or '#rrggbb'). (OPTIONAL)
 *   opacity                   Should be a value from 0 to 100. 0 is invisible, 100 is opaque. (OPTIONAL)
 *   glass_mode                The layer is fully transparent (no color, no opacity) (DEFAULT false)
 * 
 * Methods
 * 
 *   show()
 *   hide()
 *   resize()           Updates the layer dimensions based on the element (element option must be set)
 *                      This is mostly for when the element is the document body, and the window is resized.
 *   visible()          Returns true if visible
 *   getLayer()         Returns the DIV element created for the shade layer
 *   getDimensions()    Returns dimensions of the element, if element is document.body returns viewport dimensions.
 * 
 * Usage
 * 
 *   When the layer is created, it is not shown by default, show() must be called.
 *   This allows to change some default values such as zIndex, before displaying the layer.
 * 
 * @author  Fabrice Denis
 */
(function(){
  
  Core.Ui.ShadeLayer = Core.createClass();

  var Y = Core.YUI,
      Dom = Y.util.Dom,
      //Event = Y.util.Event,
      ShadeLayer = Core.Ui.ShadeLayer,

    /**
     * Default settings
     */
    DEFAULT_COLOR = '#000',
    DEFAULT_OPACITY = 20,
  
    /**
     * Currently assumed to be lower value than App.Ui.AjaxIndicator DEFAULT_ZINDEX
     */
    DEFAULT_ZINDEX = 90;

  ShadeLayer.prototype = {
  
    init: function(options)
    {
      this.color = options.color || DEFAULT_COLOR;
  
      this.opacity = typeof(options.opacity)!=='undefined' ? options.opacity : DEFAULT_OPACITY;
      this.opacity = Math.max(Math.min(this.opacity, 100), 0);
  
      // look for conditional comment div
      this.isIE = !!Dom.get('ie');
  
      if (options.element)
      {
        var offsets = Dom.getXY(options.element);
        this.pos  = {
          left: offsets[0],
          top:  offsets[1]
        };
        
        this.size = this.getDimensions(options.element);

        this.element = options.element;
      }
      else
      {
        this.pos  = options.pos;
        this.size = options.size;
      }

      var elLayer = document.createElement("div");
      var layerStyles = {
        display:  'none',
        position: 'absolute',
        top:      this.pos.top+'px',
        left:     this.pos.left+'px',
        zIndex:   DEFAULT_ZINDEX,
         width:    /%$/.test(this.size.width) ? this.size.width : this.size.width+'px',
        height:   /%$/.test(this.size.height) ? this.size.height : this.size.height+'px'
      };
      
      // set shading
      if (!options.glass_mode) {
          // ex: background:#85a5d2; opacity:0.5; filter:alpha(opacity=50);
        layerStyles.background = this.color;
         layerStyles.opacity = Math.round(this.opacity)/100;
      }
  
      Dom.setStyles(elLayer, layerStyles);
       if (this.isIE){
         // IE6 opacity filter
        elLayer.style.filter = 'alpha(opacity=' + this.opacity + ')';
      }

  //console.log('x %o', elLayer);

      this.elBody = document.getElementsByTagName("body")[0];
      this.elBody.insertBefore(elLayer, this.elBody.firstChild);
      this.elLayer = elLayer;
    },
    
    show: function()
    {
      // update dimensions (if repeating show/hide and the container content changes)
      if (this.element) {
        this.resize();
      }
      Dom.setStyle(this.elLayer, "display", "block");
    },
    
    hide: function()
    {
      Dom.setStyle(this.elLayer, "display", "none");
    },
  
    resize: function()
    {
      if (!this.element) {
        throw new Error('uiShadeLayer.resize() cannot resize without element');
      }
      
      var newSize = this.getDimensions(this.element);
  
      // try to fix small gap caused by Firefox
  //    var elementSize = 
  
      if (newSize.width != this.size.width || newSize.height != this.size.height) 
      {
        this.size = newSize;
        //  uiConsole.log('resize to %o', this.size);
        Dom.setStyles(this.elLayer,
        {
          width: this.size.width + 'px',
          height: this.size.height + 'px'
        });
      }
    },
    
    visible: function()
    {
      return this.elLayer.style.display !== "none";
    },
  
    destroy: function()
    {
      if (this.elLayer) {
        this.elBody.removeChild(this.elLayer);
      }
      this.elLayer = null;
    },
    
    getLayer: function()
    {
      return this.elLayer;
    },
  
    /**
     * Wrapper that returns the dimensions of the entire viewport if element is the
     * document body, or the dimensions of a specific element, calls the appropriate
     * Prototype function.
     * 
     * @param {HTMLElement}  element
     */
    getDimensions: function(element)
    {
      if (element === document.body)
      {
        return {
          width: Dom.getViewportWidth(),
          height: Dom.getViewportHeight() 
        };
      }
      else
      {
        var region = Dom.getRegion(element);
        return {
          width: region.width,
          height: region.height
        };
      }
    }
  };

})();

