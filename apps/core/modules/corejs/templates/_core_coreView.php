<p>The Core object</p>

<?php pre_start('js') ?>
// Bind function to scope with optional arguments
Core.bind(fn, context, args)           

// OOP, returns constructor for a base class
Core.createClass()
// OOP, returns a constructor for an extended class
Core.extend()

// Helper to bootstrap page code with onDOMReady
Core.ready(fn);

// Log message to console (maps to Firebug console.log() if present)
Core.log(msg[, args])
// Log a warning message (maps to Firebug console.warn() if present)
Core.warn(msg[, args]);
// Throws an exception (maps to Firebug console.error() if present).
Core.halt(msg[, args]);
<?php pre_end() ?>


<h2>corejs/core/toolkit.js</h2>

<p> toolkit.js provides additional helpers and missing functionality to the underlying javascript library (YUI2)</p>

<?php pre_start('js') ?>
// Turns an object into its URL-encoded query string representation.
Core.Toolkit.toQueryString(o)
<?php pre_end() ?>

<h2>DOM ready event</h2>

<?php pre_start('js') ?>
Core.ready(function() {
  var Y = Core.YUI;
  // do something...
  var ajaxTable = new Core.Ui.AjaxTable(Y.one('#demo'));
});
<?php pre_end() ?>
