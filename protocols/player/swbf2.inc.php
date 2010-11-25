<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 57),
              'score'  => array ('type' => 'int',    'length' => 5,  'width' => 9),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 9),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 11),
              'hero'   => array ('type' => 'int',    'length' => 4,  'width' => 7),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 7)
            );

  read_string ($result, "\0");
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
                  'frags'  => read_string ($result, "\0"),
                  'hero'   => read_string ($result, "\0")
                );
  };
?>
