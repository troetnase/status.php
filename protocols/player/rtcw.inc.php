<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 70),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 15),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 15)
            );

  foreach ($array as $current) {
    $settings = preg_split ("/\s/", $current, 3);

    $player[] = array (
                  'name'  => preg_replace ("/(\^[\da-f]{1}|^\"|\"$)/i", "", $settings[2]),
                  'frags' => $settings[0],
                  'ping'  => $settings[1]
                );
  };
?>
