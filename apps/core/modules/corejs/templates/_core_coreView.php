
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

// Throws an exception
Core.error(msg);
// Log message to console with optional printf style arguments
Core.log(msg [, args])
<?php pre_end() ?>


<h2>corejs/core/toolkit.js</h2>

<p> toolkit.js provides additional helpers and missing functionality to the underlying javascript library (YUI2)</p>

<?php pre_start('js') ?>
// Turns an object into its URL-encoded query string representation.
YAHOO.Toolkit.toQueryString(o)
<?php pre_end() ?>

<h2>DOM ready event</h2>

<?php pre_start('js') ?>
Core.ready(function() {
  var Y = Core.YUI;
  // do something...
  var ajaxTable = new Core.Ui.AjaxTable(Y.one('#demo'));
});
<?php pre_end() ?>
