<?php
/*
* Enhanced PDO Database Class
* Connect to Database
* Create prepared statements
* Bind Values
* Return rows and results
* Include all utility functions
*/
class Database
{
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $charset = DB_CHARSET;
  private $dbname = DB_NAME;
  private $dbh;
  private $stmt;
  private $error;

  public function __construct()
  {
    // Set DSN
    $dsn = 'mysql:host=' . $this->host . ';charset=' . $this->charset . ';dbname=' . $this->dbname;
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::ATTR_CASE => PDO::CASE_LOWER
    );

    //Create PDO Instance
    try {
      $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
    } catch(PDOException $e) {
      $this->error = $e->getMessage();
      echo $this->error;
    }
  }

  // Core PDO Methods
  public function query($sql)
  {
    $this->stmt = $this->dbh->prepare($sql);
  }

  public function bind($param, $value, $type = null)
  {
    if(is_null($type)){
      switch(true){
        case is_int($value):
          $type = PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = PDO::PARAM_NULL;
          break;
        default:
          $type = PDO::PARAM_STR;
          break;
      }
    }
    $this->stmt->bindValue($param, $value, $type);
  }

  public function execute()
  {
    return $this->stmt->execute();
  }

  public function resultSet()
  {
    $this->execute();
    return $this->stmt->fetchAll();
  }

  public function single()
  {
    $this->execute();
    return $this->stmt->fetch();
  }

  public function rowCount()
  {
    return $this->stmt->rowCount();
  }

  // Insert Methods
  /**
   * InsertRow - Insert a single row into the database
   * @param String $tableName - table name
   * @param array $values - array of values of form array(<columnName> => <value>)
   * @param String $querySuffix - mysql INSERT suffix e.g. 'ON DUPLICATE KEY UPDATE...'
   * @param String $queryPrefix - mysql INSERT prefix e.g. 'IGNORE' for INSERT IGNORE
   * @return bool
   */
  public function InsertRow($tableName, $values, $querySuffix = null, $queryPrefix = null)
  {
    $columns = array_keys($values);
    $fields = '`' . implode('`, `', $columns) . '`';
    $placeholders = ':' . implode(', :', $columns);

    $prefix = $queryPrefix ? $queryPrefix . ' ' : '';
    $suffix = $querySuffix ? ' ' . $querySuffix : '';

    $query = "{$prefix}INSERT INTO `$tableName` ($fields) VALUES ($placeholders){$suffix}";
    $this->query($query);

    foreach($values as $key => $value) {
      $this->bind(":$key", $value);
    }

    return $this->execute();
  }

  /**
   * InsertRows - Insert multiple rows into the database
   * @param string $tableName
   * @param array $rowsValues - array of dictionaries of values for each row
   * @param string $querySuffix - mysql INSERT suffix
   * @param string $queryPrefix - mysql INSERT prefix
   * @return bool
   */
  public function InsertRows($tableName, $rowsValues, $querySuffix = null, $queryPrefix = null)
  {
    if (empty($rowsValues)) {
      return false;
    }

    // Get columns from first row
    $firstRow = reset($rowsValues);
    $columns = array_keys($firstRow);
    $fields = '`' . implode('`, `', $columns) . '`';

    // Create placeholders for all rows
    $rowPlaceholders = [];
    $bindValues = [];
    foreach ($rowsValues as $rowIndex => $rowData) {
      $rowPlaceholder = [];
      foreach ($columns as $column) {
        // Aici este corecția - folosim sintaxa nouă pentru interpolarea variabilelor
        $placeholder = ":{$column}_{$rowIndex}";
        $rowPlaceholder[] = $placeholder;
        $bindValues[$placeholder] = $rowData[$column];
      }
      $rowPlaceholders[] = '(' . implode(', ', $rowPlaceholder) . ')';
    }

    $prefix = $queryPrefix ? $queryPrefix . ' ' : '';
    $suffix = $querySuffix ? ' ' . $querySuffix : '';

    $query = "{$prefix}INSERT INTO `$tableName` ($fields) VALUES " .
      implode(', ', $rowPlaceholders) .
      $suffix;

    $this->query($query);

    foreach ($bindValues as $placeholder => $value) {
      $this->bind($placeholder, $value);
    }

    return $this->execute();
  }

  /**
   * ReplaceRow - Replace a row in the table
   * @param string $tableName - table name
   * @param array $values - array of values of form array(<columnName> => <value>)
   * @return Boolean
   */
  public function ReplaceRow($tableName, $values)
  {
    return $this->InsertRow($tableName, $values, null, 'REPLACE');
  }

  /**
   * ReplaceRows - Replace multiple rows in the table
   * @param string $tableName
   * @param array $rowsValues - array of dictionaries of values for each row
   * @return bool
   */
  public function ReplaceRows($tableName, $rowsValues)
  {
    return $this->InsertRows($tableName, $rowsValues, null, 'REPLACE');
  }

  // Count Methods
  /**
   * CountRow - Count rows in table matching condition
   * @param string $tableName
   * @param mixed $where - condition or 1 for all rows
   * @return int
   */
  public function CountRow($tableName, $where = 1)
  {
    $query = "SELECT COUNT(*) as count FROM `$tableName` WHERE $where";
    $this->query($query);
    $result = $this->single();
    return (int)$result->count;
  }

  /**
   * CountRows - Alias for CountRow
   * @param string $tableName
   * @param mixed $where
   * @return int
   */
  public function CountRows($tableName, $where = 1)
  {
    return $this->CountRow($tableName, $where);
  }

  // Select Methods
  /**
   * SelectRow - Select rows from a table
   * @param string $table Table name
   * @param string $columns Columns to select
   * @param string $where WHERE clause
   * @param string $suffix Additional SQL (ORDER BY, LIMIT, etc)
   * @return array
   */
  public function SelectRow($table, $columns, $where = '1', $suffix = '')
  {
    $query = "SELECT $columns FROM `$table` WHERE $where $suffix";
    return $this->executeQuery($query);
  }

  /**
   * Executes a given query and returns the rows
   * @param String $query - mysql query
   * @return array
   */
  public function executeQuery($query)
  {
    $this->query($query);
    return $this->resultSet();
  }

  /**
   * Check if a row exists
   * @param string $tableName
   * @param string $where
   * @return Boolean
   */
  public function RowExists($tableName, $where = '1')
  {
    $query = "SELECT 1 FROM `$table` WHERE $where LIMIT 1";
    $this->query($query);
    return $this->rowCount() > 0;
  }

  // Update Methods
  /**
   * UpdateRow - Update a row in the table
   * @param String $tableName
   * @param array $values
   * @param String $where
   * @return Boolean
   */
  public function UpdateRow($tableName, $values, $where = '1')
  {
    $fields = array();
    foreach($values as $key => $value) {
      $fields[] = "`$key` = :$key";
    }
    $setClause = implode(', ', $fields);

    $query = "UPDATE `$tableName` SET $setClause WHERE $where";
    $this->query($query);

    foreach($values as $key => $value) {
      $this->bind(":$key", $value);
    }

    return $this->execute();
  }

  // Delete Methods
  /**
   * DeleteRow - Delete a row from database
   * @param string $tableName
   * @param string $where
   * @return Boolean
   */
  public function DeleteRow($tableName, $where = '1')
  {
    $query = "DELETE FROM `$tableName` WHERE $where";
    return $this->executeNonQuery($query);
  }

  // Utility Methods
  public function GetLastInsertId($tableName = null)
  {
    return $this->dbh->lastInsertId($tableName);
  }

  public function GetError()
  {
    return $this->error;
  }

  public function FormatSqlString($value)
  {
    return $this->dbh->quote($value);
  }

  public static function FormatSqlDate($timestamp)
  {
    return date('Y-m-d', $timestamp);
  }

  public static function FormatSqlDatetime($timestamp)
  {
    return date('Y-m-d H:i:s', $timestamp);
  }

  public function executeNonQuery($query)
  {
    $this->query($query);
    return $this->execute();
  }

  public function GetAffectedRows()
  {
    return $this->rowCount();
  }
}