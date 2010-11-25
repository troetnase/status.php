<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 34),
              'guid'   => array ('type' => 'string', 'length' => 34, 'width' => 36),
              'score'  => array ('type' => 'int',    'length' => 5,  'width' => 6),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 6),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 7),
              'honor'  => array ('type' => 'int',    'length' => 5,  'width' => 6),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 5)
            );

  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");
  read_string ($result, "\0");

  read_byte   ($result);

  while ((count ($player)) < $counter) {
    read_string ($result, "\0");

    $player[] = array (
                  'goal'   => read_string ($result, "\0"),
                  'honor'  => read_string ($result, "\0"),
                  'name'   => read_string ($result, "\0"),
                  'ping'   => read_string ($result, "\0"),
                  'roe'    => read_string ($result, "\0"),
                  'deaths' => read_string ($result, "\0"),
                  'frags'  => read_string ($result, "\0"),
                  'score'  => read_string ($result, "\0"),
                  'guid'   => read_string ($result, "\0")
                );
  };
?>
