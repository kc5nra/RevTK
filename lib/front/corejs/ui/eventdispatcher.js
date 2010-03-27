/**
 * EventDispatcher implements the observer design pattern.
 * 
 * Methods:
 *   connect(name, fn [, scope])
 *   disconnect(name[, fn])
 *   notify(name [, arg1[, ...]])
 *   hasListeners(name)
 * 
 * @see     http://developer.apple.com/documentation/Cocoa/Conceptual/Notifications/index.html Apple's Cocoa framework
 * 
 * @author  Fabrice Denis
 * @version 2009.07.17 (notify() now has return value, added scope to listeners)
 */
(function (){

  Core.Ui.EventDispatcher = Core.createClass();

  var Y = Core.YUI,
      EventDispatcher = Core.Ui.EventDispatcher;

  EventDispatcher.prototype =
  {
    listeners: null,

    init: function()
    {
      this.listeners = {};
    },
    
    destroy: function()
    {
      this.listeners = {};
    },
  
    /**
     * Connects a listener to a given event name.
     *
     * @param {String}    name     The type of event (the event's name)
     * @param {Function}  fn       A javascript callable
     * @param {Object}    context  Context (this) for the event. Default value: the window object.
     */
    connect: function(name, fn, context)
    {
      if (!this.listeners[name]) {
        this.listeners[name] = [];
      }

      this.listeners[name].push({
        fn:    fn,
        scope: context || window
      });
    },
    
    /**
     * Disconnects a listener, or all listeners, for an event.
     *
     * If fn is not specified, then all listeners for this event are unsubscribed.
     *
     * @param {String}    name   An event name
     * @param {Function=}  fn     A javascript callable (optional)
     *
     * @return {?number}    Number of listeners unsubscribed, or null the listener is not found
     */
    disconnect: function(name, fn)
    {
      var i, l, callables, s;
  
      if (!this.listeners[name]) {
        return null;
      }
  
      // if listener is undefined, delete all listeners
      if (!fn) {
        fn = true;
      }
  
      callables = this.listeners[name];
      l = callables.length;
      
      for (i = l - 1; i > -1; i--) {
        s = callables[i];
        if (true === fn || s.fn === fn) {
          delete s.fn;
          delete s.scope; 
          callables.splice(i, 1); // unset array item
        }
      }
 
      return l;
    },
  
    /**
     * Notifies all listeners of a given event.
     *
     * @param {string} name  An event name
     * @param {...*} arguments  An arbitrary set of parameters to pass to the handler.
     * @return {boolean|null} false  False if one of the subscribers returned false, true otherwise
     */
    notify: function(name)
    {
      var args = Array.prototype.slice.call(arguments, 0),
          i, ret, subscriber;

      args.shift();
      callables = this.listeners[name] ? this.listeners[name] : [];

      if (args.length===1 && Y.lang.isArray(args[0])) {
        alert('EventDispatcher()  using obsolete notify() signature?');
      }
  
      if (!callables.length) {
        return null;
      }
  
      for (i = 0; i < callables.length; i++) {
        subscriber = callables[i]; 
        ret = subscriber.fn.apply(subscriber.scope, args.length ? args : []);
        if (false === ret) {
          break;
        }
      }
      
      return (ret !== false);
    },
  
    /**
     * Returns true if the given event name has some listeners.
     *
     * @param {string} name    An event name
     *
     * @return {boolean} true if some listeners are connected, false otherwise
     */
    hasListeners: function(name)
    {
      if (!this.listeners[name]) {
        return false;
      }
      return this.listeners[name].length > 0;
    }
  };
})();
