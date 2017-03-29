<?php

$tables = array(
  'settings'   => array(
    'columns'     => array(
      'id'    => 'int(11) NOT NULL',
      'param' => 'varchar(20) NOT NULL',
      'value' => 'varchar(30) DEFAULT NULL ',
    ),
    'values'      => array(
      array( 'local_url', 'http://parser.localhost' ),
      array( 'remote_url', 'https://otto.de' ),
      array( 'currency', '1' ),
      array( 'error_level', 0 ),
    ),
    'primary_key' => 'id'
  ),
  'currencies' => array(
    'columns'     => array(
      'id'     => 'INT NOT NULL AUTO_INCREMENT',
      'rate'   => 'FLOAT(5,3) NOT NULL',
      'symbol' => 'VARCHAR(10) NOT NULL',
      'after'  => 'TINYINT NOT NULL',
    ),
    'values'      => array(
      array( 29.308, 'UAH', 1 ),
    ),
    'primary_key' => 'id'
  )
);
