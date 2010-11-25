<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 64),
              'score'  => array ('type' => 'int',    'length' => 5,  'width' => 12),
              'skill'  => array ('type' => 'int',    'length' => 5,  'width' => 12),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 12)
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

      case "score_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['score'] = (int)$value;

          $counter++;
        };

        break;

      case "deaths_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['deaths'] = (int)$value;

          $counter++;
        };

        break;

      case "skill_":
        while (($value = read_string ($result, "\0")) != "") {
          $player[$counter]['skill'] = (int)$value;

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
