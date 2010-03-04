<h2>Features that may be added later</h2>

<ul>
	<li>FormHelper: add as needed<br/>
		<?php echo link_to('10.1. Form Helpers', 'http://www.librosweb.es/symfony_1_1_en/capitulo10/form_helpers.html') ?>

	<li><b>Escaping Javascript</b>, add functions from EscapingHelper (esc_js_no_entities, ...)
	
	<li>Action -><b>setTemplate</b>('myCustomTemplate');<br/>
	    <?php echo link_to('6.2.4. Action Termination (à la fin)', 'http://www.librosweb.es/symfony_1_1_en/capitulo6/actions.html') ?>
	<li>AssetHelper <b>decorate_with()</b> (what uses?)
	<li>User session: Flash attributes<br/>
		<?php echo link_to('6.4.2. Flash Attributes', 'http://www.librosweb.es/symfony_1_1_en/capitulo6/user_session.html') ?>
	<li>Controlling Database Transactions<br/>
		<?php echo link_to('13.1.6 @ Zend Framework', 'http://framework.zend.com/manual/en/zend.db.html#zend.db.adapter.transactions') ?>
	<li>coreDebug: sfDebug functions for php version etc
</ul>

<h2>Bugs</h2>

<ul>
	<li>Autoloading look for table peer in apps/[myapp]/lib/model/ and if not found, then look into /lib/model/
	<li><b>Error Handling</b> : require('f.txt') is not caught as exception, something to do with error num and error mask.
</ul>

<h2>Ideas</h2>

<ul>
	<li>Ajouter une interface pour crèer des nouveaux modules, classes, components.
	    En choisissant l'application, ensuite le module éventuel, cliquer "Create".
</ul>


