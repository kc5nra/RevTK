<h2>coreDatabase</h2>

<p> coreDatabase is an abstraction class that allows to setup a database
connection via configuration parameters, and then access the data
with SQL querries or through the coreDatabaseSelect object.

<?php pre_start() ?>
// Constants for setFetchMode()
const FETCH_NUM         // Return row as an indexed array (only number indices)
const FETCH_ASSOC       // Return row as an associative array (the default)
const FETCH_OBJ         // Return row as an object

// Returns the underlying database connection object or resource.
// If not presently connected, returns null
// @return object|resource|null
function getConnection()

// Start building a query with the coreDatabaseSelect object.
// @return coreDatabaseSelect
function select($columns = null)

// Set the default fetch mode for fetch(), fetchRow() and fetchAll().
// @return int  Previous fetch mode.
function setFetchMode($mode)
// Returns current fetch mode
function getFetchMode()

// Run a SQL query directly, throws coreDatabaseException if it fails.
function query($query, $bind = null)
// Returns number of rows in result set of the last query, or false on failure.
function num_rows()
// Fetches the next row in the format of the current fetch mode.
// @return mixed Row as object/array (see setFetchMode), or FALSE if no results.
function fetch($fetchMode = null)
// Fetches the next row and returns it as an object.
// Note that the class's constructor receives an array of parameters as first argument.
// @return mixed Row as object, or FALSE if no results.
function fetchObject($class = 'stdClass', array $params = array())

// Returns the value of one column from a result set (useful COUNT querries)
// @return mixed The first column of the first row of result set, or FALSE.
function fetchOne($query, $bind = null)
// Returns the first row as of the result, or FALSE
// @return mixed Row as object/array (see setFetchMode), or FALSE if no results.
function fetchRow($query, $bind = null)
// Fetch all rows from the resultset
// @return array An array of resultset rows (see setFetchMode) or empty array
function fetchAll($query, $bind = null)
// Fetches the first column of all result rows as an array.
// @return array An array of values from the first column, or empty array
function fetchCol($query, $bind = null)

// Insert a new row with specified key => values,
// ignored columns get default values as per SQL TABLE creation.
// @return boolean TRUE on success, FALSE on error.
function insert($table, $data = array())

// Updates columns (key => values) in matching row(s) with optional where clause
// @return boolean TRUE on success, FALSE on error.
function update($table, $data, $where = null, $bind = null)

// Delete all rows, or matching rows with optional where clause
// @return boolean TRUE on success, FALSE on error.
function delete($table, $where = null, $bind = null)

// Returns auto increment value from the last mysql insert,
// returns 0 if no auto increment value was created
function lastInsertId()

// Safely quote values for SQL statement
// Does not quote integers and coreDbExpr instances
// If $value is an array, the result is a comma-separated list of quoted values
function quote($value)

// Output a html table with the resultset (or single rowdata)
// @param $resultset Array of objects or assoc.arrays
function dumpResultSet($resultset)
<?php pre_end() ?>

<h2>Connecting to the Database</h2>

<p> The actual database connection only happens when the coreDatabase object is
    retrieved the first time through coreContext, so no connection is made unless
	it is needed:

<?php pre_start() ?>
  $db = coreContext::getInstance()->getDatabase();
<?php pre_end() ?>

<h2>Database Connection Settings</h2>

<p> The database connection is configured in <?php echo link_to('settings.php', 'doc/misc?page_id=settings') ?>:

<?php pre_start() ?>
  'database_connection' => array
  (
    'database'       => 'blog',
    'host'           => 'localhost',
    'username'       => 'login',
    'password'       => 'passwd',
    
    # If true, execute "SET NAMES 'utf8'" after opening the connection
    'set_names_utf8' => true
  )
<?php pre_end() ?>

<h2>Binding and Quoting Parameters</h2>

<p> All methods that accept a query, or a where clause also accept binding parameters.
    The question marks in the query string are substituted with the parameters.
	The $bind argument must be a single value, or an array:

<?php pre_start() ?>
// One parameter
// => SELECT * FROM users WHERE userid = 34
$rows = $db->fetchAll('SELECT * FROM users WHERE userid = <var>?</var>', $user_id);

// Multiple parameters
// => SELECT * FROM users WHERE userid = 34 OR username = 'O\'Reilly'
$rows = $db->fetchAll('SELECT * FROM users WHERE userid = <var>?</var> OR username = <var>?</var>',
     array($user_id, $user_name));
<?php pre_end() ?>

<p> SQL expressions passed as a binding parameter will be quoted,
    to avoid this wrap the expression with the class <b>coreDbExpr</b>:

<?php pre_start() ?>
// => UPDATE users SET lastlogin = NOW()
$db->query('UPDATE users SET lastlogin = ?', <em>new coreDbExpr(</em>'NOW()'<em>)</em>)
<?php pre_end() ?>

<h2>Building SELECT queries</h2>

<p> A query can be passed as a string with optional parameters:

<?php pre_start() ?>
$db->query('SELECT * FROM users WHERE username = ?', $username);
<?php pre_end() ?>

<p> Queries built with <?php echo link_to('coreDatabaseSelect','doc/core?include_name=databaseselect') ?> also become a string parameter:

<?php pre_start() ?>
// => SELECT * FROM users WHERE (userid = 4)
$rows = $db->fetchAll( $db->select()->from('users')->where('userid = ?', $user_id) );
<?php pre_end() ?>

<h2>Inserting and Updating Data</h2>

<p> The <b>insert()</b> method takes data as an associative array.
    Remember to use coreDbExpr to wrap SQL expressions:

<?php pre_start() ?>
/*
INSERT bugs SET created_on=CURDATE(),bug_description='Something wrong',bug_status='NEW'
*/

$data = array(
    'created_on'      => new coreDbExpr('CURDATE()'),
    'bug_description' => 'Something wrong',
    'bug_status'      => 'NEW'
);

$db->insert('bugs', $data);

<?php pre_end() ?>

<p> If your table is defined with an auto-incrementing primary key, you can call the
    <b>lastInsertId()</b> method after the insert:
<?php pre_start() ?>
$row_id = $db->lastInsertId();
<?php pre_end() ?>

<p> The <b>update()</b> method takes data in the same way, plus an optional criteria
    to select which rows to update. The criteria can also use bound parameters:
	
<?php pre_start() ?>
/*
UPDATE bugs SET updated_on='2007-03-23',bug_status='FIXED' WHERE bug_id = 4
*/

$data = array(
    'updated_on'      => '2007-03-23',
    'bug_status'      => 'FIXED'
);

$n = $db->update('bugs', $data, 'bug_id = ?', $bug_id);
<?php pre_end() ?>

<h2>Deleting Data</h2>

<p> The <b>delete()</b> method takes a table name as first argument. The second argument is the criteria, 
    which can take binding parameters. Be careful because the criteria is optional.

<?php pre_start() ?>
// => DELETE FROM users
$db->delete('users');    

// => DELETE FROM users WHERE userid = 50
$db->delete('users', 'userid = ?', $user_id);
<?php pre_end() ?>
