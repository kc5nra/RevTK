<h2>coreDatabaseSelect</h2>

<p> Using coreDatabaseSelect is helpful if you need to assemble a SELECT query procedurally,
    or based on conditional logic in your application.

<p>	After you build a query, you can execute the query as if you had written it as a string.

<?php pre_start() ?>
// constants to use with reset() method
const COLUMNS
const FROM
const JOINS
const WHERE
const GROUP
const ORDER
const LIMIT_COUNT
const LIMIT_OFFSET

// Initialize the SELECT statement
function __construct(coreDatabase $db, $columns = null)

// Return the SQL querry as a string
function __toString()

// Reset part of the query (see constants), or all parts if no argument is passed.
function reset($part = null)

// Select columns if they were reset, usually specify columns when instancing the class
function columns($columns)

// Adds FROM table
function from($table)

// Adds JOIN table ON condition
function join($table, $condition)
// Adds LEFT JOIN table ON condition
function joinLeft($table, $condition)

// Adds JOIN table USING(column)
function joinUsing($table, $column)
// Adds LEFT JOIN table USING(column)
function joinLeftUsing($table, $column)

// Adds WHERE
function where($criteria, $bindParams = null)

// Adds GROUP BY
function group($columns)
// Adds ORDER BY
function order($columns)

// Adds LIMIT
function limit($numrows, $offset = null)
// Convenient way to apply paging
function limitPage($pageNum, $rowsPerPage)

// Runs the Select query, shortcut to coreDatabase::query()
function query()
<?php pre_end() ?>

<h2>Creating a Select object</h2>

<p> You can start building a query with coreDatabase's <b>select()</b> method:

<?php pre_start() ?>
$select = $db->select();
<?php pre_end() ?>

<p> You can use the constructor directly, in this case you must pass a coreDatabase reference:

<?php pre_start() ?>
$select = new coreDatabaseSelect($db);
<?php pre_end() ?>

<h2>Building Select queries</h2>

<p> When building the query, you can add clauses of the query one by one:
<?php pre_start() ?>
// Create the coreDatabaseSelect object
$select = $db->select();

// Add a FROM clause
$select->from(<em>...specify table(s)...</em>);

// Add a WHERE clause 
$select->where(<em>...specify criteria...</em>);

// Add an ORDER BY clause
$select->order(<em>...specify sorting criteria...</em>);
<?php pre_end() ?>

<p> You also can use all methods of the coreDatabaseSelect object with a convenient fluent interface:
<?php pre_start() ?>
$select = $db->select()
             ->from(<em>...specify table(s)...</em>)
             ->where(<em>...specify criteria...</em>)
             ->order(<em>...specify sorting criteria...</em>);
<?php pre_end() ?>

<h2>Adding Columns</h2>

<p> Columns are specified as part of the <b>select()</b> method:

<?php pre_start() ?>
// Select a single column
$db->select( 'firstname' )->from('users');

// Select multiple columns
// => SELECT name,age,occupation FROM users
$db->select( array('name', 'age', 'occupation') )->from('users');
<?php pre_end() ?>

<p> You can also specify the <b>correlation name (also called "alias")</b> for a column.
    Instead of a simple string, use an associative array mapping the correlation name to the table name:
<?php pre_start() ?>
// => SELECT firstname AS fn,age FROM users
$db->select( array('fn' => 'firstname', 'age') )->from('users');
<?php pre_end() ?>

<p> Use the <b>columns()</b> method to change the columns of a query, or to
    specify columns after that part of the query was reset:
<?php pre_start() ?>
// => SELECT lastname FROM users
$select = $db->select( 'firstname' )->from('users');
$select->reset(coreDatabaseSelect::COLUMNS);
$select->columns( 'lastname' );
<?php pre_end() ?>

<h2>Adding Expression Columns</h2>

<p> When specifying columns containing expressions, wrap the expression with the <b>coreDbExpr class</b>
    to indicate that this part of the query should not be quoted:
<?php pre_start() ?>
// => SELECT firstname,age * 2 FROM users
$db->select( array('firstname', new coreDbExpr('age * 2')) )->from( 'users' );
<?php pre_end() ?>


<h2>Adding a FROM clause</h2>

<p> Add a single table, or multiple tables with an array:

<?php pre_start() ?>
// => SELECT * FROM users
$db->select()->from('users');

// => SELECT * FROM users,ratings
$db->select()->from( array('users', 'ratings') );
<?php pre_end() ?>

<p> Like columns, you can specify <b>aliases</b> for tables by using an associative array:

<?php pre_start() ?>
// => SELECT * FROM users AS u,ratings
$db->select()->from( array('u' => 'users', 'ratings' ));
<?php pre_end() ?>

<p> A schema name can appear in the table name:

<?php pre_start() ?>
// => SELECT * FROM users,myschema.products
$db->select()->from( array('users', 'myschema.products') );
<?php pre_end() ?>

