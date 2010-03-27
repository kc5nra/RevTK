/**
 * EditStoryComponent client side.
 * 
 * Methods:
 * 
 *   isEdit()     Returns true if currently in edit mode.
 *   doCancel()   Force return to view mode and cancel changes.
 * 
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

var EditStoryComponent = Class.create();
EditStoryComponent.prototype =
{
  initialize:function(elContainer)
  {
    uiConsole.log('EditStoryComponent.initialize()');

    this.evtCache = new uiEventCache();
    
    // set defaults
    this.bEdit = false;
    this.sBackupStory = '';

    var el = $('sv-textarea');
    this.evtCache.addEvent(el, 'click', this.evEdit.bind(this));
    this.evtCache.addEvent(el, 'mouseover', this.evHover.bind(this));
    this.evtCache.addEvent(el, 'mouseout', this.evHover.bind(this));  

    this.evtCache.addEvent($('storyedit_cancel'), 'click', this.evCancel.bind(this));
  },
  
  destroy:function()
  {
    this.evtCache.destroy();
  },
  
  /**
   * Enter edit mode.
   * 
   */
  evEdit:function(oEvent)
  {
    this.editStory();
  },

  /**
   * Returns true if currently in edit mode.
   * 
   * @return boolean
   */
  isEdit:function()
  {
    return this.bEdit;
  },

  /**
   * Edit Story or Edit a copy of another user's story.
   * 
   * @param {Object} sCopyStory   The "copy" story feature will set this to the copied story text.
   */  
  editStory:function(sCopyStory)  
  {
    this.sBackupStory = $('frmStory').value;

    // edit a new story, cancel will restore the previous one
    if (sCopyStory) {
      $('frmStory').value = sCopyStory
    }

    $('storyview').style.display = 'none';
    $('storyedit').style.display = 'block';
    
    var  elTextArea = $('frmStory');
    this.setCaretToEnd(elTextArea);
    
    this.bEdit = true;
  },

  evHover:function(oEvent)
  {
    var type = oEvent.type;
    var element = oEvent.element();
    if (type=='mouseover') {
      element.addClassName('hover');
    }
    else {
      element.removeClassName('hover');
    }
  },
  
  evCancel:function(oEvent)
  {
    this.doCancel();
  },

  /**
   * Cancel any changes and switch back to view mode
   * 
   */
  doCancel:function()
  {
    $('storyedit').style.display = 'none';
    $('frmStory').value = this.sBackupStory;
    $('storyview').style.display = 'block';
    
    this.bEdit = false;
  },
  
  /**
   * Cross-browser move caret to end of input field
   */
  setCaretToEnd:function(element)
  {
    if (element.createTextRange) {
      var range = element.createTextRange();
      range.collapse(false);
      range.select();
    }
    else if (element.setSelectionRange) {
      element.focus();
      var length = element.value.length;
      element.setSelectionRange(length, length);
    }
  }
}
