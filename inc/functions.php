<?php

function get_http_response( $url ) {
  $options = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_AUTOREFERER    => true,
    CURLOPT_CONNECTTIMEOUT => 90,
    CURLOPT_TIMEOUT        => 90,
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_SSL_VERIFYPEER => false
  );

  $curl = curl_init( $url );
  curl_setopt_array( $curl, $options );
  $content = curl_exec( $curl );
  $header  = curl_getinfo( $curl );
  curl_close( $curl );

  $header['content'] = $content;

  return $header;
}

function handle_links(&$html) {
  $dom = new DOMDocument;
  $dom->loadHTML( $html );
  $links = $dom->getElementsByTagName( 'a' );
  foreach ( $links as $link ) {
    $remote_link = parse_url( $link->getAttribute( 'href' ) );
    $link->setAttribute( 'href', LOCAL_URL . $remote_link['path'] );
  }
  $html = $dom->saveHTML();
}

function handle_prices( &$html ) {
  $reg_price = '((?!0\.00)[1-9]\d{0,2}( \d{3})*(\,\d\d)?)';
  $html      = preg_replace_callback(
    array(
      '/€&nbsp;' . $reg_price . '/',
      '/€ ?' . $reg_price . '/',
      '/' . $reg_price . ' ?€/',
    ),
    function ( $matches ) {
      global $currency;

      return
        ( ! $currency['after'] ? $currency['symbol'] : '' ) .
        number_format( $matches[1] * $currency['rate'], 2, '.', ' ' ) .
        ( $currency['after'] ? " {$currency['symbol']}" : '' );
    },
    $html
  );
}
