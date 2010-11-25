<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 70),
              'score'  => array ('type' => 'int',    'length' => 5,  'width' => 15),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 15)
            );

  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");

  read_byte   ($result);

  while ((count ($player)) < $counter) {
    $player[] = array (
                  'name'   => read_string ($result, "\0"),
                  'score'  => read_string ($result, "\0"),
                  'ping'   => read_string ($result, "\0")
                );
  };
?>
