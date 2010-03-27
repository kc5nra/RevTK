<?php
/**
 * ExportCSV - Export a database query results to a CSV file.
 * 
 * 
 * @package    
 * @author     Fabrice Denis
 */

class ExportCSV
{
  protected
    $db        = null,
    $options   = array();
    
  const
    LINE_TERMINATED_BY = "\r\n",
    FIELDS_TERMINATED_BY = ",";

  /**
   * 
   * @return 
   */
  public function __construct($db)
  {
    $this->db = $db;
  }
  
  /**
   * Export the query results to the current output buffer.
   * 
   * Options:
   *   col_escape:           Array of booleans, true means to escape as string, false means no escaping
   *   output_callback       Output callback function for ob_start() OPTIONAL (defaults to 'ob_gzhandler')
   *   column_heads          Output column names in the first row OPTIONAL (defaults to true)
   * 
   * @param object $select   Select object
   * @param array  $columns  Array of column names as displayed in CSV,
   *                         must match the number of columns in the select
   * @param array  $options  Options (see above)
   */
  public function export(coreDatabaseSelect $select, $columns, $options = array())
  {
    $this->options = array_merge(array(
      'output_callback'  => 'ob_gzhandler',
      'column_heads'     => true
    ), $options);
    
    ob_start($this->options['output_callback']);

    $numColumns = count($columns);

    if (isset($options['column_heads']) && $options['column_heads']===true) {
      echo implode(self::FIELDS_TERMINATED_BY, $columns) . self::LINE_TERMINATED_BY;
    }

    // what columns to escape as strings
    $escapeCol = isset($options['col_escape']) ? $options['col_escape'] : null;

    $select->query();

    while ($row = $this->db->fetch(coreDatabase::FETCH_NUM))
    {
      $cells = array();
      
      for ($i = 0; $i < $numColumns; $i++)
      {
        $t = $row[$i];
        
        if ($escapeCol!==false && $escapeCol[$i])
        {
          // escape string values
          $t = preg_replace('/\t/', '\t', $t);
          $t = preg_replace('/"/', '""', $t);
          $t = '"' . $t . '"';
        }
  
        $cells[] = $t;
      }
      
      echo implode(self::FIELDS_TERMINATED_BY, $cells) . self::LINE_TERMINATED_BY;
    }

    return ob_get_clean();
  }
}
