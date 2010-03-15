<h2>Welcome</h2>

<p> The Core framework is a lightweight php5 framework implementing the MVC pattern.

<ul>
  <li>Templating and layout using php.
  <li>Database abstraction layer (currently only implements mysql)
  <li>Basic database ORM (Object Relational Mapping) for separation of data access
  <li>Uses php for configuration files
</ul>

<p> This is a "live" documentation of the framework:

<ul>
  <li>The documentation itself uses the framework
  <li>Features can be tested easily by editing this "demo" application
  <li>Documentation is not exhaustive but covers the most frequent needs
  <li>Examples are designed so that code blocks can be copy/pasted to save time
</ul>

<h2>References</h2>

<ul>
  <li><?php echo link_to('The MVC Pattern','http://www.librosweb.es/symfony_1_1_en/capitulo2/the_mvc_pattern.html') ?></li>
</ul>


<h2>Credits</h2>

<p> The Core framework is based on <?php echo link_to('Symfony','http://www.symfony-project.org/') ?>. It started as a separate project, and then gradually started to replicate the Symfony API. Once I learned enough about MVC and php, I stopped rewriting code, and started including libraries as is (core/lib/sf). The Core code should mirror closely the Symfony API, in order to ease a possible transition to the original Symfony someday.</p>

<p> The database layer implementation also started as a learning project. It mirrors the API from <?php echo link_to('Zend_Db','http://framework.zend.com/manual/en/zend.db.html') ?>. It is much smaller because it implements only the most common MySQL features. Similarly to the Core framework code, the coreDatabase API should mirror that of Zend_Db closely to ease transition to the Zend_Db library if the need comes.</p>
  
<p> Core framework documentation design based on the 2008 <?php echo link_to('24 ways','http://24ways.org/') ?>.</p>
