/**
 * rkLeitnerSVG
 * 
 * Display of the user's review progress, as stacks of flashcards.
 * We use the raphael vector graphics library which create an abstraction
 * to SVG on modern browsers and VML on IE6.
 * 
 * @author  Fabrice Denis
 * @package RevTK
 */

var rkLeitnerSVG = Class.create();
rkLeitnerSVG.prototype =
{
  BAR_WIDTH:      31,
  BAR_DEPTH:      13,
  BAR_SPACING:    60,
  BAR_MAXHEIGHT:  187,
  BAR_MINHEIGHT:   12,
  BAR_ZEROHEIGHT: 6,
  
  BOX_WIDTH:      80,
  BOX_DEPTH:      32,

  MODE_SIMPLE:    0,
  MODE_FULL:      1,
  MODE_SIMPLE_BOXES: 5,

  chartdata:      null,
  viewmode:        null,
  viewsets:        null,

  /**
   * 
   * 
   * 
   * @param {Object} elSVGComponent   Parent element of .svg-inner-div
   * @param {Object} data
   */
  initialize:function(elSVGComponent, data)
  {
    this.isIE = !!document.getElementById('ie');
    this.elCanvas = elSVGComponent.down('.svg-inner-div');
    this.setPaperSize(this.elCanvas);
    this.setupCanvas(this.elCanvas);
    
    /* Unused
    Event.observe(window, 'resize', this.evWindowResize.bind(this));
    */
    
    this.viewsets = [null, null];

    this.setViewMode(this.MODE_SIMPLE);
    this.setChartData(data);
    this.drawChart();
  },

  setChartData:function(data)
  {
    this.chartdata = data;
  },
  
  /**
   * Change the view mode,
   * create view set for that view mode, if not done yet,
   * returns true if a viewset was created.
   * 
   * @param {Object} mode
   */
  setViewMode:function(mode)
  {
    this.viewmode = mode;
    if (!this.viewsets[mode])
    {
      this.viewsets[mode] = this.pp.set();
      return true;
    }
    return false;
  },
  
  clearAll:function()
  {
    this.elCanvas.innerHTML = '';
  },
  
  /**
   * Switch between view sets (like draw lists),
   * if the alternate view was not drawn yet, draw it once.
   * 
   * @param {Object} mode
   */
  toggleViewMode:function(mode)
  {
  //  uiConsole.log('toggle mode ' + mode);

    this.getViewSet().hide();

    // build alternate view, once
    if (this.setViewMode(mode))
    {
      this.drawChart();
    }
    
    curset = this.getViewSet();
  //  curset.attr({opacity: 0}).show().animate({opacity: 1}, 200);
    curset.show();
  },

  getViewSet:function()
  {
    return this.viewsets[this.viewmode];
  },
  
  viewSetPush:function(elements)
  {
    var i, set = this.getViewSet();
    if (elements instanceof Array)
    {
      for (i = 0; i < elements.length; i++)
      {
        set.push(elements[i]);
      }
    }
    else
    {
      set.push(elements);
    }
  },
  
  /**
   * Draw chart according to current viewmode and
   * add all elements to the current view's set.
   * 
   */
  drawChart:function()
  {
    var dispboxes = this.makeDispData();
    this.drawCardBoxes(dispboxes);
  },

  /**
   * Draw the card boxes.
   * 
   */
  drawCardBoxes:function(dispboxes)
  {
    for (i = 0; i < dispboxes.length; i++)
    {
      this.drawBox(dispboxes[i]);
    }
  },

  /* Unused
  evWindowResize:function(oEvent)
  {
    this.setPaperSize();
    this.pp.setSize(this.pw, this.ph);
  },
  */
  
  setPaperSize:function(elCanvas)
  {
    this.pw = elCanvas.offsetWidth;
    this.ph = elCanvas.offsetHeight;
  },

  setupCanvas:function(elCanvas)
  {
    this.pp = Raphael(elCanvas, this.pw, this.ph);
  //  new RaphaelFix(elCanvas, this.pp);
  },

  /**
   * Convert input chartdata to view data.
   * Create 5 or 8 card boxes depending on the view mode.
   * 
   * Return value:
   * [
   *   // box
   *   {
   *     index: <box index>
   *     stacks:
   *     [
   *       // stack
   *       {
   *         value:  <num of cards>
   *         type:   <"failed", "untested", "expired", "fresh">
   *         height: <calculated height based on maximum stack value, for rendering>
   *       }
   *       // second stack
   *       ...
   *     ]
   *   }
   *   // ...
   * ]
   * 
   * 
   * @return  array
   */
  makeDispData:function()
  {
    var boxes = this.chartdata.boxes;
    var dispboxes = [];
    var dispstacks = [];
    
    for (i = 0; i < boxes.length; i++)
    {
      var stacks = boxes[i];
      if (i >= this.MODE_SIMPLE_BOXES && this.viewmode==this.MODE_SIMPLE)
      {
        dispboxes[this.MODE_SIMPLE_BOXES - 1].stacks[0].value += stacks[0].value;
        dispboxes[this.MODE_SIMPLE_BOXES - 1].stacks[1].value += stacks[1].value;
      }
      else
      {
        dispboxes[i] = { index:i, stacks:[] };
        
        dispboxes[i].stacks[0] = {
          value: stacks[0].value,
          type:  stacks[0].type
        };
        dispboxes[i].stacks[1] = {
          value: stacks[1].value,
          type:  stacks[1].type
        };
        
        dispstacks.push(dispboxes[i].stacks[0]);
        dispstacks.push(dispboxes[i].stacks[1]);
      }
    }

    this.calcHeights(dispstacks);

    // set the footer text
    for (i = 0; i < dispboxes.length; i++)
    {
      var total = dispboxes[i].stacks[0].value + dispboxes[i].stacks[1].value;
      dispboxes[i].footerText = total + ' Cards';
    }
    
    this.setStackLinks(dispstacks);

    //  console.log(dispstacks);
    return dispboxes;
  },
  
  /**
   * Add urls to the clickable stacks with request parameters
   * for the review module.
   * 
   * @param {Object} dispstacks
   */
  setStackLinks:function(dispstacks)
  {
    // set the links
    for (i = 0; i < dispstacks.length; i++)
    {
      var stack = dispstacks[i];
      
      // empty stacks have no interaction
      if (stack.value <= 0)
      {
        continue;
      }
      
      switch (stack.type)
      {
        case 'failed':
          stack.href = this.chartdata.url_study;
          stack.title = stack.value + ' kanji to (re-)learn';
          break;
        case 'untested':
          stack.href = this.chartdata.url_new;
          stack.title = stack.value + ' new kanji';
          break;
        case 'expired':
          var boxnum = Math.floor(i/2) + 1;
          stack.href = this.chartdata.url_review + '&box=' + boxnum;
          stack.title = stack.value + ' expired kanji';
          
          // merge boxes for the last stack in simple view
          if (this.viewmode === this.MODE_SIMPLE && boxnum===this.MODE_SIMPLE_BOXES)
          {
            stack.href += '&merge=1';
          }
          
          break;
        case 'fresh':
          stack.title = 'Green cards are not due for review yet.';
        default:
          break;
      }
    }
  },

  /**
   * Find height of each bar by scaling min max values accross the chart
   * 
   * Add a height property to the stacks.
   * 
   * @param {Object} stacks   Array of stack objects (:value  :type)
   */
  calcHeights:function(aStacks)
  {
    var i;

    // get the maximum
    var iMax = 0;
    for (i = 0; i < aStacks.length; i++)
    {
      var oStack = aStacks[i];
      iMax = Math.max(iMax, oStack.value);
    }
    
    // scale values
    for (i = 0; i < aStacks.length; i++)
    {
      var oStack = aStacks[i];
      if (oStack.value > 0)
      {
        oStack.height = Math.floor(this.BAR_MAXHEIGHT * oStack.value / iMax);
        oStack.height = oStack.height < this.BAR_MINHEIGHT ? this.BAR_MINHEIGHT : oStack.height;
      }
      else
      {
        oStack.height = this.BAR_ZEROHEIGHT;
      }
    }
  },

  /**
   * Draw one card box, expired vs fresh stacks
   * 
   */
  drawBox:function(box)
  {
    var  x, y, ox, oy, BOX_SPACING;
    var aStacks = box.stacks;

    // box base coordinate
    if (this.viewmode === this.MODE_SIMPLE)
    {
      BOX_SPACING = 110;
      ox = Math.floor( 152 + (box.index * BOX_SPACING) );
      oy = Math.floor( this.ph - 60 + this.BOX_DEPTH/2 );
    }
    else
    {
      BOX_SPACING = 92;
      ox = Math.floor( 50 + (box.index * BOX_SPACING) );
      oy = Math.floor( this.ph - 60 + this.BOX_DEPTH/2 );
    }
    
    this.drawBoxBg(box, ox, oy, 10);

    // stacks
    x = ox - this.BAR_WIDTH;
    y = oy;
    this.drawStack(x, y, aStacks[0]);
    x = ox + 1;
    y = oy;
    this.drawStack(x, y, aStacks[1]);    

    // bottom text
    var footerText = box.footerText;
    if (footerText)
    {
      var attr = { font:"14px Arial, sans-serif", fill:"#000" };
      this.drawText(ox, oy + 16, footerText, attr, true);
    }

  },

  /**
   * Draw box background gradient and title
   * 
   */
  drawBoxBg:function(box, ox, oy, cornersize)
  {
    var x, y, x2, y2;

    if (this.viewmode === this.MODE_SIMPLE)
    {
      x = ox - this.BAR_WIDTH;
      y = oy - this.BAR_MAXHEIGHT - 70;
      x2 = x + this.BAR_WIDTH * 2 + this.BAR_DEPTH;
      y2 = y + 161;
    }
    else
    {
      x = ox - this.BAR_WIDTH + cornersize/2;
      y = oy - this.BAR_MAXHEIGHT - 70;
      x2 = x + this.BAR_WIDTH * 2; // + this.BAR_DEPTH;
      y2 = y + 161;
    }
    var bg = this.pp.path(
      {
        stroke: "none",
        gradient: "270-#FEFAE5:5-#F1EED5:95"
      })
      .moveTo(x, y - cornersize)
      .lineTo(x2, y - cornersize).addRoundedCorner(cornersize, "rd")
      .lineTo(x2 + cornersize, y2).addRoundedCorner(10, "dl")
      .lineTo(x, y2 + cornersize).addRoundedCorner(10, "lu")
      .lineTo(x - cornersize, y).addRoundedCorner(10, "ur")
      .andClose();
    this.getViewSet().push(bg);
    
    this.drawBoxTitle(box, ox + cornersize/2, y + 10);
  },
  
  drawBoxTitle:function(box, x, y)
  {
    var attr = { font:"13px Arial, sans-serif", fill:"#908C70" };
    var titles = [
      "New and\nForgotten",
      "One\nReview", "Two\nReviews", "Three\nReviews", "Four\nReviews",
      "Five\nReviews", "Six\nReviews", "Seven+\nReviews"
    ];

    var title = (this.viewmode === this.MODE_SIMPLE && box.index===4) ? "Four+\nReviews" : titles[box.index];

    this.drawText(x, y, title, attr, true);
  },

  /**
   * Draw SVG text.
   * 
   * Hack-ish fix vertical positioning in IE (for Raphaeljs 0.7.4)
   * 
   * @param {Object} x
   * @param {Object} y
   * @param {Object} caption
   * @param {Object} attr      Font, fill, etc
   * @param {Object} center    True to center text, false to left-align
   */
  drawText:function(x, y, caption, attr, center)
  {
    // 
    if (this.isIE) {
      y += 4;
    }

    attr['text-anchor'] = center ? "middle" : "start";
    var t = this.pp.text(x, y, caption).attr(attr);
    this.getViewSet().push(t);
  },

  /**
   * Draw a stack.
   * 
   * Add anchor attributes if stack is clickable.
   * 
   */
  drawStack:function(ox, oy, oBar)
  {
    var attr = false;
    var type = oBar.value > 0 ? oBar.type : 'empty';

    switch (type)
    {
      case 'failed':
        attr = {
          strokeColor: '#85391e',
          frontColor: '#ff8257',
          sideColor: '#d2633f',
          topColor: '#ffa994'
        };
        break;
      case 'untested':
        attr = {
          strokeColor: '#2c6585',
          frontColor: '#40a8e5',
          sideColor: '#3d83ac',
          topColor: '#8abde4'
        };
        break;
      case 'expired':
        attr = {
          strokeColor: '#905c24',
          frontColor: '#ffae57',
          sideColor: '#d2633f',
          topColor: '#ffcc7f'
        };
        break;
      case 'fresh':
        attr = {
          strokeColor: '#218038',
          frontColor: '#40E569',
          sideColor: '#3dac58',
          topColor: '#8ae49c'
        };
        break;
      case 'empty':
      default:
        attr = {
          strokeColor: '#444',
          frontColor: '#929292',
          sideColor: '#818181',
          topColor: '#b1b1b1'
        };
        break;
    }
    
    if(attr !== false)
    {
      this.drawIsoBar(ox, oy, oBar, attr);
      this.addBarBehaviour(ox, oy, oBar);
    }
  },

  /**
   * Draw a single isometric stack.
   * 
   * Draw the front, side, top paths, anti clockwise.
   * 
   */
  drawIsoBar:function(ox, oy, oBar, attr)
  {
    var x, y, p, height = oBar.height;
    
    // side
    x = ox + this.BAR_WIDTH;
    y = oy;
    p2 = this.pp.path({stroke:attr.strokeColor, fill:attr.sideColor, 'stroke-opacity':0.5})
      .moveTo(x, y)
      .lineTo(x+this.BAR_DEPTH, y-this.BAR_DEPTH)
      .lineTo(x+this.BAR_DEPTH, y-this.BAR_DEPTH-height)
      .lineTo(x, y-height)
      .lineTo(x, y);

    // front
    x = ox;
    y = oy;
    p1 = this.pp.path({stroke:attr.frontColor, fill:attr.frontColor, 'stroke-opacity':0.5 })
      .moveTo(x, y)
      .lineTo(x+this.BAR_WIDTH, y)
      .lineTo(x+this.BAR_WIDTH, y-height)
      .lineTo(x, y-height)
      .lineTo(x, y);
      
    // top
    x = ox;
    y = oy - height;

    p3 = this.pp.path({stroke:attr.topColor, fill:attr.topColor, 'stroke-opacity':0.5})
      .moveTo(x, y)
      .lineTo(x+this.BAR_WIDTH, y)
      .lineTo(x+this.BAR_WIDTH+this.BAR_DEPTH, y-this.BAR_DEPTH)
      .lineTo(x+this.BAR_DEPTH, y-this.BAR_DEPTH)
      .lineTo(x, y);
    
    this.viewSetPush([p1, p2, p3]);
  },
  
  /**
   * Add events to stack.
   * 
   * @param {Object} ox
   * @param {Object} oy
   * @param {Object} oBar
   */
  addBarBehaviour:function(ox, oy, oBar)
  {
    var attr = {};
    var height = oBar.height;

    if (oBar.href)
    {
      attr.href = oBar.href;
    }
    
    if (oBar.title)
    {
      attr.title = oBar.title;
    }

    // combined shape that cover the bar
    if (oBar.href || oBar.title)
    {
      x = ox;
      y = oy;
    
      var p_highlight = this.pp.path(
      {
        stroke:'#fff', fill:'#000', opacity: 0
      })
      .moveTo(x, y)
      .lineTo(x+this.BAR_WIDTH, y)
      .lineTo(x+this.BAR_WIDTH+this.BAR_DEPTH, y-this.BAR_DEPTH)
      .lineTo(x+this.BAR_WIDTH+this.BAR_DEPTH, y-this.BAR_DEPTH-height)
      .lineTo(x+this.BAR_DEPTH, y-this.BAR_DEPTH-height)
      .lineTo(x, y-height)
      .lineTo(x, y)
      .attr(attr);
      this.viewSetPush(p_highlight);

      var set = false;
      /*
      if (oBar.title)
      {
        var txt = {"font": '18px "Arial"', stroke: "none", fill: "#000"};
        x = ox + this.BAR_WIDTH/2;
        y = oy - this.BAR_MAXHEIGHT/2 - 30;
        set = this.pp.set();
        set.push( this.pp.rect(x - 50, y, 100, 40, 5).attr({fill: "#fff", stroke: "#000", "stroke-width": 2, opacity:0.5}).hide() );
        set.push( this.pp.text(x, y+10, oBar.title).attr(txt).hide() );
      }
      */

      if (oBar.href)
      {
        Event.observe(p_highlight.node, 'mouseenter', (function(set)
        {
          return function()
          {
            p_highlight.attr({ stroke:'#000', opacity: 0.2});
            
            if (set) {
              set.attr('opacity', 0).show().animate({opacity: 1}, 200);
              //label.show();
            }
          }
        })(set));
        Event.observe(p_highlight.node, 'mouseleave', (function(set)
        {
          return function()
          {
            p_highlight.attr({ opacity: 0});
  
            if (set) {
              //set.animate({opacity: 0}, 200);
              set.hide();
            }
          }
        })(set));
      }
    }    
  }
}

