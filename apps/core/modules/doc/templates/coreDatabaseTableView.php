<h2>coreDatabaseTable</h2>

<p> The coreDatabaseTable class provides a simple interface to access data of a table in the database.
    There is no need to write querries for the most common operations: <b>insert</b>, <b>update</b> and <b>delete</b>.

<p> The Table Data Gateway encapsulates the <b>data logic</b>, for example a method to return the
	user rating on a forum. Its main benefit is <b>reusability</b>: methods of the data object can be called
    from various places in the application, or even in the project.

<?php pre_start() ?>
// Timestamp columns updated by insert() and update()
const CREATED_ON = 'created_on';
const UPDATED_ON = 'updated_on';

// The coreDatabase reference always available as self::$db
protected static $db;

// Optional application-specific logic can be run on first instancing of the data model
// There is no need to extend this function if not using it.
function initialize()

// Returns the name of the table, use it if manually building querries.
// self::getInstance()->getName()
function getName()
// Returns an array of column names as defined in the peer class.
// self::getInstance()->getColumns()
function getColumns()

// Start a coreDatabaseSelect querry with the table name already specified.
// Shortcut for self::$db->select(<em>...</em>)->from(self::getName())
function select($columns = null)

// Count the number of rows in table, with an optional where clause
// @return mixed Number of rows, or FALSE on failure
function  count($where = null, $bindParams = null)

// Insert a new record, $data should be in the same format as coreDatabase ->insert()
// This method will set the CREATED_ON and UPDATED_ON timestamps, if not specified.
// @return boolean TRUE on success, FALSE on error.
function  insert($data = array())

// Updates columns (key => values) in row(s) optionally matching where clause.
// This method will set the UPDATED_ON timestamp if the column is present
// @return boolean TRUE on success, FALSE on error.
function  update($data, $where = null, $bindParams = null)

// Delete all rows, or rows matching the optional where clause.
// @return boolean TRUE on success, FALSE on error.
function  delete($where = null, $bindParams = null)
<?php pre_end() ?>

<h2>Defining a Table Class</h2>

<p> For each table in the database that you want to access, define a class that extends coreDatabaseTable.

<p> The class should be named <samp><var>Something</var>Peer</samp>
    and the include file <samp><var>Something</var>Peer.php</samp>.

<p> The classname uses CamelCase, while the actual table name should be lowercase.
	
<p> Note that actual name of the table in the database, which can be set in the coreDatabaseTable class,
    does not need to be the same as the class name:

<?php pre_start() ?>
&lt;?php
/**
 * Data model for the users table.
 * 
 * @package    MyApp
 * @subpackage Users
 */

class <var>Users</var>Peer extends coreDatabaseTable
{
  protected
    $tableName   = 'users',
    $columns     = array(
      'id',
      'username',
      'password',
      coreDatabaseTable::CREATED_ON,
      coreDatabaseTable::UPDATED_ON
    );

  /**
   * Get this peer instance to access the base methods.
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

  /**
   * Example method, return user info as an object (stdClass).
   *
   */  
  public static function getUser($user_id)
  {
    self::getInstance()->select()->where('id = ?', $user_id)->query();
    return self::$db->fetchObject();
  }
}
<?php pre_end() ?>

<h2>Accessing the Table Peer</h2>

<p> Calling a peer method will autoload the class from one of the <samp>lib/model/</samp> directories
    (first it looks into the application folder, then in the project folder).

<p> All the base coreDatabaseTable methods need to be accessed through an instance of the peer class.
    However if custom methods in the peer class use <b>getInstance()</b>, then the peer method
	can be called statically, for conciseness:

<?php pre_start() ?>
// Base methods MUST be accessed with an instance
$countUsers = UsersPeer::getInstance()->count();

// Custom methods can be accessed statically,
// the custom method need to use getInstance() to access the base methods
$countNewUsers = UsersPeer::countNewUsers();

// Although this would allow the custom method to access base methods
// with <em>$this</em> it is recommended to always use self::getInstance() in custom methods,
// and declare them as static. Static calls in actions also improve readability.
$countNewUsers = UsersPeer::<em>getInstance()-></em>countNewUsers();
<?php pre_end() ?>

