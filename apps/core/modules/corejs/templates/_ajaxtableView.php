<?php
/**
 * ajaxtable demo - partial for corejs/ui/ajaxtable demo.
 * 
 * Demonstrates core/ui/ajaxtable support for rows per page links,
 * pager links, and column head sort links; all routed through an
 * ajax panel instance.
 * 
 * @author  Fabrice Denis
 */
use_helper('Form', 'Widgets');
?> 

<?php /* the form must enclose the whole table for the selection table checkbox states */ ?>

<?php echo form_tag('corejs/'.(isset($selection) ? 'selectiontable' : 'ajaxtable'), array('method' => 'post')) ?>
  <?php echo input_hidden_tag('hidden_form_element', 'foobar') ?>

<div class="ajax-table-paging">
  <?php echo ui_filter_std('Rows:',
    array(
      array('10', '/foo/bar?rows=10&sort=framenum&order=0', array('class' => 'table-page')),
      array('20', '/foo/bar?rows=20&sort=framenum&order=0', array('class' => 'table-page')),
      array('50', '/foo/bar?rows=30&sort=framenum&order=0', array('class' => 'table-page'))
    ),
    array(
      'style'  => 'float:left;',
      'active' => 1  // index of active option
    )
  );
  ?>
  
  <ul class="uiPager" style="margin:0 0 10px">
    <li class="prev disabled">&laquo; Previous</li>
    <li class="active">1</li>
    <li><a href="?rows=20&amp;sort=framenum&amp;order=0&amp;page=2" class="table-page">2</a></li>
    <li><a href="?rows=20&amp;sort=framenum&amp;order=0&amp;page=3" class="table-page">3</a></li>
    <li class="next"><a href="?rows=20&amp;sort=framenum&amp;order=0&amp;page=2" class="table-page">Next &raquo;</a></li>
  </ul>
  <div class="clear"></div>
</div>

<?php #demonstrate the table column sort in Core.Ui.AjaxTable ?>
<table cellspacing="0" class="tabular">
  <thead>
  <tr>
    <th width="5%"><a href="?sort=framenum&amp;order=1" class="active table-sort sortasc">Framenum</a></th>
    <th width="19%"><a href="?sort=keyword&amp;order=0" class="table-sort">Keyword</a></th>
    <th width="8%"><a href="?sort=successcount&amp;order=0" class="table-sort">Pass</a></th>
    <th width="8%"><a href="?sort=failurecount&amp;order=0" class="table-sort">Fail</a></th>
    <th width="15%"><a href="?sort=ts_lastreview&amp;order=0" class="table-sort">Last Review</a></th>
    <?php if (isset($selection)): ?>
      <th width="1%"><input type="checkbox" class="chkAll" value="all" name="chkAll"/></th>
    <?php endif ?>
  </tr>
</thead>
<tbody>
  <tr class="" id="b1bea8608bf74a8351f7a946e926eece-1">
    <td class="center">1</td>
    <td class="keyword"><a href="/study/kanji/1">one</a></td>
    <td class="bold center">8</td>
    <td class="center red">4</td>
    <td class="center nowrap">1 Nov 2009</td>
    <?php if (isset($selection)): ?>
      <td class="center">
        <input type="hidden" value="0" name="sel_rf-1"/>
        <input type="checkbox" name="chk_rf-1" class="checkbox"/>
      </td>
    <?php endif ?>
  </tr>
  <tr class="foobar" id="bec633358a17e8b5828cbd8cb470afd9-5">
    <td class="center">5</td>
    <td class="keyword"><a href="/study/kanji/5">five</a></td>
    <td class="bold center">0</td>
    <td class="center red">2</td>
    <td class="center nowrap">2 Nov 2009</td>
    <?php if (isset($selection)): ?>
      <td class="center">
        <input type="hidden" value="0" name="sel_rf-2"/>
        <input type="checkbox" name="chk_rf-2" class="checkbox"/>
      </td>
    <?php endif ?>
  </tr>
  <tr class="" id="8814ef36356005517478b649f62c77b2-6">
    <td class="center">6</td>
    <td class="keyword"><a href="/study/kanji/6">six</a></td>
    <td class="bold center">2</td>
    <td class="center red">1</td>
    <td class="center nowrap">1 Nov 2009</td>
    <?php if (isset($selection)): ?>
      <td class="center">
        <input type="hidden" value="0" name="sel_rf-3"/>
        <input type="checkbox" name="chk_rf-3" class="checkbox"/>
      </td>
    <?php endif ?>
  </tr>
</tbody>
</table>

</form>

<?php if ($_request->getMethod() === coreRequest::POST): ?>
  <?php pre_start('info'); print_r($_params->getAll()); pre_end() ?>
<?php endif ?>
