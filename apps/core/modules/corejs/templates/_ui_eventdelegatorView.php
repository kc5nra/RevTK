<p>Methods:</p>

<?php pre_start('js') ?>
// Start listening to events of given type(s) on the root element and
// all children elements. Pass one event type, or an array of event types.
init(elRoot, "click")
init(elRoot, ['click', ...])    
                                
// Subscribe a callback for elements of class name, "on" is a shortcut
onClass(class, fn[, scope])
on(class, fn[, scope])                    

// Subscribe a callback for element that matches the id.
onId(id, fn[, scope])

// Subscribe a callback for element that matches the tag name.
onTag(tagname, fn[, scope])

// Subscribe the default callback for events bubbling up to
// the root element. Use e.target in this handler to get the
// element that started the event chain, otherwise "el" will be the root element.
onDefault(fn[, scope])
                           
// Cleanup the event listener from the DOM.
destroy()
<?php pre_end() ?>  

<h2>Callback signature:</h2>

<p> The callback receives the event object, and the current element in the bubble chain.
    Usually matchedEl is the element with the class name that was registered with on().
</p>
<p> When using onDefault(), matchedEl is always the root element. To get the element that started
    the event chain use <samp>e.target</samp>.
</p>    
<p> Return <strong>false</strong> explicitly to stop the event and interrupt the event chain.
    Otherwise the event will continue bubbling up to the onDefault() handler if set,
    or the default element behaviour (links, form submit button, etc).
</p>

<?php pre_start('js') ?>
myCallback(e, matchedEl)
<?php pre_end() ?>
