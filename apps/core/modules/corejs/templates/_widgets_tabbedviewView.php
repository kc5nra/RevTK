<?php use_helper('Widgets') ?>

<?php slot('inline_styles') ?>
<?php end_slot() ?>

<h2>uiTabs</h2>

<p> The <samp>ui_tabs()</samp> helper, specifying the "uiTabs" class for default styles:

<?php echo ui_tabs(array(
      array( 'Dummy' ),
      array( 'Clicky', 'index/home', array('onclick' => 'alert("clicked!");return false;') ),
      array( 'Homepage', '@homepage', array('class' => 'monkeys', 'id' => 'Chimp') )
    ), 0, array('class' => 'uiTabs'));
  ?>

<h2>uiTabbedView</h2>

<p> To create tabbed content, add view ids as class attributes on the links, and as id attribute
    on the view elements (see the javascript class uiTabbedView())

<p> By registering listeners, it is possible to load contents of a tab or run other
    javascript initializations only when the tab is activated.

<?php echo ui_tabs(array(
      array( 'Simple', '#uiTabbedView-simple'),
      array( 'Alert',  '#uiTabbedView-alert', array('class' => 'custom_class') ),
      array( 'Ajax',   '#uiTabbedView-ajax', array('id' => 'custom_id') )
    ), 0, array('id' => 'demo', 'class' => 'uiTabs')) ?>
<div class="uiTabbedBody">
  <div id="uiTabbedView-simple">
    View one.
  </div>
  <div id="uiTabbedView-alert" style="display:none">
    View two.
  </div>
  <div id="uiTabbedView-ajax" style="display:none">
    View three.
  </div>
</div>

<h2>Vertical uiTabbedView</h2>

<p> Vertical tabs à-la-GMail, simply by changing the CSS.

<style type="text/css">
#vtabs .uiTabs { float:left; width:150px; margin:10px 0 0; }
#vtabs .uiTabbedBody { margin-left:150px; border:4px solid #ccc; background:#fff; min-height:300px; }
#vtabs .uiTabs li { border:0; background:none; float:none; margin:0; }
#vtabs .uiTabs li a { color:#005CB1; float:none; font:12px/1.1em Arial, sans-serif; }
#vtabs .uiTabs li a span { float:none; display:block; padding:6px 0 6px 15px; text-decoration:underline; }
#vtabs .uiTabs li a, #vtabs .uiTabs li a:hover { background:none; padding:0; }
#vtabs .uiTabs li a:hover span { background:none; }
#vtabs .uiTabs li.active a, #vtabs .uiTabs li.active a:hover span { background:#ccc; }
#vtabs .uiTabs li.active span { background:#ccc; }
</style>

<div id="vtabs">
  <?php echo ui_tabs(array(
        array('Lorem',   '#uiTabbedView-vtabs1'),
        array('Ipsum',   '#uiTabbedView-vtabs2'),
        array('Opossum', '#uiTabbedView-vtabs3')
      ), 0, array('class' => 'uiTabs')) ?>
  <div class="uiTabbedBody">
    <div id="uiTabbedView-vtabs1">
      View one.
    </div>
    <div id="uiTabbedView-vtabs2" style="display:none">
      View two.
    </div>
    <div id="uiTabbedView-vtabs3" style="display:none">
      View three.
    </div>
  </div>
  <div style="clear:both;"></div>
</div>


<script>
(function(){

  var Y = Core.YUI,
      Ui = Core.Ui,
      tabs_demo;
  
  Core.ready(function() {
    tabs_demo.initialize();
  });

  tabs_demo = {

    initialize:function()
    {
      var tabbedView = new Core.Widgets.TabbedView(Ui.get('demo'), {
        'onTabClick': Y.bind(this.onTabFocus, this, 'dingo')
      });
      
      var tabbedView2 = new Core.Widgets.TabbedView(Ui.get('vtabs'), {
        'onTabClick': Y.bind(this.onTabClick, this),
        'onTabFocus': Y.bind(this.onTabFocus, this),
        'onTabBlur':  Y.bind(this.onTabBlur, this)
      });
    },
    
    onTabClick:function(sViewId)
    {
      App.log('onTabClick %s', sViewId);
    },
  
    onTabBlur:function(sViewId)
    {
      App.log('onTabBlur %s', sViewId);
    },
  
    onTabFocus:function(sViewId)
    {
      App.log('onTabFocus %s', sViewId);
      
      switch(sViewId)
      {
        case 'simple':
          break;
        case 'alert':
          alert('Tab activated!');
          break;
        case 'ajax':
          if (!this.ajaxPanel)
          {
            // from uiAjaxPanel Core doc page
            this.ajaxPanel = new Core.Ui.AjaxPanel('uiTabbedView-ajax',
            {
              bUseShading: true,
              post_url: "<?php echo url_for('ui/uiajaxpaneldemo1') ?>"
            });
            this.ajaxPanel.get();
          }
          break;
        
        case 'vtabs2':
          if (!this.ajaxPanel)
          {
            // from uiAjaxPanel Core doc page
            this.ajaxPanel = new Core.Ui.AjaxPanel('uiTabbedView-vtabs2',
            {
              bUseShading: true,
              post_url: "<?php echo url_for('ui/uiajaxpaneldemo1') ?>"
            });
            this.ajaxPanel.get();
          }
          break;
      }
    }
  };

})();
</script>
