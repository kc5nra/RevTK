<?php
/**
 * MySQL implementation of the database abstraction layer.
 * 
 * Custom public methods (may be merged into coreDatabase.php):
 * 
 *   getDebugLog()   Returns log of SQL querries (requires const LOG_QUERIES set true)
 * 
 * 
 * @author  Fabrice Denis
 * @package Core
 */

class coreDatabaseMySQL extends coreDatabase
{
  protected
    $fetchMode   = coreDatabase::FETCH_ASSOC,
    $result      = null,
    $connection  = null;

  public function connect()
  {
    // get parameters
    $database = $this->getParameter('database');
    $host     = $this->getParameter('host', 'localhost');
    $password = $this->getParameter('password');
    $username = $this->getParameter('username');

    $this->connection = @mysqli_connect($host, $username, $password);

    // select our database
    if ($this->connection===false || ($database != null && !@$this->connection->select_db($database)))
    {
      // can't select the database
      throw new coreDatabaseException(sprintf('Failed to connect MySQLDatabase "%s".', $database));
    }

    // remnant of old code..
    if ($this->getParameter('set_names_utf8'))
    {
      $this->connection->query("SET NAMES 'utf8'");
     }
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function query($sql, $bindParams = null)
  {
    if ($bindParams!== null)
    {
      $sql = $this->bind($sql, $bindParams);
    }

    // echo query but do not execute it
    if (self::LOG_QUERIES)
    {
      $this->logQuery($sql);
    }

    $this->result = $this->connection->query($sql);
    if ($this->result === false)
    {
      throw new coreDatabaseException('SQL Query Failed: '.$sql);
    }
    return ($this->result!==false);
  }

  public function num_rows()
  {
    return mysqli_num_rows($this->result);
  }
  
  public function fetch($fetchMode = null)
  {
    if ($fetchMode===null) {
      $fetchMode = $this->fetchMode;
    }

    switch ($fetchMode)
    {
      case self::FETCH_NUM:
        $result = @mysqli_fetch_array($this->result, MYSQLI_NUM);
        break;
      case self::FETCH_OBJ:
        $result = @mysqli_fetch_object($this->result);
        break;
      case self::FETCH_ASSOC:
      default:  
        $result = @mysqli_fetch_array($this->result, MYSQLI_ASSOC);
        break;
    }

    return $result;
  }
  
  public function fetchObject($class = 'stdClass', array $params = array())
  {
    $obj = new $class($params);
    $row = $this->fetch(self::FETCH_ASSOC);
    if (!is_array($row)) {
        return false;
    }
    foreach ($row as $key => $val) {
        $obj->$key = $val;
    }
    return $obj;
  }
  
  public function select($columns = null)
  {
    return new coreDatabaseSelect($this, $columns);
  }

  public function fetchOne($query, $bindParams = null)
  {
    $this->query($query , $bindParams);
    $row = $this->fetch(self::FETCH_NUM);
    if (!is_array($row)) {
      return false;
    }
    return $row[0];
  }

  public function fetchRow($query, $bindParams = null)
  {
    $this->query($query, $bindParams);
    return $this->fetch();
  }

  public function fetchAll($query, $bindParams = null)
  {
    $this->query($query, $bindParams);
    $data = array();
    while ($row = $this->fetch())
    {
      $data[] = $row;
    }
    return $data;
  }

  public function fetchCol($query, $bindParams = null)
  {
    $this->query($query, $bindParams);
    $data = array();
    while ($row = $this->fetch(self::FETCH_NUM))
    {
      $data[] = $row[0];
    }
    return $data;
  }

  public function insert($table, $data = array())
  {
    $values = $this->chain($data);
    $q = "INSERT {$table} SET {$values}";
    $result = $this->query($q);
    return $result;
  }

  public function lastInsertId()
  {
    return mysqli_insert_id($this->connection);
  }

  public function update($table, $data, $where = null, $bindParams = null)
  {
    $values = $this->chain($data);
    $q = "UPDATE {$table} SET {$values}";
    if ($where !== null) {
      $q .= ' WHERE ' . $this->bind($where, $bindParams);
    }
    $result = $this->query($q);
    return $result;
  }

  public function delete($table, $where = null, $bindParams = null)
  {
    $q = "DELETE FROM {$table}";
    if ($where !== null) {
      $q .= ' WHERE ' . $this->bind($where, $bindParams);
    }
    $result = $this->query($q);
    return $result;
  }

  /**
   * Safely quote a parameter for the SQL string, do not quote integers and coreDbExpr instances.
   * 
   * If an array is passed as the value, the array values are quoted
   * and then returned as a comma-separated string.
   *
   * @param  mixed  $value The value to quote.
   * 
   * @return mixed  An SQL-safe quoted value (or string of separated values).
   */
  public function quote($value)
  {
    if ($value instanceof coreDbExpr)
    {
      return $value->__toString();
    }

    if (is_array($value))
    {
      foreach ($value as &$val)
      {
        $val = $this->quote($val);
      }
      return implode(', ', $value);
    }
        
    return $this->_quote($value);
  }

  private function _quote($value)
  {
    if (is_int($value) || is_float($value))
    {
      return $value;
    }
    return '\'' . $this->connection->real_escape_string($value) . '\'';
  }

  /**
   * Transform a hash into a SQL string of key and value assignments : "key=value,key=value,(...)"
   * @return string
   * @param $fields Object
   */
  public function chain($fields)
  {
    assert('is_array($fields)');
    $a = array();
    foreach ($fields as $key => $value) {
      $a[] = $key . '=' . $this->quote($value);
    }
    $s = implode(',', $a);
    return $s;
  }

  /**
   * Aliases creates a SQL string of selected columns or expressions, key => value pairs become aliases.
   * eg. "column1 AS c1,NOW() AS thetimeisnow"
   *
   * @param $query Object
   * @param $values Object
   */
  public function aliases($columns)
  {
    // single column
    if (is_string($columns)) {
      return $columns;
    }

    // mutliple columns or expressions
    $parts = array();
    foreach ($columns as $aliasExpr => $fullExpr)
    {
      if (is_string($aliasExpr)) {
        $parts[] = $fullExpr . ' AS ' . $aliasExpr;
      }
      else {
        assert('!is_array($fullExpr)');
        $parts[] = $fullExpr;
      }
    }
    return implode(',', $parts);
  }

  /**
   * Bind and quote parameters into a query string, each '?' character is bound to one parameter
   *
   * @param $query Object
   * @param $params Object
   */
  public function bind($query, $bindParams)
  {
    if (is_null($bindParams))
    {
      return $query;
    }
    
    if (!is_array($bindParams))
    {
      // dont cast to (array) because of coreDbExpr
      $bindParams = array($bindParams);
    }

    // replace each '?' with corresponding parameter
    $parts = preg_split('/\?/', $query, count($bindParams)+1);
    $query = array_shift($parts);
    if (count($parts)!==count($bindParams)) {
      throw new coreDatabaseException("coreDatabase->bind() - Invalid number of parameters.");
    }
    while (count($parts)>0) {
      $query = $query . $this->quote(array_shift($bindParams)) . array_shift($parts);
    }
    return $query;
  }

  /**
   * Returns a SQL statement which returns a date+time adjusted to the
   * timezone of the user ($session->timezone).
   * 
   * The date returned by this statement will switch at midnight time
   * of the user's timezone (assuming the user set the timezone properly).
   * (the user's timezone range is -12...+14)
   * 
   * @param string  If set, 
   * 
   * @todo  Move to RevTK extension of core class
  */
  public function localTime($column = 'NOW()')
  {
    $timezone = coreContext::getInstance()->get('auth')->getTimezone();
    $timediff = $timezone - coreConfig::get('server_timezone', 0);
    $hours = floor($timediff);
    $minutes = ($hours != $timediff) ? '30' : '0';  // some timezones have half-hour precision, convert to minutes
  
    $s = sprintf('ADDDATE(%s, INTERVAL \'%d:%d\' HOUR_MINUTE)', $column, $hours, $minutes);
    return $s;
  }

  /**
   * Echoes a resultset in html table.
   * 
   * @return 
   * @param object $resultset
   */
  public function dumpResultSet($resultset)
  {
    // display column names
    echo '<table cellspacing="1" style="border:1px solid #B3DCFF;border-collapse:collapse;">';
    echo '<tr class="head">';
    $numfields = mysqli_num_fields($this->result);
    $colNames = array();
    while ($finfo = mysqli_fetch_field($this->result))
    {
      $colNames[] = $finfo->name;
      echo '<th style="border:1px solid #B3DCFF;padding:2px 9px;">' . $finfo->name . '</th>';
    }

    echo "</tr>\n";

    // single value
    if (gettype($resultset)!=='object' && gettype($resultset)!=='array')
    {
      echo '<tr><td style="background:#DFF1FF;padding:2px 9px;border:1px solid #B3DCFF;">' . $resultset . '</td></tr></table>';
      return;
    }

    // single row
    if ( (is_array($resultset) && !array_key_exists('0', $resultset)) ||
       (is_object($resultset)) )
    {
      $resultset = array($resultset);
    }

    // display table contents
    $emptyCellHtml = "<td bgcolor=\"#B3DCFF\">&nbsp;</td>";
    $tdBegin = '<td style="background:#DFF1FF;padding:2px 9px;border:1px solid #B3DCFF;">';
    $tdEnd = '</td>';
    foreach ($resultset as $rowdata)
    {
      echo "<tr>";
      
      for ($i=0; $i<$numfields; $i++)
      {
        $colvalue = gettype($rowdata);
        if (is_array($rowdata)){
          $colvalue = isset($rowdata[$i]) ? $rowdata[$i] : $rowdata[$colNames[$i]];
        }else if (is_object($rowdata)){
          $colvalue = $rowdata->$colNames[$i];
        }

        $cellHtml = $colvalue!==null ? $tdBegin.$colvalue.$tdEnd : $emptyCellHtml;

        echo $cellHtml;
      }
      echo "</tr>\n";
    }
    echo "</table>\n";
  }
}

/**
 * Extends for Mysqli
 *
 * @author     Fabrice Denis
 * @package    Core
 * @subpackage Database
 */
class coreDatabaseStatementMySQL extends coreDatabaseStatement
{
  /**
   * The mysqli_stmt object.
   *
   * @var mysqli_stmt
   */
  protected $_stmt = null;

