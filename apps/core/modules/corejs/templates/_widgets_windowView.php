<?php use_helper('Form', 'Widgets') ?>

<?php slot('inline_styles') ?>
.window-body { color:#000; background:#fff; padding:8px; font-size:13px; line-height:1.2em; }
<?php end_slot() ?>

<p> Use Window to create a simple draggable window with a close button.<br/>
	  <?php echo input_tag('btnOne', 'Open Window', array('type' => 'button', 'id' => 'demo1')) ?>

<?php echo ui_window('Hello world', array('id' => 'Demo1Window')) ?>

<div id="window-demo-1">
  <div class="yui-widget-hd">
    <div class="left"></div>
    <div class="middle">
      <div class="window-title">Head</div>
    </div>
    <div class="right"></div>
  </div>
  <div class="yui-widget-bd">
    <div class="left"></div>
    <div class="middle window-body">
      Body, lorem ipsumus. Deveant inici peum subpoena.
    </div>
    <div class="right"></div>
  </div>
  <div class="yui-widget-ft">
    <div class="left"></div>
    <div class="middle"></div>
    <div class="right"></div>
  </div>
</div>

<?php #Testing the PNG image ?>
<!--div style="background:url(/corejs/widgets/window/assets/window2.png) no-repeat 0 0;width:96px;height:96px;position:absolute;left:100px;top:100px;"></div-->


<script type="text/javascript">
Core.ready(function() {

  var Y = Core.YUI,
      wndw = null;

  function onClick(e)
  {
    w = new Core.Widgets.Window("window-demo-1", {
      width:      "300px",
      events: {
        onWindowClose: onWindowClose
      }
    });
    w.show();
  }

  window.setTimeout(function(){ onClick(); }, 300);
  
  function onWindowClose()
  {
    Core.log("onWindowClose() called");
  }

  Y.one("#demo1").on("click", onClick);
  
});
</script>