<h2>Adding a Table JOIN</h2>

<p> The methods <b>join()</b> and <b>joinLeft()</b> take a table name (optionally with alias) and a criteria:

<?php pre_start() ?>
// => SELECT product_id,product_name 
//      FROM products AS p
//      JOIN line_items AS l ON p.product_id = l.product_id

$db->select(array('product_id', 'product_name'))
   ->from(array('p' => 'products'))
   ->join(array('l' => 'line_items'), 'p.product_id = l.product_id');
<?php pre_end() ?>

<p> Each of the <b>join</b> methods has a corresponding <b>'using'</b> method,
    which uses a column instead of a criteria for the join:

<?php pre_start() ?>
// => SELECT * FROM table1 JOIN table2 USING(column1) WHERE (column2 = 'foo')

$db->select()
   ->from('table1')
   ->joinUsing('table2', 'column1')
   ->where('column2 = ?', 'foo');
<?php pre_end() ?>

<h2>Adding a WHERE Clause</h2>

<p> The <b>where()</b> method takes a SQL expression, and optional bound parameters:

<?php pre_start() ?>
// => SELECT * FROM products WHERE (price > 100)
$db->select()->from('products')->where('price > ?', 100);
<?php pre_end() ?>

<p> You can invoke the where() method <b>multiple times</b> on the same coreDatabaseSelect object.
    The resulting query combines the multiple terms together using AND between them.
	Because coreDatabaseSelect puts <b>parentheses</b> around each where() expression, there
	is no unexpected results from operator precedence:

<?php pre_start() ?>
// => SELECT * FROM products WHERE (price < 100 OR price > 500) AND (category = 'books')
$db->select()->from('products')
   ->where('price < ? OR price > ?', array($price_min, $price_max))
   ->where('category = ?', $category);
<?php pre_end() ?>

<h2>Adding a GROUP BY Clause</h2>

<p> The argument to the <b>group()</b> method is a column or an array of columns to use in the GROUP BY clause:

<?php pre_start() ?>
// => SELECT box,COUNT(*) FROM reviews WHERE (userid = 15) GROUP BY box
$db->select(array('box', new coreDbExpr('COUNT(*)')))
   ->from('reviews')
   ->where('userid = 15')
   ->group('box');
<?php pre_end() ?>	

<h2>Adding an ORDER BY Clause</h2>

<p> The <b>order()</b> method works similarly to the group() method.
    To change the sorting order, simply add the ASC or DESC keyword next to the column name, separated
	by a space:

<?php pre_start() ?>
// => SELECT name,age FROM users ORDER BY age DESC,name
$db->select(array('name', 'age'))
   ->from('users')
   ->order(array('age DESC', 'name'));
<?php pre_end() ?>	

<h2>Adding a LIMIT Clause</h2>

<p> You can use the <b>limit()</b> method to specify the count of rows and the number of rows to skip:

<?php pre_start() ?>
// => SELECT * FROM products AS p LIMIT 20, 10
$select = $db->select()
             ->from(array('p' => 'products'),
                    array('product_id', 'product_name'))
             ->limit(10, 20);
<?php pre_end() ?>	

<p> The <b>limitPage()</b> method provides an alternative way to specify row count and offset:

<?php pre_start() ?>
// => SELECT * FROM products AS p LIMIT 20, 10
$select = $db->select()
             ->from(array('p' => 'products'),
                    array('product_id', 'product_name'))
             ->limitPage(2, 10);
<?php pre_end() ?>	

<h2>Converting the Select object to a String</h2>

<p> To convert a select object to a SQL String, use the <b>__toString()</b> method:

<?php pre_start() ?>
// Note that this is usually not required as passing a coreDatabaseSelect object as an
// argument to  a function taking a string will do the conversion automatically.
$db->select(array('name', 'age'))->from('users')->__toString();
<?php pre_end() ?>	


<h2>Resetting Parts of the Query</h2>

<p> The <b>reset()</b> method enables you to clear one specified part of the SQL query,
    or else clear all parts of the SQL query if you omit the argument:
<?php pre_start() ?>
// Reset the ORDER BY clause
$select->reset( coreDatabaseSelect::ORDER );

// Reset the whole Select
$select->reset();
<?php pre_end() ?>	

<h2>Running the Select Query</h2>

<p> The <b>query()</b> method provides a shortcut to coreDatabase query(). To retrieve rows
	from this query you can use coreDatabase's <b>fetch()</b> and <b>fetchObject()</b> methods:

<?php pre_start() ?>
$data = array();
$db->select(array('name', 'age'))->from('users')->query();
while ($row = $db->fetch()) {
    $data[] = $row;
}
<?php pre_end() ?>     


<!--php
	$db = coreContext::getInstance()->getDatabase();

	echo "<p><samp>" . $db->select('firstname')->where('userid = ?', 5)->query();
    echo "</samp>";

-->