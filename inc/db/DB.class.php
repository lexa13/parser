<?php

class DB {
  private $link;

  function __construct() {
    global $db_conf;
    $this->link = mysqli_connect(
      $db_conf['host'],
      $db_conf['user'],
      $db_conf['password'],
      $db_conf['database'],
      $db_conf['port']
    );
    $this->handleError( 'Cannot connect to DB.' );

    // Checking for tables existing
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '{$db_conf['database']}'";
    if ( $this->getVal( $sql ) == 0 ) {
      $this->initDB();
    }
  }

  private function initDB() {
    global $tables;
    echo "DB Initialization...<br />";
    foreach ( $tables as $table_name => $table ) {
      echo "Creating [{$table_name}]...<br />";
      $separator = '';
      $sql       = "CREATE TABLE `parser`.`{$table_name}` (";
      foreach ( $table['columns'] as $col => $desc ) {
        $sql .= "{$separator} `{$col}` $desc";
        if ( $col == $table['primary_key'] ) {
          $sql .= " AUTO_INCREMENT";
        }
        $separator = ",";
      }
      $sql .= ", PRIMARY KEY (`{$table['primary_key']}`)) ENGINE = InnoDB DEFAULT CHARSET=utf8";
      mysqli_query( $this->link, $sql );
      $this->handleError( "Error creating [{$table_name}]." );
      echo "Table [{$table_name}] was created successful.<br />";
      if ( count( $table['values'] ) > 0 ) {
        echo "Inserting default data into [{$table_name}]...<br />";
        $sql       = "INSERT INTO `{$table_name}` (";
        $separator = "";
        foreach ( $table['columns'] as $col => $desc ) {
          if ( $col != $table['primary_key'] ) {
            $sql .= "{$separator} `$col`";
            $separator = ",";
          }
        }
        $sql .= ") VALUES";
        $separator = "";
        foreach ( $table['values'] as $row ) {
          $sql .= "{$separator} ('" . implode( "', '", $row ) . "')";
          $separator = ",";
        }
        mysqli_query( $this->link, $sql );
        $this->handleError( "Error inserting default data into [{$table_name}]." );
      }
    }
    echo "DB was initialized successful. Refresh page.";
    exit;
  }

  private function handleError( $msg ) {
    if ( mysqli_connect_errno() ) {
      echo "Error: {$msg}<br />";
      echo "Error code: " . mysqli_connect_errno() . "<br />";
      echo "Error desc: " . mysqli_connect_error() . "<br />";
      exit;
    }
  }

  private function getRows( $sql ) {
    try {
      $result = mysqli_query( $this->link, $sql );
      if ( $result ) {
        $rows = array();
        while ( $row = mysqli_fetch_assoc( $result ) ) {
          $rows[] = $row;
        }

        return $rows;
      }
    } catch ( Error $err ) {
      $this->handleError( $err );
    }

    return array();
  }

  private function getRow( $sql ) {
    $rows = $this->getRows( $sql . ' LIMIT 1' );

    return $rows[0];
  }

  private function getVal( $sql, $field = false ) {
    $row = $this->getRow( $sql );
    if ( $field ) {
      $val = $row[ $field ];
    } else {
      $row = array_values( $row );
      $val = $row[0];
    }

    return $val;
  }

  function getSettings() {
    $result = $this->getRows( "SELECT param, value FROM settings" );
    $rows   = array();
    foreach ( $result as $row ) {
      $rows[ $row['param'] ] = $row['value'];
    }

    return $rows;
  }

  function getCurrency( $id ) {
    $id = intval( $id );

    return $this->getRow( "SELECT rate, symbol, after FROM currencies WHERE id={$id}" );
  }
}
