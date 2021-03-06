<?php
  $header  = array (
               'name'  => array ('type' => 'string', 'length' => 32, 'width' => 68),
               'frags' => array ('type' => 'int',    'length' => 5,  'width' => 12),
               'ping'  => array ('type' => 'int',    'length' => 4,  'width' => 10),
               'time'  => array ('type' => 'int',    'length' => 4,  'width' => 10)
             );

  while ($counter < $cur) {
    $flags            = (int)ord (read_byte ($result));

    $player[$counter] = array ();

    if ($flags & 1) {
      $player[$counter]['name'] = preg_replace ("/\\\$[\d]{1}/",
                                                "", parse_ase ($result));
    };

    if ($flags & 2) {
      $player[$counter]['team'] = parse_ase ($result);
    };

    if ($flags & 4) {
      $player[$counter]['skin'] = parse_ase ($result);
    };

    if ($flags & 8) {
      $player[$counter]['frags'] = parse_ase ($result);
    };

    if ($flags & 16) {
      $player[$counter]['ping'] = parse_ase ($result);
    };

    if ($flags & 32) {
      $player[$counter]['time'] = parse_ase ($result);
    };

    $counter = $counter + 1;
  };
?>
