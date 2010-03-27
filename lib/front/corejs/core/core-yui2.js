/**
 * Provides additional helpers to YUI2 and include minimal YUI2 dependencies.
 * 
 *  YAHOO.bind(fn, scope [, args, ...] )
 *  
 *  YAHOO.Array  (see  http://developer.yahoo.com/yui/3/api/Array.html )
 *    Y.Array(o)
 *    Y.Array.each(array, fn [, context])
 *  
 *  YAHOO.util.Dom
 *    down(rootEl, classname [, tagname])
 *    setStyles(element, hash)
 * 
 *  YAHOO.bind(fn, scope [, args, ...] )
 * 
 * @author Fabrice Denis
 */
/*! Copyright (c) 2010, Yahoo! Inc. All rights reserved. */

/* =require from "%YUI2%" */
/* !require "/yahoo/yahoo.js" */
/* !require "/dom/dom.js" */
/* !require "/event/event.js" */
/* =require "/yahoo-dom-event/yahoo-dom-event.js" */

/* These are for reference, but not enabled in the minimal build */

/* !require "/dragdrop/dragdrop-min.js" */
/* !require "/animation/animation-min.js" */
/* !require "/connection/connection-min.js" */
/* !require "/container/container-min.js" */

/**
 * Augment YUI 2.8.0r4 with useful method/helpers, some of which are patched from YUI 3.0.0 Beta1.
 * 
 */
(function() {

  var Y = YAHOO,
      Dom = Y.util.Dom,
      ArrayNative = Array.prototype;
  
  /**
   * @name YAHOO.Dom
   */
  Y.lang.augmentObject(Dom,
  {
    /**
     * 
     * @param {Object} el
     * @param {Object} styles
     */
    setStyles: function(el, styles)
    {
      for (var s in styles) {
        Dom.setStyle(el, s, styles[s]);
      }
    },

    /**
     * Returns first child element of given classname.
     * 
     * @param {HTMLElement|String}  rootEl   Root element (if string, pass an id) 
     * @param {String} classname  Classname is required
     * @param {String} tagname    Optional tag name, may speed up the find if lots of child elements
     * 
     * @return {HTMLElement}      Element or undefined
     */
    down: function(rootEl, classname, tagname)
    {
      return Dom.getElementsByClassName(classname, tagname || "*", Dom.get(rootEl))[0];
    }
  });

  if (!Y.bind)
  {
    /**
     * Returns a function that will execute the supplied function in the
     * supplied object's context, optionally adding any additional
     * supplied parameters to the END of the arguments the function
     * is executed with.
     *
     * In some cases it is preferable to have the additional arguments
     * applied to the beginning of the function signature.  For instance,
     * FireFox setTimeout/setInterval supplies a parameter that other
     * browsers do not.
     *   
     * Note: YUI provides a later() function which wraps setTimeout/setInterval,
     * providing context adjustment and parameter addition.  This can be 
     * used instead of setTimeout/setInterval, avoiding the arguments
     * collection issue when using bind() in FireFox.
     *
     * @param f {Function} the function to bind
     * @param c the execution context
     * @param args* 0..n arguments to append to the end of arguments collection
     * 
     * @return {function} the wrapped function
     */
    Y.bind = function(f, c) {
      var a = ArrayNative.slice.call(arguments, 2);
      return function () {
        return f.apply(c || f, ArrayNative.slice.call(arguments, 0).concat(a));
      };
    };
  }

})();


/**
 * Add useful helpers from YUI 3.0.0b1
 * 
 * YAHOO.Array 
 * 
 */
(function() {

  var Y = YAHOO,
      Native = Array.prototype;

  /**
   * YAHOO.Array from YUI 3.0.0 Beta 1 bugged in Safari 3! => Using plain old iteration code.
   * 
   * TODO: Check code from latest YUI 3 build
   * 
   * @param {Object} collection
   */
  Y.Array = function(o)
  {
    a=[];
    for (i=0, l=o.length; i<l; i=i+1) {
        a.push(o[i]);
    }
    return a;
  };
  
  /**
   * Executes the supplied function on each item in the array.
   * 
   * The function is called with arguments: element, index, the_array
   * 
   * @method each
   * @param a {Array} the array to iterate
   * @param f {Function} the function to execute on each item
   * @param o Optional context object
   * @static
   * @return {YUI} the YUI instance
   */
  Y.Array.each = (Native.forEach) ?
    function (a, f, o) { 
        Native.forEach.call(a || [], f, o || Y);
        return Y;
    } :
    function (a, f, o) { 
        var l = (a && a.length) || 0, i;
        for (i = 0; i < l; i=i+1) {
            f.call(o || Y, a[i], i, a);
        }
        return Y;
    };

})();

