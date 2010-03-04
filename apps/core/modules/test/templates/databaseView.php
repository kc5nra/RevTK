<h2>Database Tests</h2>

<p> This page is for testing the database layer, see tests in template file.

<?php
/**
 * Database test follow below.
 * 
 */	

//withoutStatementTest();
//withStatementTest();

//echo '<code style="display:block">' . $select . '</code>';


/**
 * Add 1000 user records in dummy database, WITH prepared statement.
 *  
 *
 * @return 
 */
function withStatementTest()
{
	$db = coreContext::getInstance()->getDatabase();

	$stmt = new coreDatabaseStatementMySQL($db,
		"INSERT users (username, userlevel, joindate) VALUES (?, ?, NOW())");

	$db->query("LOCK TABLES users WRITE");
	
	for ($i = 0; $i < 1000; $i++)
	{
		$s = 'framenum' . $i;
		$level = $i & 0xF;
		$stmt->execute(array($s, $level));
	}
	
	$db->query("UNLOCK TABLES");
}

/**
 * Add 1000 user records in dummy database WITHOUT prepared statement: avg. 700ms
 * With table locking, avg. 660ms (minimal difference!)
 * 
 * @return 
 */
function withoutStatementTest()
{
	$db = coreContext::getInstance()->getDatabase();

	$db->query("LOCK TABLES users WRITE");
	
	for ($i = 0; $i < 1000; $i++)
	{
		$s = 'framenum' . $i;
		$level = $i & 0xF;
		$db->query("INSERT users (username, userlevel, joindate) VALUES (?, ?, NOW())", array($s, $level));
	}
	
	$db->query("UNLOCK TABLES");
}



// Initialize testing
//$test = new coreTest('Database Abstraction And ORM');
//$test->echoStyles();
//selectTests($db, $test);
//fetchTests($db, $test);
//dataManipulationTests($db, $test);
//ormTests($db, $test);

function selectTests($db, $test)
{
	$query = $db->select()->from('TestTable');
	$test->out($query, "select() with no specified columns defaults to '*'");

	$query = $db->select(array('t.age', 't.firstname'))->from(array('t' => 'TestTable'));
	$test->out($query, "aliased table");

	$query = $db->select('single_column')->from('TestTable');
	$test->out($query, "single column");

	$query = $db->select(array('multiple','columns'))->from('TestTable');
	$test->out($query, "multiple columns");

	// mutliple where clauses
	$query = $db->select(array('multiple','columns'))
			->from('TestTable')
			->where('id = ? AND age>?', array(15, 33));
	$test->out($query, "multiple where clauses");

	// group by
	$query = $db->select()->from('TestTable')
				->group('group_col');
	$test->out($query , "group");

	// order by single
	$query = $db->select()->from('TestTable')
				->order('order_col1 ASC');
	$test->out($query , "order single");

	// order by multiple
	$query = $db->select()->from('TestTable')
				->order(array('order_col1 ASC', 'order_col2 DESC'));
	$test->out($query , "order multiple");

	// selected expression and alias
	$query = $db->select(array('productid', 'cost_plus_tax' => 'p.cost * 1.08'))
				->from('TestTable');
	$test->out($query, "selected expression, alias, order");
	
	// join
	$query = $db->select()->from(array('t' => 'TestTable'))
			->join('table2', 't.id = table2.id');
	$test->out($query, "join");

	// joinUsing
	$query = $db->select()->from(array('t' => 'TestTable'))
			->joinUsing('table2', 'id');
	$test->out($query, "joinUsing");

	// joinLeft
	$query = $db->select()->from(array('t' => 'TestTable'))
			->joinLeft('table2', 't.id = table2.id');
	$test->out($query, "joinLeft");

	// joinLeftUsing
	$query = $db->select()->from(array('t' => 'TestTable'))
			->joinLeftUsing('table2', 'id');
	$test->out($query, "joinLeftUsing");

	// where single
	$query = $db->select()->from('TestTable')
			->where('firstname = ?', 'John d\'Oe "le Quoted"');
	$test->out($query, "single where, quotes are escaped");

	// multiple where() calls
	$query = $db->select()->from('TestTable')
			->where('a = ?', 1)
			->where('b != ?', 2);
	$test->out($query, "multiple where() calls");

	// limit
	$query = $db->select()->from('TestTable')
			->limit(10, 4);
	$test->out($query, "limit(10, 4)");

	// limitPage
	$query = $db->select()->from('TestTable')
			->limitPage(2, 10);
	$test->out($query, "limitPage(2, 10)");

	// reset and replace part of previous query
	$query = $db->select()->from('TestTable')
			->limitPage(2, 10);
	$query->reset(coreDatabaseSelect::COLUMNS);
	$query->columns(array('snoopy', 'count' => 'COUNT(*)'));
	$query->limit(2008);
	$test->out($query, "reset() and columns() replacement");
	
	// create a select directly without passing through Database
	$query = new coreDatabaseSelect($db);
	$query->columns(array('firstname','age'));
	$query->from('super_clients');
	$test->out($query, "create a select directly");
}

