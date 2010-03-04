/*jslint forin: true */
/*global window, Core, App, YAHOO, alert, console, document */
/**
 * Core.Ui
 * 
 * Core.Ui Helpers
 *   getParams()                 Parse values from HTML element class names
 *   getDataset()                Get values from an element's HTML5 "data-" attributes
 *   insertTop()                 Insert node at top of document, but within #ie if present to enable IE styles
 *   toggle()                    Toggle display of element (but use "" rather than "block", for TDs etc)
 * 
 *  
 * @author Fabrice Denis
 */

if (typeof(Core) === 'undefined' || !Core.YUI) {
  throw new Error('Core or YUI is not declared');
}


/**
 * Core.Ui Helpers
 * 
 * These are global helpers related to the DOM and user interface. 
 */
(function(){
  
  var Y = YAHOO,
      Dom = Y.util.Dom;
  
  Core.Ui =
  {
    /**
     * Returns first element that matches the classname.
     * If no argument is given, returns immediate child element.
     * 
     * @param {Object} el
     * @param {Object} classname
     * 
     * @return {HTMLElement}
     */
    down: function(el, classname)
    {
      if (!arguments.length) {
        return Dom.getFirstChild(el);
      }
      
      return Dom.getElementsByClassName(classname, "*", el)[0];
    },
    
    /**
     * Return parameters and values that are passed through
     * the HTML Element class names.
     * 
     * Eg: <div class="module module-id-1 module-status-off"> ... </div>
     * 
     * Returns: Object { id: "1", status: "off" }
     * 
     * @param {HTMLElement} el     The element
     * @param {String}   name      The base class name (without dash suffix!)
     */
    getParams: function(el, name)
    {
      var re = new RegExp("(?:^|\\s)" + name + "-([^-]+)-(\\w+)", "g"),
          obj = {},
          a;
      
      while ((a = re.exec(el.className)))
      {
        var prop = a[1], value = a[2];
        obj[prop] = value;
      }
      
      return obj;
    },
    
    /**
     * Returns an object with properties mapped to the element's data-* attributes.
     * 
     * The "data-*" attributes will validate with HTML 5 validator.
     * 
     * Example markup:
     * 
     *   <ol>
     *     <li data-foo="bar" data-author="456">Beyond The Sea</li>
     *     ...
     *   </ol>
     * 
     * Example code:
     * 
     *   var length = App.Ui.Helper.getDataset(li);
     *   
     *   => { "foo": "bar", "author": "456" }
     * 
     *   
     * TODO   Ideally this should return element.dataset if present (for speed), but unless we can
     *        test it don't use it or it may break in future browser versions
     * @see   http://dev.w3.org/html5/spec/Overview.html#embedding-custom-non-visible-data
     *        http://ejohn.org/blog/html-5-data-attributes/
     */
    getDataset: function(el)
    {
      var i, dataset = {};
      for (i = 0; i < el.attributes.length; i++)
      {
        var attrib = el.attributes[i];
        if (attrib.specified === true && attrib.name.indexOf("data-") === 0)
        {
          //App.log(attrib.name + " = " + attrib.value);
          dataset[attrib.name.slice(5)] = attrib.value;
        }
      }
      return dataset;
    },

    /**
     * Insert node as first child of the document, _EXCEPT_ for IE, insert
     * as the first child of the #ie div (which should be first child of document.body).
     * 
     * This ensures that styles targetting IE (eg. #ie .foobar { ... } ) will work
     * on dialogs and other elements dynamically inserted into the page. 
     * 
     * @param {HTMLElement} node
     */    
    insertTop: function(node)
    {
      var elParent = Dom.get("ie") || document.body;
      elParent.insertBefore(node, elParent.firstChild);
    },

    /**
     * Toggle display of element.
     * 
     * Use "" instead of "block" so it works on table cells in IE.
     * 
     * @param {Object} el        HTMLElement reference
     * @param {Bool} display     Boolean    
     */
    toggle: function(el, display)
    {
      el.style.display = display ? "" : "none";
    }
    
  };

})();
