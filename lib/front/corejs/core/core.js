/**
 * Core framework.
 * 
 * Core is the global namespace that acts as a wrapper around library specific code.
 * 
 * Core methods
 *   bind()           Create a closure to preserve execution context
 *   createClass()    OOP, returns constructor for a base class
 *   extend()         OOP, returns a constructor for an extended class
 *   ready()          Sets window onload code 
 *   error()          Throws an exception
 *   warn()           Log a warning message (same as log, different color in Firebug)
 *   log()            Log message to console (see console.js)
 * 
 * @author  Fabrice Denis
 */
/*jslint forin: true */
/*global YAHOO, window, YAHOO, alert, console, document */

/* =require from "%CORE%" */
/* =require "/core/core-yui2.js" */

var Core =
{
  /**
   * Proxy to the underlying javascript library.
   * 
   */
  YUI: YAHOO,
  
  /**
   * Helper to bind function with arguments always appended to
   * the END of the argument collection.
   * 
   * See toolkit.js 
   */
  bind: function(fn, context, args)
  {
    return YAHOO.bind.apply(YAHOO, arguments);
  },
  
  /**
   * A constructor function to create a new class.
   * 
   * Examples:
   *   App.Ui.FooWidget = Core.createClass();
   *   
   * @param {Object} px   Optional prototype object containing properties and methods
   * @return {Function}   Class constructor that will call init() method when instanced
   */
  createClass: function(px)
  {
    var fn = function() {
      return this.init.apply(this, arguments);      
    };
    
    // optional: set prototype for the new class
    if (px) {
      fn.prototype = px;
    }
    
    return fn;
  },
  
  /**
   * Create a child class from a base class and optional properties/methods
   * 
   * Example:
   * 
   *   var Human = Core.createClass();
   *   Human.prototype = {
   *     init: function() {
   *       // ... 
   *     } 
   *   };
   * 
   *   var SuperHuman = Core.createClass();
   *   Core.extend(SuperHuman, Human, {
   *     init: function() {
   *       // call parent constructor
   *       SuperHuman.superclass.init.apply(this, arguments);
   *     }
   *   });
   * 
   * See YAHOO.lang.extend example http://developer.yahoo.com/yui/examples/yahoo/yahoo_extend.html
   * 
   * @param {Function} subc     Sub class constructor
   * @param {Function} superc   Base class constructor
   * @param {Object} overrides  Additional properties/methods to add to the child prototype
   */
  extend: function(subc , superc , overrides) {
    YAHOO.lang.extend(subc, superc, overrides);
  },


  /**
   * Report an error.
   * 
   * @param {string} msg  the error message
   */
  error: function(msg) {
    
    window.alert('EXCEPTION: ' + msg);
    
    // doesn't work arrrghghhhh
    //throw new Error(msg);
  },
  
  /**
   * Log a warning message, uses console.warn() if Firebug is present.
   * 
   */
  warn: function()
  {
    if (console && typeof (console.warn) === 'function') {
      console.warn.apply(console.warn, arguments);
    } else {
      Core.log.apply(Core.log, arguments);
    }
  },

  /**
   * Set the onload event for the current page.
   * 
   * Use this to make sure we use the same "load" event throughout the application.
   * 
   * Example:
   *   App.ready(function(){
   *     App.getGlobalED().on("clickme", function(){ alert("I has been clicked!"); });
   *   }); 
   * 
   */
  ready: function(f)
  {
    YAHOO.util.Event.onDOMReady(f);
  }
  
};

/* =require "/core/toolkit.js" */
/* =require "/core/console.js" */

// shortcut to test & learn YUI in Firebug's console
var Y = YAHOO;
