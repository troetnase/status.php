<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 48),
              'ip'     => array ('type' => 'string', 'length' => 22, 'width' => 34),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 9),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 9)
            );

  foreach ($array as $current) {
    $player[] = array (
                  'frags' => intval       (read_string ($current, " ")),
                  'ping'  => intval       (read_string ($current, " ")),

                  'name'  => preg_replace ("/(\^[\da-f]{1}|^\"|\"$)/i", "",
                                           read_string ($current, "\" ")),

                  'ip'    => preg_replace ("/(\^[\da-f]{1}|^\"|\"$)/i", "",
                                           read_string ($current, "\" "))
                );
  };
?>
