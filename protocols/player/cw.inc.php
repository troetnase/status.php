<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 64),
              'kills'  => array ('type' => 'int',    'length' => 5,  'width' => 12),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 14),
              'rank'   => array ('type' => 'int',    'length' => 4,  'width' => 10)
            );

  read_byte ($result);

  while (($head = read_string ($result, "\0")) != "") {
    read_byte ($result);

    $counter = 0;

    switch (strtolower ($head)) {
      case "player_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['name'] = $value;

          $counter++;
        };

        break;

      case "deaths_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['deaths'] = (int)$value;

          $counter++;
        };

        break;

      case "rank_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['rank'] = (int)$value;

          $counter++;
        };

        break;

      case "kills_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['kills'] = (int)$value;

          $counter++;
        };

        break;

      default:
        while (($value = read_string ($result, "\0")) != "") {
          $counter++;
        };

        break;
    };
  };
?>
