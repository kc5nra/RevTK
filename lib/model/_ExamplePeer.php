<?php
/**
 * This is a coreDatabaseTable peer template.
 * 
 * @author  Fabrice Denis
 */

class ExamplePeer extends coreDatabaseTable
{
  protected
    $tableName = 'Example',
    $columns = array
    (
      'id',
      'username',
      'password'
    );

  /**
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

}
