<?php use_helper('Widgets') ?>

<h2>Helper</h2>

<p>Example use of the php helper</p>

<?php pre_start() ?>
ui_filter_std(
  $label,
  array(
    array($name, $internal_uri, $options),  /* same as link_to() */
    array($name, $internal_uri, $options)
    //...
  ),
  array(
    'id' => 'filter1', /* options as for tag helper */
    'active' => 1      // index of active option
  )
);
<?php pre_end() ?>

<p>Using ui_filter_std() helper</p>

<?php echo ui_filter_std('Label',
      array(
        array('One', '@homepage', array('class' => 'uiFilterStd-one')),
        array('Two', 'http://www.google.com', array('class' => 'uiFilterStd-two'))
      ), array('id' => 'demo1', 'active' => 1)) ?>

<h2>Generated markup:</h2>

<?php pre_start('html') ?>
<?php echo ui_filter_std('Label', array(array('One', '@homepage'), array('Two', 'http://www.google.com')), array('id' => 'demo1', 'active' => 1)) ?>
<?php pre_end() ?>

<script type="text/javascript">
Core.ready(function(){
  var filt = new Core.Widgets.FilterStd('demo1', {
    onSwitch: function(id) {
      Core.log("Clicked tab id '%s'", id);
    }
  });
});
</script>