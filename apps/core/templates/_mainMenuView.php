    <div id="sidebar2">
      <div class="padding">
        <h3>Main</h3>
        <ol>
          <li><?php echo link_to('The Front Controller', 'doc/misc?page_id=front') ?></li>
          <li><?php echo link_to('Code Organization', 'doc/misc?page_id=codeorganization') ?></li>
          <li><?php echo link_to('Application Settings', 'doc/misc?page_id=settings') ?></li>
          <li><?php echo link_to('Links & Routing System', 'doc/misc?page_id=routing') ?></li>
          <li><?php echo link_to('View Configuration', 'doc/misc?page_id=viewconfig') ?></li>
          <li><?php echo link_to('Techniques', 'doc/misc?page_id=techniques') ?></li>
          <li><?php echo link_to('Notes', 'doc/misc?page_id=notes') ?></li>
        </ol>
      </div>    
    </div>
  
    <div id="sidebar">
      <div class="padding">
        
        <h3>Core</h3>
        <ul>
          <li><?php echo link_to('coreAction', 'doc/core?include_name=action') ?></li>
          <li><?php echo link_to('coreComponent', 'doc/core?include_name=component') ?></li>
          <li><?php echo link_to('coreConfig', 'doc/core?include_name=config') ?></li>
          <li><?php echo link_to('coreContext', 'doc/core?include_name=context') ?></li>
          <li><?php echo link_to('coreController', 'doc/core?include_name=controller') ?></li>
          <li><?php echo link_to('coreDatabase','doc/core?include_name=database') ?></li>
          <li><?php echo link_to('coreDatabaseSelect','doc/core?include_name=databaseselect') ?></li>
          <li><?php echo link_to('coreDatabaseStatement','doc/core?include_name=databasestatement') ?></li>
          <li><?php echo link_to('coreDatabaseTable','doc/core?include_name=databasetable') ?></li>
          <li><?php echo link_to('coreException','doc/core?include_name=exception') ?></li>
          <li><?php echo link_to('coreJson', 'doc/core?include_name=json') ?></li>
          <li><?php echo link_to('coreRequest', 'doc/core?include_name=request') ?></li>
          <li><?php echo link_to('coreResponse', 'doc/core?include_name=webresponse') ?></li>
          <li><?php echo link_to('coreToolkit', 'doc/core?include_name=toolkit') ?></li>
          <li><?php echo link_to('coreUser', 'doc/core?include_name=user') ?></li>
          <li><?php echo link_to('coreUserBasicSecurity', 'doc/core?include_name=userbasicsecurity') ?></li>
          <li><?php echo link_to('coreValidator', 'doc/core?include_name=validator') ?></li>
          <li><?php echo link_to('coreView', 'doc/core?include_name=view') ?></li>
        </ul>
        
        <h3>Helpers</h3>
        <ul>
          <li><?php echo link_to('AssetHelper', 'doc/helper?helper_name=asset') ?></li>
          <li><?php echo link_to('CoreHelper', 'doc/helper?helper_name=core') ?></li>
          <li><?php echo link_to('DateHelper', 'doc/helper?helper_name=date') ?></li>
          <li><?php echo link_to('FormHelper', 'doc/helper?helper_name=form') ?></li>
          <li><?php echo link_to('PartialHelper', 'doc/helper?helper_name=partial') ?></li>
          <li><?php echo link_to('TagHelper', 'doc/helper?helper_name=tag') ?></li>
          <li><?php echo link_to('TextHelper', 'doc/helper?helper_name=text') ?></li>
          <li><?php echo link_to('UrlHelper', 'doc/helper?helper_name=url') ?></li>
        </ul>
        
        <h3>Symfony</h3>
        <ul>
          <li><?php echo link_to('sfParameterHolder', 'doc/lib?page_id=sfParameterHolder') ?></li>
        </ul>
        
        <h3>Core Javascript</h3>
        <ul>
          <li><?php echo link_to('CoreJs', 'documentation/corejs') ?></li>
        </ul>
    
        <h3>Demos</h3>
        <ul>
          <li><?php echo link_to('Forms', 'test/formdemo') ?></li>
          <li><?php echo link_to('Security', 'test/securitydemo') ?></li>
          <li><?php echo link_to('Setting layout', 'test/layout') ?></li>
          <li><?php echo link_to('text/plain page', 'test/plaintext') ?></li>
        </ul>
      
      </div>    
    </div>