function fetchTests($db, $test)
{
	echo "<h2>fetchOne</h2>";
	
	$R = $db->fetchOne($db->select('COUNT(*)')->from('users'));
	$db->dumpResultSet($R);

	echo "<h2>fetchRow</h2>";

	$row = $db->fetchRow($db->select()->from('users')->where('userid=?', 15));
	$test->out(print_r($row,true));
	$db->dumpResultSet(array($row));

	echo "<h2>fetchRowAssoc</h2>";

	$row = $db->fetchRowAssoc($db->select()->from('users')->where('userid = ? AND username= ?', array(15, 'fuaburisu')));
	$test->out(print_r($row,true));
	$db->dumpResultSet(array($row));

	echo "<h2>fetchAll</h2>";

	$rows = $db->fetchAll($db->select()->from('users')->where('userid >= 10 AND userid <= ?', 15));
	$test->out(print_r($rows,true));
	$db->dumpResultSet($rows);

	echo "<h2>fetchAllAssoc</h2>";

	$rows = $db->fetchAllAssoc($db->select()->from('users')->where('userid >= 10 AND userid <= ?', 15));
	$test->out(print_r($rows,true));
	$db->dumpResultSet($rows);
}

function dataManipulationTests($db, $test)
{
	echo "<h2>insert</h2>";
	echo "<strong>Insert 1</strong><br>";
	$db->insert('_test1', array(
		'firstname' => 'Blobby',
		'age' => 28,
		'created_on' => '2001-11-11'
		
	));
	
	echo "<strong>Insert 2</strong><br>";
	$db->insert('_test1', array(
		'firstname' => 'Fabrice',
		'age' => 34,
		'created_on' => new coreDbExpr('CURRENT_TIMESTAMP')
	));

	echo "Last insert id = " . $test->out(print_r($db->lastInsertId(), true));

	$rows = $db->fetchAll($db->select()->from('_test1'));
	$db->dumpResultSet($rows);

	
	echo "<h2>update</h2>";

	$data = array('age' => 100);
	$db->update('_test1', $data, 'id = ? AND firstname = ?', array(3, 'Blobby'));

	// update with expression
	$data = array('age' => new coreDbExpr('age * 2'));
	$db->update('_test1', $data, 'id = ? AND firstname = ?', array(4, 'Fabrice'));

	// check changes
	$rows = $db->fetchAll($db->select()->from('_test1'));
	$db->dumpResultSet($rows);

	echo "<h2>delete</h2>";
	$db->delete('_test1', 'id = ?', 3);
}

/**
 * Run some coreDatabaseTable tests (ORM)
 * 
 * @param object $db
 * @param object $test
 */
function ormTests($db, $test)
{
	// Testing method access to the table peer attributes

	TestOrmPeer::exampleMethod($test);

	// Testing table data access through the peer class:
	echo "<h2>Select</h2>";

	//$db->setFetchMode(coreDatabase::FETCH_NUM);	
	$rows = $db->fetchAll(TestOrmPeer::select());
	$db->dumpResultSet($rows);

	echo "<h2>Count</h2>";

	$test->out(TestOrmPeer::count('id > ?', 24), "count('id > 24')");

	echo "<h2>Insert</h2>";

	TestOrmPeer::insert(array
	(
		'firstname' => 'Superman',
		'age' => 28
	));

	// show new row
	$id = $db->lastInsertId();
	echo '<p> Inserted new row with id: <b>'.$id.'</b>:';
	$rows = $db->fetchRow(TestOrmPeer::select()->where('id = ?', $id));
	$db->dumpResultSet($rows);

	echo "<h2>Update</h2>";

	// delay 1 second to see the UPDATED_ON changes
	sleep(1);

	TestOrmPeer::update(array
	(
		'firstname' => 'She-Ra',
		'age' => 31
	), 'id = ?', $id);

	// show update results
	echo '<p> Updated row with id: <b>'.$id.'</b> (1 second delayed):';
	$rows = $db->fetchRow(TestOrmPeer::select()->where('id = ?', $id));
	$db->dumpResultSet($rows);

	echo "<h2>Delete</h2>";

	echo '<p> Deleted row with id: <b>'.$id.'</b>.';

	TestOrmPeer::delete('id >= ?', 27);
}
