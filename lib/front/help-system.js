/*! HelpSystem (c)2008 Fabrice Denis - http://kanji.koohii.com */
/**
 * Guides user through the features on the page, by displaying
 * a sequence of "bubble" boxes next to the feature, and allows
 * advancing forward and backward through the sequence of tips.
 * 
 * Todo
 * - Refactor for YUI3
 * - Document more (here)
 * - Create test page
 * 
 * @author  Fabrice Denis
 * @date    Jan 2008
 */

var HelpSystem = Class.create();
HelpSystem.prototype =
{
	// which side of html element to position around, the arrow will point towards the element (opposite side)
	// note! the indexes also match the order of the arrow tips in the arrow gif image
	HS_POS_RIGHT:1,
	HS_POS_BOTTOM:2,
	HS_POS_TOP:3,
	HS_POS_MIDDLE:4,	// position on top of element, centered horizontally and a little above the middle

	HS_ARROW_WIDTH:19,	// size of arrow tip place on one of the sides of the bubble
	HS_ARROW_HEIGHT:19,

	default_pos:{x:106, y:163},	// default absolute position on page if 1st step doesn't specify position
	default_width:241,

	initialize:function()
	{
		// template can be passed from the dynamically loaded help file
		if (this.htmlTemplate)
		{
			var div = document.createElement('div');
			var root = $('ie') || document.getElementsByTagName('body')[0];
			div.innerHTML = this.htmlTemplate;
			root.appendChild(div);
		}

		this.layer = $('hs-layer');
		this.arrow = this.layer.getElementsByTagName('div')[0];

		if (!this.layer){
			alert('HelpSystem:: layer not found!');
			return;
		}

		this.layer.style.position = 'absolute';
		this.layer.style.zIndex = '100';
		this.layer.style.width = this.default_width+'px';

		var tfoot = this.layer.getElementsByTagName('tfoot')[0];
		this.backLink = tfoot.getElementsByTagName('a')[0];
		this.nextLink = tfoot.getElementsByTagName('a')[1];
		this.stepSpan = this.layer.getElementsByTagName('span')[0];
		// close link appears at the last step (not same as the close button image)
		this.closeLink = tfoot.getElementsByTagName('a')[2];
		
		this.steps = [];
	},
	
	add:function(o)
	{
		this.steps.push(o);
	},
	
	start:function()
	{
		this.layer.style.display = 'block';
		this.layer.onclick = this.eventHandler.bindAsEventListener(this);

		var i;
		for (i=1; i<this.steps.length;i++)
		{
			if (!this.steps[i].ttl){
				this.steps[i].ttl = this.steps[i-1].ttl;
			}
			if (!this.steps[i].pos && this.steps[i-1].pos)
			{
				this.steps[i].pos = this.steps[i-1].pos;
				this.steps[i].ele = this.steps[i-1].ele;
			}
		}

		this.oldpos = false;
		this.step = 0;
		this.show();
	},
	
	show:function()
	{
		var o = this.steps[this.step];

		// hide to speed up refresh
		this.layer.style.display = 'none';

		// back/forward links
		this.backLink.style.display = (this.step>0) ? '':'none';
		this.nextLink.style.display = (this.step<this.steps.length-1) ? '':'none';
		this.closeLink.style.display = (this.step<this.steps.length-1) ? '':'block';

		// current step
		this.stepSpan.innerHTML = (this.step+1)+'/'+this.steps.length;

		var ele = null;
		var pos = 0;

		for(param in o)
		{
			switch(param){
				case 'ttl':
					this.layer.getElementsByTagName('th')[0].innerHTML = o[param];
					break;
				case 'msg':
					this.layer.getElementsByTagName('tbody')[0].getElementsByTagName('td')[0].innerHTML = o[param];
					break;
				case 'ele':
					ele = o[param](); // function returns page element
					break;
				case 'pos':
					pos = o[param];
					break;
			}
		}
		
		// position help bubble near element if provided, otherwise stay in the same place
		// if this is the first step, and no element is provided, use default position
		var newpos = false;
		
		// if element is provided, position around element
		if (ele || this.step==0)
		{
			var elepos = ele ? dom.findPosition(ele) : false;
			var arrowpos, layerpos;
			switch(pos){
				case this.HS_POS_RIGHT:
					newpos = {x:elepos[0]+ele.offsetWidth, y:elepos[1]+ele.offsetHeight/2};
					arrowpos = {x:-19, y:29-this.HS_ARROW_HEIGHT/2} //{x:newpos.x, y:newpos.y-this.HS_ARROW_HEIGHT/2};
					layerpos = {x:newpos.x+this.HS_ARROW_WIDTH, y:newpos.y-29};
					break;
				case this.HS_POS_BOTTOM:
					newpos = {x:elepos[0]+ele.offsetWidth/2, y:elepos[1]+ele.offsetHeight};
					arrowpos = {x:this.default_width/2-this.HS_ARROW_WIDTH/2, y:-19};
					layerpos = {x:newpos.x-this.default_width/2, y:newpos.y+this.HS_ARROW_HEIGHT};
					break;
				case this.HS_POS_TOP:
					newpos = {x:elepos[0]+ele.offsetWidth/2, y:elepos[1]};
					arrowpos = {x:this.default_width/2-this.HS_ARROW_WIDTH/2, y:0};
					layerpos = {x:newpos.x-this.default_width/2, y:newpos.y-this.HS_ARROW_HEIGHT};
					break;
				case this.HS_POS_MIDDLE:
					newpos = {x:elepos[0]+ele.offsetWidth/2, y:elepos[1]+ele.offsetHeight/3};
					arrowpos = false;
					layerpos = {x:newpos.x-this.default_width/2, y:newpos.y};
					break;
				default:
					if (this.step==0) {
						arrowpos = false;
						layerpos = this.default_pos;
					}
					break;
			}

			// timeout to get proper dimensions after layer is displayed
			var that = this;
			window.setTimeout(function() {
				
				// adjust layer if it goes outside of the main content
				var mainDiv = $('mainright');
				if (mainDiv){
					var mainpos = dom.findPosition(mainDiv);
					var difX = (layerpos.x+that.default_width) - (mainpos[0]+mainDiv.offsetWidth);
					if (difX > 0){
//						console.log(difX);
						layerpos.x-=(difX + 8);
						if (pos==that.HS_POS_TOP || pos==that.HS_POS_BOTTOM){
							arrowpos.x+=difX;
						}
					}
				}
				
				// adjuts POS_TOP now that we have the layer's height
				if (pos==that.HS_POS_TOP){
					var divheight = that.layer.offsetHeight-that.HS_ARROW_HEIGHT*2;
					layerpos.y -= divheight;
					arrowpos.y += divheight;
				}
				
				if (arrowpos){
					arrowpos.x += that.HS_ARROW_WIDTH; // adjust pos due to padding on layer
					arrowpos.y += that.HS_ARROW_HEIGHT;
					that.positionDiv(that.arrow, arrowpos.x, arrowpos.y);
					that.arrow.style.backgroundPosition = -((pos-1)*that.HS_ARROW_WIDTH)+'px 0';
					that.arrow.style.display='block';
				}else{
					// default position doesn't use arrow tip
					that.arrow.style.display='none';
				}
				// adjust position because of padding on the sides (for IE6, surprise!)
				layerpos.x -= that.HS_ARROW_WIDTH;
				layerpos.y -= that.HS_ARROW_HEIGHT;
				that.layer.style.padding = that.HS_ARROW_HEIGHT+'px '+that.HS_ARROW_WIDTH+'px';
				that.positionDiv(that.layer, layerpos.x, layerpos.y);
			}, 0);
		}

		if (!this.oldpos || newpos.x!=this.oldpos.x || newpos.y!=this.oldpos.y)
		{
			this.startFadeIn();
			this.oldpos = newpos;
		}
		else {
			this.layer.style.display = 'block';
		}
	},

	back:function()
	{
		if (this.step>0)
		{
			this.step--;
			this.show();
		}
		
	},

	next:function()
	{
		if (this.step<this.steps.length-1)
		{
			this.step++;
			this.show();
		}
	},

	end:function()
	{
		this.layer.onclick = null;
		this.layer.style.display = 'none';
	},
	
	eventHandler:function(e)
	{
		var elem = Event.element(e);
		if (/hs-(\w+)/.test(elem.className))
		{
			if (this[RegExp.$1])
			{
				this[RegExp.$1]();
				Event.stop(e);
				return false;
			}
		}
	},
	
	/* helper functions */
	
	positionDiv:function(div, x, y)
	{
		div.style.left = Math.floor(x)+'px';
		div.style.top = Math.floor(y)+'px';
	},
	
	FADEIN_SPEED: 300,
	FADEIN_FRAMES: 16,

	startFadeIn:function()
	{
		var animFrame = 0;
		var animDelay =  Math.floor(this.FADEIN_SPEED/this.FADEIN_FRAMES);
		var that = this;
		function animate()
		{
			var opacity = animFrame<that.FADEIN_FRAMES ? Math.sin((animFrame*90/that.FADEIN_FRAMES)*Math.PI/180) : 1;
			AppUI.setOpacity(that.layer, opacity);
			animFrame++;
			if (animFrame<that.FADEIN_FRAMES){
				if (that.timeout){
					window.clearTimeout(that.timeout);
				}
				that.timeout = window.setTimeout(animate, animDelay);
			}
		}
		animate();
		this.layer.style.display = 'block';
	}
	
};