<h2>Table Initialization</h2>

<p> If application-specific logic needs to be initialized when a Table class is constructed,
    declare the initialize() function, it will be called when the peer class is loaded:
	
<?php pre_start() ?>
/**
 * Run some application-specific logic after the table is instanced.
 * 
 */
public function initialize()
{
  <em>...</em>
}
<?php pre_end() ?>

<h2>Extending the Model</h2>

<p> Add new methods to the class extending coreDatabaseTable, and access the table attributes
    like this:

<?php pre_start() ?>
public static function doSomething()
{
  // get the table name
  echo self::getInstance()->getName();

  // get the columns
  echo self::getInstance()->getColumns();

  // get the coreDatabase reference
  echo self::$db;

  // do something
  <em>...</em>
}
<?php pre_end() ?>


<h2>CREATED_ON and UPDATED_ON Timestamps</h2>

<p> The <b>insert()</b> and <b>update()</b> methods will automatically set values for
    columns named <samp>created_on</samp> and <samp>updated_on</samp>. The columns
	will be set to <b>NOW()</b> unless you explicitly set them in the array of data.

<p> The timestamp columns should be declared like this in the database:

<pre class="info">
created_on TIMESTAMP NOT NULL DEFAULT 0
updated_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
</pre>

<h2>Counting Rows</h2>

<p> The <b>count()</b> method provides an easy way to get a number of rows from a table.
    If no argument is given, the query corresponds to a COUNT(*) on the current table,
	otherwise a criteria and optional bound parameters may be specified:

<?php pre_start() ?>
// => SELECT COUNT(*) FROM users
$count = UsersPeer::getInstance()->count();

// => SELECT count(*) FROM users WHERE (age >= 24)
$count = UsersPeer::getInstance()->count('age >= ?', $min_age);
<?php pre_end() ?>


<h2>Selecting from the Table</h2>

<p> The <b>select()</b> method is a convenient proxy method for the coreDatabase select() method,
    with the FROM clause already set:

<?php pre_start() ?>
// Shortcut for self::$db->select(<em>...</em>)->from(self::getName())
self::getInstance()->select()->where('username = ?', $username);

// Calling ->from() multiple times overwrites the previous value, so
// the following lines have the same effect:
self::$db->select()->from('foods');
self::getInstance()->select()->from('foods');
<?php pre_end() ?>

<h2>Inserting Rows to a Table</h2>

<p> The Table <b>insert()</b> method takes an assiociative array of column names and data,
    just like the equivalent coreDatabase method, without the need to specify the table.
	It will also set the <samp>created_on</samp> and <samp>updated_on</samp> columns
	to NOW() if present, and you do not explicitly set them:
	
<?php pre_start() ?>
$data = array(
  'created_on' => new coreDbExpr('NOW()'),
  'firstname'  => 'Henry',
  'age'        => 18
);
UsersPeer::getInstance()->insert($data);
<?php pre_end() ?>

<h2>Updating Rows in a Table</h2>

<p> The <b>update()</b> method takes an associative array of columns and values to assign
    to these columns; and an SQL expression that is used in a WHERE clause, plus optional
	bound parameters.

<p> This method also sets the <samp>updated_on</samp> column to NOW() unless another value
    is given for that column.

<?php pre_start() ?>
$data = array(
  'firstname'  => 'Bill',
  'age'        => 35
);
UsersPeer::getInstance()->update($data, 'id = ?', $user_id);
<?php pre_end() ?>

<h2>Deleting Rows from a Table</h2>

<p> The <b>delete()</b> method takes two optional arguments: a where clause and optional
    bound parameters.
	
<p> Be careful that calling the method without arguments will delete <b>all</b> rows:
	
<?php pre_start() ?>
// => DELETE FROM users
UsersPeer::getInstance()->delete();

// => DELETE FROM users WHERE age < 18
UsersPeer::getInstance()->delete('age < ?', $min_age);
<?php pre_end() ?>
