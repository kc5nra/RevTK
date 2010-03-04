/**
 * Extends AjaxTablr and adds the ability to select rows.
 * 
 * 
 * 
 * 
 * @augments  Core.Ui.AjaxTable
 * 
 * @author    Fabrice Denis
 * @version   2009/11/30 (yui3)
 */
(function() {

  var Y   = Core.YUI,
      Dom = Core.Dom,
      Ui  = Core.Ui;

  Core.Widgets.SelectionTable = Core.extend(Core.Ui.AjaxTable,
  {
    selection: {},

    /**
     * Constructor.
     * 
     * @param {String|Object} container   Container id, HTMLElement or YUI Node  
     */
    init: function(container)
    {
      this._super = Core.Widgets.SelectionTable.superclass;

      // init AjaxTable
      this._super.init.call(this, container);

      this.selection = {};

      this.evtDel.on("checkbox", this.onCheckBox, this);
      this.evtDel.on("chkAll", this.onCheckAll, this);
    },
    
    destroy: function()
    {
      this._super.destroy.call(this);
    },
    
    /**
     * EventDelegator event.
     */
    onCheckBox: function(e, el)
    {
      var row = Dom.getParent(el, "tr"),
          inputs = Dom.getParent(el).getElementsByTagName("input");
      this.setSelection(row, inputs[0], el.checked);
      return true;
    },
    
    /**
     * EventDelegator event.
     */
    onCheckAll: function(e, el)
    {
      var i,
          check = el.checked,
          rows  = this.container.getElementsByTagName("table")[0].tBodies[0].getElementsByTagName("tr");
     
      for (i = 0; i < rows.length; i++)
      {
        var tr = rows[i],
            inputs = tr.getElementsByTagName("input");
        if (inputs[1].checked !== check) {
          inputs[1].checked = check;
          this.setSelection(tr, inputs[0], check);
        }
      }
      
      return true;
    },

    /**
     * EventDelegator event.
     */
    onClickRow: function(e, el)
    {
      var elChk = Y.one(el).one("input.checkbox");
      if (elChk) {
        elChk.simulate("click");
        return false;
      }
      return true;
    },
    
    setSelection: function(row, input, check)
    {
      // set value
      input.value = check ? "1" : "0";
      // set highlight
      Y.DOM[check ? "addClass" : "removeClass"](row, "selected");
    }
  });

})();