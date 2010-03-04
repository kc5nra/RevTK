/*! uiForm (c)2008 Fabrice Denis - http://kanji.koohii.com */

/**
 * uiForm
 * 
 * @package   UiParts
 * @author    Fabrice Denis
 * @copyright (c)2008 Denis Fabrice
 */

/* todo: move to proper place (can we avoid inflating toolbox.js ?) */
dom.getHiddenInputs = function(parentElem)
{
	var inputs = parentElem.getElementsByTagName('input');
	var found = [];
	for (var i=0; i<inputs.length; i++)
	{
		if (inputs[i].type==='hidden')
		{
			found.push(inputs[i]);
		}
	}
	return found;
};



var uiForm = Class.create();
uiForm.prototype =
{
	// strings
	FIX_ERRORS_BEFORE_SAVE: 'FIX_ERRORS_BEFORE_SAVE message',
	CONFIRM_UNSAVED_CHANGES: 'The changes you made will be lost if you navigate away from this page.',
	WARNING_SORT_TABLE: 'Please Save or Cancel your changes before sorting the table.',

	initialize:function(view)
	{
	//	console.log('uiForm::initialize()');

		// initialize
		this.view = view;								// the uiView instance containing the form(s)
		this.evtCache = new uiEventCache('uiForm');
		this.lastactive = null;	
		this.sections = [];								// uiSection's

		// initialize form sections
		var primaryContentDiv = this.view.contentDiv;
		var edivs = dom.getElementsByClassName(primaryContentDiv,'div',uiSection.prototype.CLASS_EXPANDABLE);
		for (var i=0;i<edivs.length;i++)
		{
			var ediv = edivs[i];
			this.sections.push( new uiSection(this, ediv) );
		}

		if (this.sections.length>0) {
			// attach event to capture clicks outside of the editable areas
			this.evtCache.addEvent(document,'click',this.documentClickEvent.bindAsEventListener(this));

			// keyboard hook
			this.evtCache.addEvent(document,'keydown',this.documentKeyDownEvent.bindAsEventListener(this));
		}
	},
	
	destroy:function()
	{
		var i;
		for (i=0; i<this.sections.length; i++)
		{
			this.sections[i].destroyContent();
		}
	
		if (this.evtCache){
			this.evtCache.destroy();
		}
	},

	// discard all changes on the view
	formDiscardChanges:function(section)
	{
		if (section.bPageModified)
		{
			section.clearPageModified(); //don't confirm again that we may loose changes
			
			// reload original data
			section.updateAjaxContent(section, false);
		}
	},

	documentClickEvent:function(e)
	{
		// Note that the event here is not necessary for saving the form, it's just convenience
		//  to allow the user to click outside a section and get validation before the final Save,
		//  or simply to view the data in the non-editable form which is easier to read

		var elem = Event.element(e);
		var elemType = elem.nodeName.toLowerCase();

		switch(elemType)
		{
			// if clicking a link (potentially leaving this page or loading another view), do not post back the form data yet
			// this solves problems with the onbeforeunload event.
			// 
			case 'a':
				break;
			default:
				var expddiv = dom.getParent(elem,'div','expandable');
				if (this.lastactive && !expddiv){
					//console.log('uiForm :: clicked outside editable section!');
					this.switchSection(null);
				}
				break;
		}
		return true;
	},

	/*
		Allow exit row edit mode by pressing ESC
	*/
	documentKeyDownEvent:function(e)
	{
		var mykey = window.event ? event.keyCode : e.keyCode;
		var isCtrl = window.event ? window.event.ctrlKey : e.ctrlKey;	
		var eatkey = false;
		if (!isCtrl)
		{
			if (mykey===Event.KEY_ESC)
			{
				// exit row edit mode
				var tabular = this.lastactive.tabularTable;
				if (this.lastactive && tabular)
				{
					this.lastactive.unfocusTabularData(tabular);
					eatkey = true;
				}
			}
			if (eatkey) {
				Event.stop(e);
				return false;
			}
		}
		return true;
	},
	
	// if a section is edited, leave edit mode, and copy data back to hidden fields
	leaveEditMode:function()
	{
		var expddiv = this.lastactive.ediv;

		// if tabular table, leave row edit mode
		var tabularData = this.lastactive.tabularTable;
		if (tabularData){
			this.lastactive.unfocusTabularData(tabularData);
		}
		else{
			// copy all inputs to the read-only fields
			this.lastactive.copyFieldsForViewMode(expddiv);
			this.lastactive.setTitleBarMainValue();
		}

		// switch view mode css
		CssClass.remove(expddiv, 'expd-edit');

		// clear last global success msg (from last Save All Changes)
		this.view.clearGlobalMessage();

		// push changes to server
		//this.lastactive.updateAjaxContent(this.lastactive, true);

		this.lastactive = null;
	},

	/*
		Activate section, or deactivate current section if "section" is null	
	*/
	switchSection:function(section)
	{
		var bSwitched = false;

		// toggle last active section back to view mode
		if (this.lastactive && this.lastactive!=section)
		{
			this.leaveEditMode();
		}

		// toggle clicked section to edit mode
		if (section && !this.lastactive)
		{
			CssClass.add(section.ediv,'expd-edit');
			this.lastactive = section;
			bSwitched = true;
		}

		return bSwitched;
	}
}


