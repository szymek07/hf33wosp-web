<?php

function isDev() {
  return isset($_ENV["ENVIRONMENT"]) && $_ENV["ENVIRONMENT"] == "dev";
}

function getWebPage($url) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "sn32wosp", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    ); 

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);

    $content  = curl_exec($ch);

    curl_close($ch);

    return $content;
}

function removeDuplicateMarkedQsos($array) {
  $tmp_uniq_array = array();
  $uniq_array = array();
  foreach($array as $record) {
    $key = $record['qsoDate'] . $record['band'] . $record['mode'];
    if(!isset($tmp_uniq_array[$key])) {
      $tmp_uniq_array[$key] = $record;
      array_push($uniq_array, $record);
    }
  }
  return $uniq_array;
}

function countPoints($array) {
  $points = 0;
  foreach($array as $record) {
    if($record['mode'] == "CW") $points += 2;
    else $points++;
  }
  return $points;
}

function getOperatorName($array) {
  foreach($array as $record) {
    if($record['name']) return $record['name'];
  }
  return null;
}

function debug_to_console($data, $context = 'Debug in Console') {

    // Buffering to solve problems frameworks, like header() in this and not a solid return.
    ob_start();

    $output  = 'console.info(\'' . $context . ':\');';
    $output .= 'console.log(' . json_encode($data) . ');';
    $output  = sprintf('<script>%s</script>', $output);

    echo $output;
}


?>