  /**
   * @param  string $sql
   * @return void
   * @throws coreDatabaseException
   */
  public function _prepare($sql)
  {
    $mysqli = $this->_adapter->getConnection();

    $this->_stmt = $mysqli->prepare($sql);

    if ($this->_stmt === false || $mysqli->errno)
    {
      throw new coreDatabaseException("Mysqli prepare error: " . $mysqli->error);
    }
  }  

  /**
   * Executes a prepared statement.
   *
   * @param  array $params OPTIONAL Values to bind to parameter placeholders.
   * @return bool
   * @throws coreDatabaseException
   */
  public function _execute(array $params = null)
  {
    if (!$this->_stmt)
    {
        return false;
    }

    // if no params were given as an argument to execute(),
    // then default to empty array
    if ($params === null) {
        $params = array();
    }

    // send $params as input parameters to the statement
    if ($params)
    {
      array_unshift($params, str_repeat('s', count($params)));
      $stmtParams = array();
      foreach ($params as $k => &$value) {
        $stmtParams[$k] = &$value;
      }
      call_user_func_array(array($this->_stmt, 'bind_param'), $stmtParams);
    }

    // execute the statement
    $retval = $this->_stmt->execute();
    if ($retval === false)
    {
      throw new coreDatabaseException("Mysqli statement execute error : " . $this->_stmt->error);
    }
    
    return $retval;
  }

  /**
   * Returns the number of rows affected by the last INSERT, UPDATE,
   * REPLACE or DELETE query.
   *
   * For SELECT statements mysqli_affected_rows() works like mysqli_num_rows()
   *
   * @return int     The number of rows affected.
   */
  public function rowCount()
  {
    if (!$this->_adapter) {
        return false;
    }
    $mysqli = $this->_adapter->getConnection();
    return $mysqli->affected_rows;
  }
}
