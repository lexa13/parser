<?php

include_once 'config.php';
include_once 'functions.php';
include_once 'db/DB.class.php';
include_once 'db/db_schema.php';

$db = new DB();

$settings = $db->getSettings();
error_reporting( $settings['error_level'] );

$currency = $db->getCurrency( $settings['currency'] );

define( 'LOCAL_URL', $settings['local_url'] );
define( 'REMOTE_URL', $settings['remote_url'] );
define( 'PATH', $_SERVER['REQUEST_URI'] );
define( 'CONTENT_TYPE', $_SERVER[''] );

if ( ! class_exists( 'DOMDocument' ) ) {
  echo "Class 'DOMDocument' does not exsist.";
  exit;
}
