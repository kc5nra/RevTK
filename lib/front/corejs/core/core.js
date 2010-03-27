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
 *   halt()           Throws an error message (maps to Firebug console.error() if present).
 *   warn()           Log a warning message (maps to Firebug console.warn() if present)
 *   log()            Log message to console (maps to Firebug console.log() if present)
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
   *   Core.Ui.FooWidget = Core.createClass();
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
  extend: function(subc , superc , overrides)
  {
    YAHOO.lang.extend(subc, superc, overrides);
  },

  /**
   * Throws an error message (maps to Firebug console.error() if present).
   * 
   * @param {String}  Message, followed by optional arguments (sprintf style)
   */
  halt: function(message)
  {
    // throw exception doesn't work in Firefox?

    if (typeof(console) !== "undefined" && typeof (console.error) === 'function') {
      console.error.apply(console, arguments);
    }
    alert(message);
  },

  /**
   * Log a warning message (maps to Firebug console.warn() if present).
   * 
   * Use this to report potential problems which should not show in production.
   * 
   * @param {String}  Message, followed by optional arguments (sprintf style)
   */
  warn: function()
  {
    if (typeof(console) !== "undefined" && typeof(console.warn) === 'function') {
      console.warn.apply(console, arguments);
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
   *   Core.ready(function(){
   *     Core.log('Ready.'); 
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
