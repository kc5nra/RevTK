/* "reading" page */

Array.prototype.inArray = function (value) {
	var i;
	for (i=0; i < this.length; i++) {
		if (this[i] === value) {
			return true;
		}
	}
	return false;
};


/* sweettitles */

var sweetTitles = {
	xCord : 0,								// @Number: x pixel value of current cursor position
	yCord : 0,								// @Number: y pixel value of current cursor position
	tipElements : ['a'],					// @Array: Allowable elements that can have the toolTip
	obj : Object,							// @Element: That of which you're hovering over
	tip : Object,							// @Element: The actual toolTip itself
	active : 0,								// @Number: 0: Not Active || 1: Active
	init : function() {
		var i,j;
		this.tip = document.createElement('div');
		this.tip.id = 'toolTip';
		document.getElementsByTagName('body')[0].appendChild(this.tip);
		this.tip.style.top = '0';
		this.tip.style.visibility = 'hidden';
		var tipLen = this.tipElements.length;
		for ( i=0; i<tipLen; i++ ) {
			var current = document.getElementsByTagName(this.tipElements[i]);
			var curLen = current.length;
			for ( j=0; j<curLen; j++ ) {
				if (current[j].className && current[j].className=='j') {
					dom.addEvent(current[j],'mouseover', this.tipOver.bindAsEventListener(this));
					dom.addEvent(current[j],'mouseout',this.tipOut);
					current[j].setAttribute('tip',current[j].title);
					current[j].removeAttribute('title');
				}
			}
		}
	},
	updateXY : function(e) {
		if ( document.captureEvents ) {
			sweetTitles.xCord = e.pageX;
			sweetTitles.yCord = e.pageY;
		} else if ( window.event.clientX ) {
			sweetTitles.xCord = window.event.clientX+document.documentElement.scrollLeft;
			sweetTitles.yCord = window.event.clientY+document.documentElement.scrollTop;
		}
	},
	tipOut: function() {
		if ( window.tID ) {
			clearTimeout(tID);
		}
		if ( window.opacityID ) {
			clearTimeout(opacityID);
		}
		sweetTitles.tip.style.visibility = 'hidden';
	},
	checkNode : function()
	{
		var trueObj = this.obj;
		if ( this.tipElements.inArray(trueObj.nodeName.toLowerCase()) ) {
			return trueObj;
		} else {
			return trueObj.parentNode;
		}
	},
	tipOver : function(e)
	{
		var elem = Event.element(e);
		this.obj = elem;
		tID = window.setTimeout("sweetTitles.tipShow()",500);
//		sweetTitles.updateXY(e);
	},
	tipShow : function() {
		// faB++
		function findPosition(oLink)
		{
			if (oLink.offsetParent){
				for (var posX = 0, posY = 0; oLink.offsetParent; oLink = oLink.offsetParent){
					posX += oLink.offsetLeft;
					posY += oLink.offsetTop;
				}
				return [posX, posY];
			}else{
				return [oLink.x, oLink.y];
			}
		}
		var mpos = findPosition(sweetTitles.obj);
	    sweetTitles.xCord = mpos[0];
	    sweetTitles.yCord = mpos[1];
	    //faB

		var scrX = Number(this.xCord);
		var scrY = Number(this.yCord);
		var tp = parseInt(scrY+10) + this.obj.offsetHeight;
		var lt = parseInt(scrX+5);
		var anch = this.checkNode();
		//faB -- removed url from tooltip
		this.tip.innerHTML = "<p>"+anch.getAttribute('tip')+"</p>";
		if ( parseInt(document.documentElement.clientWidth+document.documentElement.scrollLeft) < parseInt(this.tip.offsetWidth+lt) ) {
			this.tip.style.left = parseInt(lt-(this.tip.offsetWidth+10))+'px';
		} else {
			this.tip.style.left = lt+'px';
		}
		if ( parseInt(document.documentElement.clientHeight+document.documentElement.scrollTop) < parseInt(this.tip.offsetHeight+tp) ) {
			this.tip.style.top = parseInt(tp-(this.tip.offsetHeight+10))+'px';
		} else {
			this.tip.style.top = tp+'px';
		}
		this.tip.style.visibility = 'visible';
		this.tip.style.opacity = '.1';
		this.tipFade(10);
	},
	tipFade: function(opac) {
		var passed = parseInt(opac);
		var newOpac = parseInt(passed+10);
		if ( newOpac < 90 ) {
			this.tip.style.opacity = '.'+newOpac;
			this.tip.style.filter = "alpha(opacity:"+newOpac+")";
			opacityID = window.setTimeout("sweetTitles.tipFade('"+newOpac+"')",20);
		}
		else {
		//faB -- fade ends at full opacity instead of 80%.
			this.tip.style.opacity = '';
			this.tip.style.filter = '';
		}
	}
};

function clearit()
{
	var ta = document.getElementById('jtextarea');
	ta.value='';
	ta.focus();
	return false;
}

var App = {
	init:function()
	{
		sweetTitles.init();
		
		$('toggle-form').onclick = this.toggle.bindAsEventListener(this);
	},
	toggle:function()
	{
		$('introduction').style.display = 'none';
		AppUI.toggleDisplay('form');
		return false;
	}
}

dom.addEvent(window,'load', App.init.bind(App));