var RaphaelFix = Class.create();
RaphaelFix.prototype =
{
  /**
   * 
   * 
   * @param {Object} elCanvas  Container element used for Raphael/SVG canvas
   * @param {Object} oRaphael   Raphael instance
   */
  initialize:function(elCanvas, oRaphael)
  {
    // the parent div should be position:relative
    this.elOuterDiv = elCanvas.parentNode;
    
    this.pp  = oRaphael;
    this.pp.fix = this;
  },

  /**
   * Emulate Raphael/SVG text with a DOM element positioned over the SVG area.
   * 
   * Raphael 0.7.3 text is not vertically positioned consistently between Gecko/WebKit,
   * but the getBBox() function seems to work, so use that to position a DIV with the text.
   * 
   * The text can contain html and use any CSS.
   * 
   * @param object  attr    SVG text Attributes (font, fill, ...)
   * @param boolean center  Horizontal text-alignment
   * 
   */  
  text:function(ox, oy, text, attr, center)
  {
    var t, s;
    var SVG_TEXT = false;

    if (SVG_TEXT)
    {
      // text alignment from Raphael 0.7.3
      attr['text-anchor'] = center===true ? 'middle' : 'start';
      t = this.pp.text(ox, oy, text).attr(attr);

      var s = t.getBBox();
      x = ox;
      y = oy;
      this.pp.rect(x, y, s.width, s.height).attr({'fill':"#000", opacity:0.5});
      
      return t.node;
    }
    else
    {
      // emulate SVG text with DOM elements
      attr['text-anchor'] = 'start';
      var t = this.pp.text(ox, oy, text.stripTags()).attr(attr);
      var s = t.getBBox();
      t.hide();

      x = center ? ox - s.width/2 : ox;
      y =  oy;
      //this.pp.rect(x, y, s.width, s.height).attr({'fill':"#000", opacity:0.5});
//      return;

      var el = new Element('div');
      el.setStyle({
        font: attr.font ? attr.font : "12px/1em Arial",
        color: attr.fill ? attr.fill : "#000",
        position: 'absolute',
        width: s.width+'px',
        left: Math.floor(x) + 'px',
        top: Math.floor(y) + 'px'
      });
      el.innerHTML = text;
      this.elOuterDiv.appendChild(el);
      return el;
    }
  }
}
