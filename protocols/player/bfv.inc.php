<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 60),
              'score'  => array ('type' => 'int',    'length' => 5,  'width' => 10),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 10),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 12),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 8)
            );

  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");

  read_byte   ($result);

  while ((count ($player)) < $counter) {
    $player[] = array (
                  'name'   => read_string ($result, "\0"),
                  'score'  => read_string ($result, "\0"),
                  'deaths' => read_string ($result, "\0"),
                  'ping'   => read_string ($result, "\0"),
                  'undef'  => read_string ($result, "\0"),
                  'frags'  => read_string ($result, "\0")
                );
  };
?>