var uiSection = Class.create();
uiSection.prototype =
{
	CLASS_EXPANDABLE:'expandable',
	CLASS_EXPANDED:'expd-expanded',
	CLASS_COLLAPSED:'expd-collapsed',
	EXPD_MAIN_VALUE:'expd-main-value',

	CLASS_NEW_ROW:'JsNewRow',
	CLASS_EDIT_ROW:'JsRowEdit',			// tabular data, row in edit mode
	CLASS_ROW_TEMPLATE:'JsRowTemplate',
	CLASS_ROW_SELECTED:'JsSelected',
	CLASS_DYN_EDIT:'JsDynEdit',				// dynamically created form fields

	POSTDATA_ID_SEPARATOR:'-',			// this is used to concatenate ids in post data, where needed

	initialize:function(oForm, ediv)
	{
	//	console.log('uiSection::initialize()');
		
		this.oForm = oForm;
		this.evtCache = new uiEventCache('uiSection');
		this.evtCacheRow = new uiEventCache('Editable Row');
		this.ediv = ediv;
		this.tabularTable = null;
		this.controllerUrl = null;
		
		this.contentDiv = dom.getElementsByClassName(ediv,'div','expd-content')[0];
		this.ajaxDiv = dom.getElementsByClassName(this.contentDiv,'div',RevTK.AJAX_CONTAINER_CLASS)[0];
		if (!this.ajaxDiv){
			alert('uiSection::initialize() - AJAX_CONTAINER_CLASS div missing');
		}

		// expand/collapse event
		// switch content to edit mode event
		var divbtn = dom.getElementsByClassName(ediv,'td','expd-button')[0];
		if (divbtn)
		{
			var a = divbtn.getElementsByTagName('a')[0];
			this.oForm.evtCache.addEvent(a,'click',this.expandCollapseEvent.bindAsEventListener(this));
		}

		this.oForm.evtCache.addEvent(this.contentDiv,'click',this.editableContentClickEvent.bindAsEventListener(this));

		// if a section is in edit mode on page load...
		if (/expd-edit/.test(ediv.className)){
			this.oForm.lastactive = this;
		}

		// table commands
		/*
		var divtblcmd = dom.getElementsByClassName(ediv,'div','top-links')[0];
		if (divtblcmd) {
			this.oForm.evtCache.addEvent(divtblcmd,'click',this.tableCommandsEventHandler.bindAsEventListener(this));
		}
		*/

		this.initializeContent();
	},
	
	// attach events to the content part that can be refreshed via ajax
	initializeContent:function()
	{
		this.bPageModified = false;

		this.modifiedRows = [];

		this.attachPageModifiedEvents(this.ajaxDiv);

		// tabular data interface
		var table = dom.getElementsByClassName(this.ajaxDiv,'table',uiWidgets.AjaxTable.prototype.TABULARDATA_CLASS)[0];
		if (table)
		{
			this.tabularTable = table;
			this.uiTableInst = new uiTable(table);

			// attach paging events
			var tPaging = dom.getElementsByClassName(this.ajaxDiv,'div','JsPaging')[0];
			if (tPaging) {
				this.evtCache.addEvent(tPaging, 'click', this.tabularDataPagingEvent.bindAsEventListener(this));
			}
			
			// table events
			this.evtCache.addEvent(table,'click',this.tabularDataHandler.bindAsEventListener(this));
		}

		// initalize "main value" copy in the section's titlebar
		this.setTitleBarMainValue();

		// process ajax response
		this.processAjaxResponse();

		// locate editable row field templates div if provided
		this.fieldTemplatesDiv = dom.getElementsByClassName(this.ajaxDiv,'div','JsFieldTemplates')[0];
	},
	
	processAjaxResponse:function()
	{
		var jsDataDiv = dom.getElementsByClassName(this.ajaxDiv,'div','JsData')[0];
		if (!jsDataDiv) {
			 alert("Missing JsData DIV")
		}
		this.jsData = dom.getHiddenParams(jsDataDiv);
		
		this.controllerUrl = this.jsData.controller;
		if (!this.controllerUrl){
			alert('uiSection::processAjaxResponse() - ERROR Missing "JsData-controller" value');
		}

		if (this.jsData.errors && this.jsData.errors=='1')
		{
		//	console.log('processAjaxResponse() Errors!');
			this.pageModified();
			
			// identify modified rows
			if (this.tabularTable)
			{
				var rows = this.tabularTable.tBodies[0].rows;
				for (r=0; r<rows.length; r++)
				{
					if (rows[r].className==='validation-error' || rows[r].className===this.CLASS_NEW_ROW) {
						this.modifiedRows[rows[r].rowIndex] = true;
					}
				}
			}
		}
	},

	/*
		Send a request to the server for an update of the uiAjaxContainer.
		
		bPostData : true to post the form data
		extraPostData : extra query parameters, always sent if set
	*/
	updateAjaxContent:function(section, bPostData, extraPostData)
	{
	//	console.log('uiSection::updateAjaxContent()');
		var expddiv = section.ediv;

		// always post back values from JsData and extraPostData
		var p, postdata = {};

		if (this.jsData.formData)
		{
			var extraFormData = this.jsData.formData;
			for (p in extraFormData)
			{
				postdata[p] = extraFormData[p];
			}
		}

		if (extraPostData)
		{
			for (p in extraPostData){
			    postdata[p] = extraPostData[p];
			}
		}

		if (bPostData) {
			this.serializeData(postdata);
		}

		// create a div to block clicks (user editing data) in the section until response arrives
		var div = this.ajaxBlockerDiv;
		if (!div)
		{
			div = document.createElement('div');
			div.style.position='absolute';
			div.style.zIndex = 100;
			//div.style.background='#fff';
			//div.style.opacity = '0.5';
			//div.style.filter = 'alpha(opacity=50)';
			div.style.display='none';
			document.body.appendChild(div);
			this.ajaxBlockerDiv = div;
		}

		// position blocker div
		var pos = dom.findPosition(expddiv), size = $(expddiv).getDimensions();
		div.style.left = pos[0]+'px';
		div.style.top = pos[1]+'px';
		div.style.width = size.width+'px';
		div.style.height = size.height+'px';
		div.style.display = 'block';

		var that = this;

		new Ajax.Request(section.controllerUrl, {
			method: 'post',
			parameters: postdata,
			onSuccess: function(transport)
			{
			//	console.log('uiForm::updateAjaxContent() : response (section %o)', section);
				div.style.display = 'none';
				section.destroyContent();
				section.ajaxDiv.innerHTML = transport.responseText;
				section.initializeContent();
				
				if (!that.jsData.errors) {
					that.clearPageModified();
				}
			},
			onFailure:function(response) {
				RevTK.ajaxHandleFailure(response);
				div.style.display = 'none';
			}
		});
	},

	destroyContent:function()
	{
		if (this.tabularTable){
			this.uiTableInst.destroy();
		}
		this.evtCache.destroy();
	},
	
	expandCollapseEvent:function(e)
	{
		var expanded = /expd-expanded/.test(this.ediv.className);
		expanded = !expanded;
		
		CssClass.remove(this.ediv,'expd-collapsed');
		CssClass.remove(this.ediv,'expd-expanded');
		CssClass.add(this.ediv, expanded ? 'expd-expanded' : 'expd-collapsed');

		this.contentDiv.style.display = expanded ? 'block':'none';
	},

	// disable the page modified warning
	clearPageModified:function()
	{
		this.bPageModified = false;

		// clear page modified css hook
		CssClass.remove(this.ajaxDiv, 'JsPageModified');
	},

	// enable the save changes, discard changes buttons
	pageModified:function()
	{
		this.bPageModified = true;
		
		// add page modified css hook
		CssClass.add(this.ajaxDiv, 'JsPageModified');

		// warning on page exit in case of unsaved changes
		window.onbeforeunload = function()
		{
			return uiForm.prototype.CONFIRM_UNSAVED_CHANGES;
		}
	},

	/*
		Save all changes on the view (confirm save to server)
		
	*/
	sectionSaveChanges:function()
	{
		if (!this.bPageModified) {
			return;
		}
			
		window.onbeforeunload = null;

		this.oForm.switchSection(null);

		// post form
		this.updateAjaxContent(this, true, {saveChanges:1});
	},

	/* detection of page changes */
	
	attachPageModifiedEvents:function(container, isEditableRow)
	{
		var that = this;
		// for editable row we add/remove events for each row, so the eventcache is different
		var eventcache = (isEditableRow) ? this.evtCacheRow : this.evtCache;
		
		function attachEvents(elems)
		{
			for (var i=0,n=elems.length; i<n; i++)
			{
				var elemtype = elems[i].getAttribute('type') || elems[i].tagName.toLowerCase();
				switch(elemtype)
				{
					case 'text':
					case 'password':
					case 'textarea':
						eventcache.addEvent(elems[i],'focus', function(){ this.setAttribute('lastvalue',this.value); });
						eventcache.addEvent(elems[i],'keyup', function(){ if (this.getAttribute('lastvalue')!=this.value) that.pageModified(); });
						break;
					case 'checkbox':
					case 'radio':
						if (/JsSelRow/.test(elems[i].className))
							break;
						eventcache.addEvent(elems[i],'click', function(){ that.pageModified(); });
						break;
					case 'select':
					case 'select-one': /* this one is for IE6 P.O.S */
						// skip the "template" SELECTs
						if (!CssClass.has(elems[i], 'edit')) {
							break;
						}

						//TEST
						//elems[i].onclick = function(){ console.log('v '+this.value+' i '+this.selectedIndex); }
						// elems[i].onchange = function(){ console.log('changed!'); }
						eventcache.addEvent(elems[i],'change',function(){ that.pageModified(); });
						break;
					case 'hidden':
					default:
						break;
				}
			}
		}
		
		var elems;
		elems = container.getElementsByTagName('input');
		attachEvents(elems);
		elems = container.getElementsByTagName('textarea');
		attachEvents(elems);
		elems = container.getElementsByTagName('select');
		attachEvents(elems);
	},

	editableContentClickEvent:function(e)
	{
		var elem = Event.element(e);

		// content click : activate this section

		if (this.oForm.switchSection(this))
		{
			// if not tabular data, try to focus directly field that was clicked
			if (!this.tabularTable)
			{
				var clicked = dom.getElementsByClassName(elem.parentNode,'*','edit')[0];
				if (clicked){
					var elemtag = clicked.nodeName.toLowerCase();
					var elemtype = (elemtag=='input') ? clicked.getAttribute('type') : elemtag;
					switch(elemtype){
						case 'select':
							clicked.focus();
							break;
						case 'text':
						case 'textarea':
							clicked.focus();
							dynHTML.setCaretToEnd(clicked);
							break;
					}
				}
			}
		}


		if (/JsAction(\w+)/.test(elem.className))
		{
			var sAction = RegExp.$1;
			return this.actionsEventHandler(e, elem, sAction);
		}

		if (elem.className.indexOf('JsDatePicker')>=0)
		{
			this.datePickerOpen(elem);
			Event.stop(e);
			return false;
		}

		// events within table row

		var parentTD = dom.getParent(elem, 'td');
		if (parentTD)
		{
			// delete a data row from tabular data
			if (/JsDelRow/.test(parentTD.className))
			{
				var row = dom.getParent(elem, 'tr');
				this.deleteDataRow(row);
				Event.stop(e);
				return false;
			}
		}
		
		return true;
	},

	// returns true if section is in edit mode
	isEditMode:function()
	{
		return /expd\-edit/.test(this.ediv.className);
	},

	/*
		Serialize all data in the div to send via Ajax
		Post should be an object (not array), post data will be added to it.
		
		post will be modified!
	*/
	serializeData:function(post)
	{
		var container = this.ediv;
		var that = this;

		function serializeTabularData(table)
		{
			var i, r, rn, rows = table.tBodies[0].rows;
			var unchangedrows = [];	// rows we don't need to post
			var deletedrowids = [];
			
			var cols = table.tHead.rows[0].cells;
			var colnum;

			// Send deleted rows ids in post data
			// Also note rows for which data doesn't need to be posted
			for (r=0, rn=rows.length; r<rn; r++)
			{
				var bDeletedRow = /JsDeleted/.test(rows[r].className);
				if (bDeletedRow)
				{
					var row_id = rows[r].id;
					deletedrowids.push(row_id);
				}

				unchangedrows[r] = (rows[r].className===this.CLASS_ROW_TEMPLATE) || bDeletedRow || !that.isModifiedRow(rows[r]);
			}
			if (deletedrowids.length>0)
			{
				post.deletedRowIds = deletedrowids;
			}

			var newRowsData = {};
			var rowids = [];
			for (r=0, rn=rows.length; r<rn; r++)
			{
				if (unchangedrows[r])
					continue;

				// row id
				rowids.push( rows[r].className===that.CLASS_NEW_ROW ? '*' : rows[r].id );

				var inputs = $A(rows[r].getElementsByTagName('input'));
				var textareas = $A(rows[r].getElementsByTagName('textarea'));
				inputs = inputs.concat(textareas);

				for (i=0; i<inputs.length; i++)
				{
					var colName = inputs[i].name;
					var elemTag = inputs[i].nodeName.toLowerCase();
					var elemType = elemTag==='input' ? inputs[i].type : elemTag;

					if ((elemType==='hidden' || elemType==='textarea') && colName!=='')
					{
						if (!(colName in newRowsData))
						{
							newRowsData[colName] = [];
						}
						newRowsData[colName].push(inputs[i].value);
					}
				}
			}
			
			for (colName in newRowsData)
			{
				post[colName] = newRowsData[colName];
			}
			
			post['rowids[]'] = rowids;
		}
		
		// serialize check boxes and radio buttons
		function serializeMultiInputData(parentElem)
		{
			var i, inputs = parentElem.getElementsByTagName('input');
			var checkedItems = [];
			for (i=0; i<inputs.length; i++)
			{
				if (inputs[i].checked)
				{
					checkedItems.push(inputs[i].value);
				}
			}
			post[inputs[0].name] = checkedItems;
		}
		
		if (this.tabularTable)
		{
			serializeTabularData(this.tabularTable);

		}
		else
		{
			// every editable data has a corresponding var tag for the "view only" mode, so look for these
			//
			var vartags = container.getElementsByTagName('var'); //dom.getElementsByClassName(container,'*','edit');
			var i;
			for (i = 0; i<vartags.length; i++)
			{
				var vartag = vartags[i];
				var parent = vartag.parentNode;
				var j, inputs = $A(parent.getElementsByTagName('input'));
				
				inputs = inputs.concat($A(parent.getElementsByTagName('textarea')));
				
				// submit all input fields associated with var tag
				for (j=0; j<inputs.length; j++)
				{
					var input = inputs[j];
					switch(input.type)
					{
						case 'text':
						case 'textarea':
						case 'hidden':
							post[input.name] = input.value;
							break;
						case 'radio':
						case 'checkbox':
							var editGroup = dom.getParent(input, 'span', 'edit');
							serializeMultiInputData(editGroup);
							break;
					}
				}
				var select = parent.getElementsByTagName('select')[0];
				if (select)
				{
					post[select.name] = select.value;
				}
			}
		}

	//	console.log('Serialized data : %o', post);
	},

	
	copyFieldsForViewMode:function(content)
	{
		var that = this;

		// copy value back into the hidden field, as well as the "view mode" <var> element
		function copyToVarTag(editNode, displayValue, hiddenValue)
		{
			// look for a sibling <var> tag which is associated with the field
			var vartag = editNode.parentNode.getElementsByTagName('var')[0];
			if (vartag) {
				vartag.innerHTML = displayValue;
			}else{
				//
				//alert('uiForm::copyFieldsForViewMode() - missing var tag');
			}
			
			// find hidden field and save value
			var inputs = editNode.parentNode.getElementsByTagName('input');
			for (var h=0;h<inputs.length;h++){
				if (inputs[h].getAttribute('type')=='hidden'){
					// if hidden value (<option value="...">) is provided save that instead of the display value
					inputs[h].value = hiddenValue!==undefined ? hiddenValue : displayValue;
					break;
				}
			}
		}

		var edits = dom.getElementsByClassName(content,'*','edit');
		for (var i=0, n=edits.length; i<n; i++)
		{
			// edit node can be the input itself, or it could be a span enclosing multiple radiobuttons/checkboxes
			var editNode = edits[i];
			var tag = editNode.nodeName.toLowerCase();
			var elemType = (tag=='input') ? editNode.getAttribute('type') : tag;
			switch(elemType)
			{
				case 'text':
				case 'textarea':
					copyToVarTag(editNode, editNode.value);
					break;
				case 'select':
					var option = editNode.options[editNode.selectedIndex];
					copyToVarTag(editNode, option.text, option.value);
					break;
				case 'span':
				// <span class="edit" ...> is a group of checkboxes or radiobuttons
					var sValue = '';
					var inputs = editNode.getElementsByTagName('input');
					for (r=0;r<inputs.length;r++){
						if (inputs[r].getAttribute('type')=='hidden'){
							// hidden field holds value from picker selection
							sValue = inputs[r].value;
							break;
						}
						else if (inputs[r].checked)
						{
							var group = dom.getParent(inputs[r],'span','group');
							var label = group.getElementsByTagName('label')[0];
							var sLabelText = label ? label.innerHTML : '[ERROR:missing <label>]';
							if (inputs[r].getAttribute('type')=='radio'){
								sValue = sLabelText;
								break;
							}else{
							//	sValue = sValue + '<span class="ico ico-check-readonly">'+sLabelText +'</span>';
								sValue = sValue + (sValue.length ? ', ':'') + '<span>'+sLabelText +'</span>';
							}
						}
					}
					copyToVarTag(editNode,sValue);
					break;
			}
		}

	},
	
	// display the section's "main value" (if any is set) in the title bar
	setTitleBarMainValue:function()
	{
		var expddiv = this.ediv;
		var content = dom.getElementsByClassName(expddiv,'div','expd-content')[0];
		
		function updateTitleBar(sValue)
		{
			var expdtitle = dom.getElementsByClassName(expddiv,'*','expd-title')[0];
			var span = expdtitle.getElementsByTagName('span')[0];
			span.innerHTML = sValue;
		}
		
		// if tabular data... display the number of rows in the section's title
		var isTabular = dom.getElementsByClassName(content,'table','cstp-tabular')[0];
		if (isTabular)
		{
			// count non-deleted rows
			var i, count = 0, rows = isTabular.tBodies[0].rows;
			for (i=rows.length-1; i>=0; i--){
				if (rows[i].className!==this.CLASS_ROW_TEMPLATE && rows[i].style.display!=='none'){
					count++;
				}
			}
			var sValue = ' ('+count+')';
			updateTitleBar(sValue);
			return;
		}

		// if not tabular data, display the "main value", if any, in the section's title
		var edits = dom.getElementsByClassName(content,'*','edit');
		for (var i=0, n=edits.length; i<n; i++)
		{
			// edit node can be the input itself, or it could be a span enclosing multiple radiobuttons/checkboxes
			var editNode = edits[i];
			var tag = editNode.nodeName.toLowerCase();
			var elemType = (tag=='input') ? editNode.getAttribute('type') : tag;
			var regMainValue = new RegExp(this.EXPD_MAIN_VALUE);
			if (regMainValue.test(editNode.className))
			{
				var displayValue = '';
				switch(elemType){
					case 'text':
						displayValue = editNode.value;
						break;
					case 'select':
						displayValue = editNode.options[editNode.selectedIndex].text;
						break;
					case 'span':
						var inputs = editNode.getElementsByTagName('input');
						for (r=0;r<inputs.length;r++){
							if (inputs[r].getAttribute('type')=='hidden'){
								// hidden field holds value from picker selection
								displayValue = inputs[r].value;
								break;
							}
							else if (inputs[r].checked) {
								var group = dom.getParent(inputs[r],'span','group');
								var label = group.getElementsByTagName('label')[0];
								var sLabelText = label ? label.innerHTML : '[ERROR:missing <label>]';
								if (inputs[r].getAttribute('type')=='radio'){
									displayValue = sLabelText;
									break;
								}else{
								//	sValue = sValue + '<span class="ico ico-check-readonly">'+sLabelText +'</span>';
									displayValue = displayValue + (displayValue.length ? ', ':'') + '<span>'+sLabelText +'</span>';
								}
							}
						}
						break;
				}
				
				if (displayValue.length)
					displayValue = ' - '+displayValue;
				updateTitleBar(displayValue);
			}
		}
	},	
	

	/* TABULAR DATA */

	// event handler for table actions (delete multiple rows, add new row, ...)
	actionsEventHandler:function(e, elem, sAction)
	{
	//	console.log('actionsEventHandler(%s)', sAction);
		
		var table = this.tabularTable;

		switch (sAction)
		{
			case 'Discard':
				this.oForm.formDiscardChanges(this);
				Event.stop(e);
				return false;

			case 'Save':
				this.sectionSaveChanges();
				Event.stop(e);
				return false;

			case 'DelRow':
				this.oForm.switchSection(this);
			
				var checkers = dom.getElementsByClassName(table.tBodies[0],'input','JsSelRow');
				var bDeleted = false;
	
				// make sure no row is currently in edition
				this.unfocusTabularData(table);
	
				for (var i=0, n=checkers.length; i<n; i++)
				{
					if (checkers[i].checked)
					{
						// 'delete' this row (hide it)
						var row = dom.getParent(checkers[i], 'tr');
						this.setRowDeleted(row);
						bDeleted = true;
					}	
				}
				
				// uncheck the "select all" checkbox
				var selAllRows = dom.getElementsByClassName(table.tHead,'input','JsSelRow')[0];
				if (selAllRows) {
					selAllRows.checked = false;
				}
	
				// update number of rows in titlebar
				if (bDeleted)
				{
					this.setTitleBarMainValue();
					this.pageModified();
				}
			
				Event.stop(e);
				return false;
			
			case 'AddRow':
				this.oForm.switchSection(this);
	
				this.unfocusTabularData(table);
	
				var newRow = this.tabularDataAddNewRow();
	
				var firstVar = newRow.getElementsByTagName('var')[0];
				var firstEditableCell = firstVar ? dom.getParent(firstVar, 'td') : null;
				this.editTabularDataRow(table, newRow, firstEditableCell);
	
				this.setTitleBarMainValue();
				this.pageModified();
	
				Event.stop(e);
				return false;
			
			default:
				alert('actionsEventHandler() - Unhandled action');
				return false;
		}
		
		return true;
	},

	// adds a new row (duplicates the hidden template row) into the tabular data table, returns new TR element
	tabularDataAddNewRow:function()
	{
		var tbody = this.tabularTable.tBodies[0];
		var rowTemplate = tbody.rows[0];

		if (!CssClass.has(rowTemplate, this.CLASS_ROW_TEMPLATE)){
			alert('uiSection::JsTableAddRow() - missing row template in table');
			return;
		}

		var newRow = rowTemplate.cloneNode(true);
		newRow.style.display = '';
		newRow.className = this.CLASS_NEW_ROW; // clear "JsRowTemplate"!
		tbody.appendChild(newRow);
		
		return newRow;
	},

	checkAllRows:function(table, bChecked)
	{
		var checkbox, rows = table.tBodies[0].rows;
		for (var r=0, n=rows.length; r<n; r++)
		{
			var tr = rows[r];
			if (tr.className===this.CLASS_ROW_TEMPLATE)
				continue;
			
			checkbox = dom.getElementsByClassName(tr,'input','JsSelRow')[0];
			if (checkbox)
			{
				checkbox.checked = bChecked ? true : false;
				if (bChecked)
					CssClass.add(tr, this.CLASS_ROW_SELECTED);
				else
					CssClass.remove(tr, this.CLASS_ROW_SELECTED);
			}
		}
	},

	tabularDataPagingEvent:function(e)
	{
		var elem = Event.element(e);
		
		if (elem.nodeName.toLowerCase()=='a')
		{
			this.unfocusTabularData(this.tabularTable);
			
			var pageQuery = elem.getAttribute('href').substr(1);
			if (pageQuery.length>0)
			{
				var pageParams = pageQuery.toQueryParams();
				this.updateAjaxContent(this, true, pageParams);
			}
		}

		Event.stop(e);
		return false;
	},

	tabularDataHandler:function(e)
	{
		var elem = Event.element(e);
		var row = dom.getParent(elem, 'tr');
		var cell = dom.getParent(elem, 'td') || dom.getParent(elem, 'th');
		if (!row)
			return;

		switch(e.type)
		{
			case 'click':
				// clicking the row selection box doesn't need to switch row in edit mode
				if (/JsSelRow/.test(elem.className))
				{
					var bChecked = elem.checked;
				
					// checkbox in table head : select all rows
					if (cell.nodeName.toLowerCase()==='th')
					{
						this.checkAllRows(this.tabularTable, bChecked);						
					}
					else
					{
						if (bChecked)
							CssClass.add(row, this.CLASS_ROW_SELECTED);
						else
							CssClass.remove(row, this.CLASS_ROW_SELECTED);
					}

					// event needs to go through for checkbox check
					return true;
				}

				// clicking in table head
				if (elem.nodeName.toLowerCase()=='a' && cell.nodeName.toLowerCase()=='th')
				{
					// sortable column
					if (/(^|\s)sort[a-z]*/.test(elem.className))
					{
						if (this.bPageModified)
						{
							alert(uiForm.prototype.WARNING_SORT_TABLE);
							Event.stop(e);
							return false;
						}
						
						// post the form with extra parameters from the column sort
						// (also activate this section if not already, unedit current row if any)
						this.oForm.switchSection(this);
						this.unfocusTabularData(this.tabularTable);

						// the link within the table head contains the sort params
						var sortQuery = elem.getAttribute('href').substr(1);
						if (sortQuery.length>0)
						{
							var sortParams = sortQuery.toQueryParams();
							this.updateAjaxContent(this, true, sortParams);
						}
						else
						{
							alert('uiSection:: missing table sort query in A tag');
						}

						Event.stop(e);
						return false;
					}
				}

				// switch that row in edit mode, unfocus last edited row
				if (!CssClass.has(row, this.CLASS_EDIT_ROW))
				{
					// is this editable at all ?
					var inputs = row.getElementsByTagName('input');
					if (inputs.length>0) {
						this.unfocusTabularData(this.tabularTable);
						this.editTabularDataRow(this.tabularTable, row, cell);
					}
				}
				break;
		}
		return true;
	},

	// note: handles both tabular data and simple data tables
	deleteDataRow:function(row)
	{
		var expddiv = this.ediv;
		var table = dom.getParent(row,'table');
		var isTabular = CssClass.has(table, 'cstp-tabular');

		// is current edited row?
		if (isTabular && CssClass.has(row, this.CLASS_EDIT_ROW))
		{
			this.unfocusTabularData(table);
		}

		this.setRowDeleted(row);
		this.setTitleBarMainValue();
		this.pageModified();
	},

	// flag row as "deleted" : hide it so can check later its removed for post data
	setRowDeleted:function(row)
	{
		row.style.display = 'none';
		CssClass.add(row, 'JsDeleted');
	},

	isModifiedRow:function(row)
	{
		return this.modifiedRows[row.rowIndex];
	},

	// switch a row in edit mode, focus input field in clicked cell if !undefined
	editTabularDataRow:function(table, row, clickedCell)
	{
		// remember if input fields have been created already (events were attached, etc.)
		var isEditable = !!row.getAttribute('uiEditable');

		if (!isEditable)
		{
			// create input fields for each <var></var> data
			var vars = row.getElementsByTagName('var');
			for (i=0; i<vars.length; i++)
			{
				// skip SelectionPicker fields (special case)
				if (vars[i].className=='JsUsePicker' || vars[i].className=='JsNoEdit')
				{
					continue;
				}

				var td = dom.getParent(vars[i],'td');

				// get the value from the first found hidden field
				var hiddenfield = td.getElementsByTagName('input')[0];
				var sData = hiddenfield ? hiddenfield.value : '';

				// dynamically insert a textbox, or a select box based on whats indicated in hiddenfield's class

				var input;
				if (/JsSelect-(\w+)/.test(hiddenfield.className))
				{
					// id of the SELECT template to use
					var fldTemplateId = RegExp.$1;

					if (!this.fieldTemplatesDiv){
						alert('uiSection::editTabularDataRow() - JsFieldTemplates missing for SELECT');
					}
					
					// get the template SELECT to use
					var templateFieldClass = 'JsSelect-'+fldTemplateId;
					var templateSelect = dom.getElementsByClassName(this.fieldTemplatesDiv,'select',templateFieldClass)[0];
					if (!templateSelect){
						alert('uiSection::editTabularDataRow() - SELECT of class "'+templateFieldClass+'" not found');
					}

					input = document.createElement('select');

					// copy template SELECT's options
					var selOptions = templateSelect.options;
					var newOption;
					for (var r=0,rn=selOptions.length; r<rn; r++)
					{
						input.options[r] = new Option(selOptions[r].text,selOptions[r].value);
					
						/* TESTING (doesn't solve the onchange-DEAD- bug )
						newOption = document.createElement("option");
						newOption.value = selOptions[r].value;  // assumes option string and value are the same 
						newOption.text = selOptions[r].text; 
						// add the new option
						try {
							input.add(newOption);  // this will fail in DOM browsers but is needed for IE
						}
						catch (e) {
							input.appendChild(newOption);
						}
						*/
					}

					// classname combines with the hiddenfield for more CSS styling options
					input.className = this.CLASS_DYN_EDIT+' edit '+hiddenfield.className;
					input.value = sData;
				}
				else
				{
					input = document.createElement('input');
					input.setAttribute('type', 'text');
					var w = td.offsetWidth - 20 - 10; // td padding, input padding, input border
					input.style.width = w+'px';
					input.className = 'text edit '+this.CLASS_DYN_EDIT;
					input.value = sData;
				}
				td.appendChild(input);
			}

			// mark row as modified for post data
			this.modifiedRows[row.rowIndex]=true;

			// attach events to detect page changes
			this.attachPageModifiedEvents(row, true);
		}


		// set edit mode on row, css will hide all 'var' elements, and show the input fields at once
		CssClass.add(row, this.CLASS_EDIT_ROW);

		// focus first editable field
		if (clickedCell)
		{
			var field = dom.getElementsByClassName(clickedCell, '*', 'edit')[0];
			var fieldNode = field ? field.nodeName.toLowerCase() : null;
			if (fieldNode==='input' || fieldNode==='select')
			{
				field.focus();
				if (fieldNode==='input')
				{
					dynHTML.setCaretToEnd(field);
				}
			}
		}
	},


	// when moving from one row to another, or switching another section in edit mode,
	// switch current row out of edit mode
	unfocusTabularData:function(table)
	{
		// if a row is in edit mode, leave edit mode
		var lasteditrow = dom.getElementsByClassName(table,'tr',this.CLASS_EDIT_ROW)[0];
		if (lasteditrow)
		{
			// prevent memory leaks
			this.evtCacheRow.destroy();

			// copy fields back to view mode
			this.copyFieldsForViewMode(lasteditrow);
			
			// delete dynamically created form fields to free up memory
			var dynFields = dom.getElementsByClassName(lasteditrow,'*',this.CLASS_DYN_EDIT);
			for (var i=0; i<dynFields.length; i++)
			{
				dynFields[i].parentNode.removeChild(dynFields[i]);
			}

			//
			CssClass.remove(lasteditrow,this.CLASS_EDIT_ROW);
		}
	},
	
	/* date picker */

	datePickerOpen:function(linkTag)
	{
		alert('datepicker');
	}
}
