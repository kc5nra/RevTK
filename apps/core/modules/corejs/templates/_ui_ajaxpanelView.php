<?php slot('inline_styles') ?>
.panel_box { margin:0 0 1em; border:4px solid #CCC; background:#eee; color:#444; padding:10px; width:400px; }
<?php end_slot() ?>

<h2>uiAjaxPanel</h2>

<p> Simple form submission in Ajax:</p>

	<div id="Demo1Panel" class="panel_box">
		<h3>Demo 1</h3>
		<div class="ajax" id="Demo1Panel-ajax">
			<?php include_component('corejs', 'demo1panel', array()) ?>
		</div>
	</div>

	<div id="Demo2Panel" class="panel_box">
		<h3>Demo 2</h3>
		<div class="ajax" id="Demo2Panel-ajax">
			<?php include_partial('corejs/demo2panel', array()) ?>
		</div>
	</div>


<script type="text/javascript">
(function() {

  var Y = Core.YUI,
      Dom = Y.util.Dom;

  App.ready = function()
  {
    Demo1Panel.initialize(Dom.get('Demo1Panel-ajax'));
    Demo2Panel.initialize(Dom.get('Demo2Panel-ajax'));
  };

	// Ajax FORM with the least setup, NO shading
	var Demo1Panel =
	{
		initialize: function(elContainer)
		{
			Core.log('Demo1Panel(%o)', elContainer);
			this.oAjaxPanel = new Core.Ui.AjaxPanel(elContainer);
		}
	};
	
	// Ajax FORM with onSubmitForm listener, adding data to the form data before post, use shading
	var Demo2Panel =
	{
		initialize:function(elContainer)
		{
			Core.log('Demo2Panel(%o)', elContainer);
			
			this.elContainer = elContainer;
			this.oAjaxPanel = new Core.Ui.AjaxPanel(elContainer, {
				bUseShading: true,
				events: {
					'onSubmitForm': Core.bind(this.onSubmitForm, this)
				}
			});
		},
		
		onSubmitForm:function(e)
		{
			Core.log('uiDemo2Panel.onSubmitForm(%o) Args: %o', e, arguments);

			var form = this.oAjaxPanel.getForm();
		  this.oAjaxPanel.setForm(form);
			this.oAjaxPanel.send({
				foo: true,
				extras: 'indeed'
			});

		  return false;
		}
	};
})();
</script>
