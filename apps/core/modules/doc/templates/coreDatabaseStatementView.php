<h2>coreDatabaseStatement</h2>

<p> Use coreDatabaseStatement to run prepared statements.
<p> The interface is the same as Zend_Db_Statement but named parameters are not supported.

<?php pre_start() ?>
// Create a statement object
function __construct(coreDatabase $adapter, $sql)

// Run a prepared statement, with optional parameters
// @return TRUE on success or FALSE on failure. 
function execute(array $params = null)

<?php pre_end() ?>

<h2>Creating a executing a prepared Statement</h2>

<?php pre_start() ?>
$db = coreContext::getInstance()->getDatabase();
$stmt = new coreDatabaseStatementMySQL(
  "INSERT users (username, userlevel) VALUES (?, ?)");
$stmt->execute('Sam Clemens', 1);
$stmt->execute('Richard Burton', 2);
<?php pre_end() ?>
