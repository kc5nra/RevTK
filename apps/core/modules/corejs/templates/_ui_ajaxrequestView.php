  <p> Test1 (see Firebug console): <button class="test test1">Go</button></p>
  <p> Test2 (see Firebug console): <button class="test test2">Go</button></p>
  <p> Test3 (see Firebug console): <button class="test test3">Go</button></p>
  <p> Test4 (see Firebug console): <button class="test test4">Go</button></p>
  <p> Test5 (see Firebug console): <button class="test test5">Go</button></p>
  <p> Test6 (see Firebug console): <button class="test test6">Go</button></p>
  <p> Test7 (handlers): <button class="test test7">Go</button></p>
  
  <h2>Results</h2>
  
  <div id="Test1Panel" style="background:#ccc;width:50%;height:200px;text-align:center;">
    Output here.
  </div>
  
  <h2>form1</h2>
  <form id="form1">
    <input type="hidden" value="hidden_value" name="hidden1" />
    <input type="text" name="txtName[]" value="Hocus" />
    <input type="text" name="txtName[]" value="Pocus" />
  </form>

<script type="text/javascript">
(function() {

  var Y = Core.YUI,
      AJAXTEST = "<?php echo $_context->getController()->genUrl('corejs/ajaxtest') ?>",
      JSONTEST = "<?php echo $_context->getController()->genUrl('corejs/jsontest') ?>";

  var demo = {

    // simple GET without any handlers
    test1:function() {
      var ar = new Core.Ui.AjaxRequest(AJAXTEST);
    },
  
    // support for query string parameters merged with "parameters" option
    test2:function() {
      var ar = new Core.Ui.AjaxRequest(AJAXTEST+ "?name=bingo&score=500" /*, { parameters:"oldname=john" }*/ );
    },
  
    // support for parameters in string format
    test3:function() {
      var ar = new Core.Ui.AjaxRequest(AJAXTEST, { parameters:"name=john&score=500" });
    },

    // support for parameters as query string with ignored url and question mark
    test4:function() {
      var ar = new Core.Ui.AjaxRequest(AJAXTEST, {
        parameters: "http://google.com?name=john&score=500"
      });
    },

    // support for parameters passed as an object
    test5:function() {
      var options = {
        method: 'post',
        parameters: {
          name:  "john",
          score: 500,
          argh:  "Double quotes\"are'percent%andback\\slashnew\rlines\nftw and <xml缠unicode=\u4050" }
      };
      var ar = new Core.Ui.AjaxRequest(AJAXTEST, options);
    },

    // serialize a form, plus additional params
    test6:function() {
      var ar = new Core.Ui.AjaxRequest(AJAXTEST, {
        method: 'post',
        form: 'form1',
        parameters: { more: "stuff", 'arrayfoo':['one', 'two'] }
      });
    },

    // test the handlers, options.arguments, options.context, JSON response
    test7:function() {
      var connection = new Core.Ui.AjaxRequest(JSONTEST,
      {
        method: 'post',
        success: demo.test7Success,
        failure: function(o){
            Core.log('Failure handler! Response: %o Arguments: %o', o, arguments);
        },
        scope: demo,
        parameters: { name: "John", score: 500 },
        argument: [1, 2, 3]
      });
      console.log('AjaxRequest transaction id = %o', connection.id);
      
    },

    test7Success:function(o)
    {
      // check that we are in the correct scope
      var scope = typeof(this.test7)==='function' ? 'Correct' : 'Wrong';
      
      Core.log('Success handler!');
      Core.log('responseJSON = %o', o.responseJSON || 'none! (check the content-type)');
      Core.log('Response: %o Arguments: %o Scope: %s', o, o.argument, scope);
    }
    
  };

  App.ready = function()
  {
    this.evtDel.on("test", function(e, el)
    {
      var fnName;
      if (/(test\d+)/.test(el.className)) {
        fnName = RegExp.$1;
        demo[fnName].apply(demo);
      }
    });
  };

})();
</script>
