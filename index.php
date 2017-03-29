<?php

require_once 'inc/init.php';

$result = get_http_response( REMOTE_URL . PATH );
$data   = $result['content'];

if ( $result['content_type'] == 'text/html' ) {
  handle_links( $data );
}
handle_prices( $data );

header( "Content-Type: {$result['content_type']}" );
echo $data;

