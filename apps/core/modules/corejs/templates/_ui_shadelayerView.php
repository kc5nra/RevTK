<?php slot('inline_styles') ?>
.panel_box { margin:0 0 1em; border:4px solid #CCC; background:#eee; color:#444; padding:10px; width:400px; }
<?php end_slot() ?>

<h2>uiShadeLayer</h2>

<p> Show/hide layer <button id="toggle">click me</button>.

<p> Destroy the layer and it should be removed from the document : <button class="click-to-destroy">Destroy</button>

<p> Shade layer on the document body <button id="btn_body">Click me</button> (click the layer to close it).

<div class="panel_box" id="demo">
  <p>Prototype is a JavaScript Framework that aims to ease development of dynamic web applications.
  <p>Select box display bug in IE6: <select><option>Tea</option></select>
  <p>Featuring a unique, easy-to-use toolkit for class-driven development and the nicest Ajax library around, Prototype is quickly becoming the codebase of choice for web application developers everywhere.
</div>

<script type="text/javascript">
App.ready = function()
{
  var Y = Core.YUI,
      Dom = Y.util.Dom,
      testLayer = null;

  this.evtDel.onId("toggle", function(e, el)
  {
    if (!testLayer) {
       testLayer = new Core.Ui.ShadeLayer({element: Dom.get('demo')});
       testLayer.show();
    }
    else if (testLayer.visible()) {
      testLayer.hide(); 
    }
    else {
      testLayer.show();
    }
  });

  this.evtDel.onId("btn_body", function(e, el)
  {
    if (testLayer) {
      testLayer.destroy();
    }
    testLayer = new Core.Ui.ShadeLayer( { element: document.body } );
    Dom.addClass(testLayer.getLayer(), "click-to-destroy");
    testLayer.show();
  });

  this.evtDel.on("click-to-destroy", function(e, el)
  {
    testLayer.destroy();
    testLayer = null;
  });
  
};
</script>
