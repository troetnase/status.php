<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 80),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 20)
            );

  foreach ($array as $current) {
    $settings = preg_split ("/\s/", $current, 3);

    $player[] = array (
                  'name'  => preg_replace ("/(\^[\da-f]{1}|^\"|\"$)/i", "", $settings[1]),
                  'ping'  => $settings[0]
                );
  };
?>
