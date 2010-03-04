/**
 * rkEditStoryWindow
 * 
 * Edit a story with the EditStoryComponent from inside an overlay window.
 * 
 * Methods:
 * 
 *   initialize()    Set initial position, must call show() to make the window visible.
 *   show()          Use show() and hide() so that the last position (from dragging) is kept.
 *   hide()
 *   close()         Close the window and destroy all events.
 * 
 * Options:
 * 
 *   left, top       Initial window position
 *   
 * 
 * 
 * @package  RevTK
 * @author   Fabrice Denis
 */

var rkEditStoryWindow = Class.create();
rkEditStoryWindow.prototype =
{
	/**
	 * 
	 * 
	 * @param {integer} nFramenum  Kanji id
	 * @param {Object}  options
	 */
	initialize:function(nFramenum, options)
	{
		this.curFramenum = false;

		// edit story uiWindow
		this.elWindow = $('EditStoryWindow');
		this.oWindow = new uiWindow(this.elWindow, {
			left:     options.left,
			top:      options.top,
			width:    512,  // 496 + 16 borders
			opacity:  0.5,
			draggable: true,
			events: {
				onWindowClose: this.onWindowClose.bind(this)
			}
		});

		// ajax panel
		this.elWindowBody = this.oWindow.getBodyElement();
		this.ajaxPanel = new uiAjaxPanel(this.elWindowBody,
		{
			post_url: options.editstory_url,
			events:
			{
				onContentInit:    this.onContentInit.bind(this),
				onContentDestroy: this.onContentDestroy.bind(this)
			}
		});
		
		// show first for ajax loading indicator positioning
		this.show();

		// start ajax load
		this.loadFramenum(nFramenum);
	},

	destroy:function()
	{
		this.hide();
		
		if (this.editStory)
		{
			this.editStory.destroy();
			this.editStory = null;
		}

		if (this.ajaxPanel)
		{
			this.ajaxPanel.destroy();
			this.ajaxPanel = null;
		}
	},

	loadFramenum:function(nFramenum)
	{
		// Don't load the same framenum twice in a row
		if (this.curFramenum && this.curFramenum == nFramenum)
		{
			return;
		}
		
		// set body dimensions before content is loaded
		this.elWindowBody.update('<div style="width:496px;height:190px;background:#fff;"></div>');

		this.ajaxPanel.get({framenum: nFramenum, reviewMode:true});
	},

	onContentInit:function()
	{
		if ($('my-story'))
		{
			this.curFramenum = parseInt(this.elWindowBody.getElementsByTagName('form')[0].elements['framenum'].value);
//alert(this.curFramenum);
			this.editStory = new EditStoryComponent(this.elWindowBody);
		}
	},
	
	onContentDestroy:function()
	{
		if (this.editStory)
		{
			this.editStory.destroy();
			this.editStory = null;
		}
	},

	show:function()
	{
		this.oWindow.show();
		this.bVisible = true;
	},
	
	hide:function()
	{
		if (this.editStory.isEdit())
		{
			this.editStory.doCancel();
		}

		this.oWindow.hide();
		this.bVisible = false;
	},
	
	isVisible:function()
	{
		return this.bVisible;
	},
	
	close:function()
	{
		// notify uiWindow close event
		this.oWindow.close();
	},
	
	/**
	 * uiWindow listener
	 * 
	 * Don't destroy the uiWindow, just hide it
	 * 
	 */
	onWindowClose:function()
	{
		this.hide();
	}
};
