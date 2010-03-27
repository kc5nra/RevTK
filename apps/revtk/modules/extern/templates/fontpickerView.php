<?php slot('inline_styles') ?>
div.fontsample-box {
  background:#fff;
  border:1px solid #ccc;
  padding:8px 10px;
  margin:0;
}
input.fontsample {
  color: #000;
  padding:0; width:100%;
  line-height:1.4em;
  font:50pt "MS Mincho", serif; 
  margin:0;
  background:none;
  border:0;
  width:100%;
}
<?php end_slot() ?>

<div class="layout-home">

<?php include_partial('home/homeSide') ?>

   <div class="col-main">
     <div class="col-box col-box-top">

      <div class="app-header">
        <h2><?php echo link_to('Home','@homepage') ?> <span>&raquo;</span> Font Picker</h2>
        <div class="clear"></div>
      </div>
  
      <label for="fontSelector">Pick your font:</label>&nbsp;
      <select name="fontSelector" id="fontSelector" onchange="javascript:fontPicker.changeFont(this.options[this.selectedIndex].value)">
        <option value="">Select...</option>
      </select>
  
      <br /><br />
  
      Sample (click to edit):<br />
  
      <div class="fontsample-box">
        <input type="text" class="fontsample" id="fontSample" value="&#31435;&#12385;&#20837;&#12426;&#31105;&#27490;" />
      </div>
  
      <br />
      <br />
  
      <h3>Notes</h3>
  
      <ul class="content">
        <li>Original font detector by Aaron Bassett.</li>
        <li>Requires Adobe Flash Player to retrieve the list of fonts on your machine.</li>
      </ul>
      
      <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
        codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" 
        width="0" height="0" id="getFontList" align="middle">
      <param name="allowScriptAccess" value="always" />
      <param name="movie" value="getFontList.swf" />
      <embed src="/images/2.0/extern/getFontList.swf" width="0" height="0" name="getFontList" allowScriptAccess="always" 
        type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
      </object>

    </div>
  </div>
</div>

<script type="text/javascript">
var fontPicker =
{
  changeFont:function(fontName){
    if (fontName) {
      var fontSample = document.getElementById('fontSample');
      fontSample.style.fontFamily = fontName;
    }
  },

  addOptions:function(selector, names) {
    for (var idx = 0; idx < names.length; idx++) {
      var name = names[idx];
      var option = document.createElement('option');
      option.value = name;
      selector.appendChild(option);
      option.appendChild(document.createTextNode(name));
    }
  },

  thisMovie:function(movieName) {
    if (document[movieName]) {
      return document[movieName];
    } else {
      return window[movieName];
    }
  },

  getFontList:function() {
    return this.thisMovie('getFontList').getFontList();
  },

  addEvent:function(obj,event_name,func_name){
    if (obj.attachEvent){
      obj.attachEvent("on"+event_name, func_name);
    }else if(obj.addEventListener){
      obj.addEventListener(event_name,func_name,true);
    }else{
      obj["on"+event_name] = func_name;
    }
  },

  loadData:function() {
    var fontSelector = document.getElementById('fontSelector');
    var fontList = null; //getFontList();
    if (fontList) {
      this.addOptions(fontSelector, fontList);
      return;
    }
    var that = this;// inline functions loose the "this" scope (Javascript limitation)
    var timer = setInterval(function(e) {
      fontList = that.getFontList();
      if (fontList) {
        clearInterval(timer);
        that.addOptions(fontSelector, fontList);
      }
    }, 250);
  }
}

fontPicker.addEvent(window,'load',function(){fontPicker.loadData()});
</script>
