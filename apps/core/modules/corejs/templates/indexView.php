<?php if ($isIndex): ?>
  <h2>CoreJs Javascript Framework</h2>
  
  <p> The <strong>Ui Javascript Framework</strong> is a collection of classes designed
      to be reusable and generic, and focused on frontend user interface development.

  <div style="padding:5px 10px;background:red;color:#fff;">
    Note! All the CoreJs demos are currently broken. Code is being reverted to YUI2 (instead of YUI3),
    there are some new classes so I didn't want to revert also the CoreJs documentation to an
    older codebase. Please look at the working website pages (/apps/revtk/...) for working examples
    of the various classes such as FilterStd (stack display page) or AjaxPanel (Manage Flashcards).
  </div>

  <p> CoreJs framework currenty is based on Yahoo's <?php echo link_to('YUI2','http://developer.yahoo.com/yui/2/')?>  Javascript and CSS library.

<?php else: ?>
  <h2><?php echo implode('/', array('corejs', $corejs_cat, $corejs_subcat)) ?></h2>
  <?php include_partial($corejs_partial) ?>
<?php endif ?>

<?php slot('sidebar') ?>
    <div id="sidebar2">
      <div class="padding">
        <h3>Main</h3>
        <ol>
          <li><?php echo link_to('Go back', '@homepage') ?></li>
        </ol>
      </div>    
    </div>
  
    <div id="sidebar">
      <div class="padding">
<?php
  $menu= array(
    'core' => array(
      'core'        => 'Core'
    ),
    'ui' => array(
      'ajaxrequest' => 'AjaxRequest',
      'ajaxpanel'   => 'AjaxPanel',
      'ajaxtable'   => 'AjaxTable',
      'eventdelegator' => 'EventDelegator',
      'shadelayer'  => 'ShadeLayer'
    ),
    'widgets' => array(
      'boxrounded'  => 'BoxRounded',
      'ibutton'     => 'IButton',
      'datafilter'  => 'DataFilter',
      'filterstd'   => 'FilterStd',
      'pager'       => 'Pager',
      'progressbar' => 'Progressbar',
      'selectiontable' => 'SelectionTable',
      'tabbedview'  => 'TabbedView',
      'tabular'     => 'Tabular',
      'window'      => 'Window'
    )
  ); 
        
  foreach ($menu as $cat => $items)
  {
    echo '<h3>'.$cat.'</h3>';
    echo '<ul>';
    foreach ($items as $subcat => $label) {
      echo '<li>'.link_to($label, "corejs/index?cat=$cat&subcat=$subcat").'</li>';
    }
    echo '</ul>';
  }
?>        
      </div>    
    </div>
<?php end_slot() ?>
