/**
 * Manage Flashcards
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

var ManageFlashcards =
{
  initialize:function()
  {
    this.view = this.initView('manage-view');
  },

  initView:function(viewId)
  {
    var me = this;
    this.viewDiv = $(viewId).down('.ajax');
    this.viewPanel = new uiAjaxPanel(this.viewDiv, {
      bUseShading: false,
      form:        'form.main-form',
      events: {
        'onSubmitForm':     onSubmitForm,
        'onContentInit':    onContentInit,
        'onContentDestroy': onContentDestroy
      }
    });

    function onContentInit()
    {
      var tableDiv = me.viewDiv.down('.selection-table') || false;
      if (tableDiv)
      {
        // clear checkboxes in case of page refresh
        tableDiv.select('input.checkbox').each(function(cb){ cb.checked = false; });
        
        me.selectionTable = new uiSelectionTable(tableDiv);
        me.selectionTableDiv = tableDiv;
      }
    }
    
    function onContentDestroy()
    {
      if (me.selectionTable) {
        me.selectionTable.destroy();
      }
    }
    
    function onSubmitForm(oEvent)
    {
      var formData = me.viewPanel.getForm().serialize(true);

      if (me.selectionTable)
      {
        var tblData = me.selectionTableDiv.down('form').serialize(true);
        Object.extend(formData, tblData);
      }

      me.viewPanel.post(formData);
    }
  },

  load:function(element, params)
  {
    this.viewPanel.post(params);
    return false;
  }
};

Event.observe(window, 'load', function() { ManageFlashcards.initialize(); });
