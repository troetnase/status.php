<?php
  $header  = array (
               'name'  => array ('type' => 'string', 'length' => 32, 'width' => 80),
               'ping'  => array ('type' => 'int',    'length' => 4,  'width' => 20)
             );

  while ($counter < $cur) {
    $flags            = (int)ord (read_byte ($result));

    $player[$counter] = array ();

    if ($flags & 1) {
      $player[$counter]['name'] = parse_ase ($result);
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
