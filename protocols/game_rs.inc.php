<?php
  $protocol  = "rs";

  $variables = array (
                 'a1' => 'maxplayer',
                 'a2' => 'tk penalty',
                 'b1' => 'numplayer',
                 'b2' => 'radar',
                 'd2' => 'version',
                 'e1' => 'map',
                 'f1' => 'gametype',
                 'g1' => 'locked',
                 'h1' => 'dedicated',
                 'i1' => 'name',
                 'q1' => 'rounds',
                 'r1' => 'roundtime',
                 't1' => 'bomb timer',
                 'y1' => 'friendly fire',
                 'z1' => 'team balance'
               );

  if ($PLAYER === true) {
    $header = array (
                'name'   => array ('type' => 'string', 'length' => 32, 'width' => 64),
                'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 10),
                'ping'   => array ('type' => 'int',    'length' => 5,  'width' => 10),
                'time'   => array ('type' => 'int',    'length' => 8,  'width' => 16)
              );
  };

  $send      = "REPORT";
  $response  = "/^rvnshld\x20{$port}\x20KEYWORD\x20\x20\xB6|\/{2,}/";

  $result    = server_query ($socket, $address, $queryport,
                             $send, $protocol, $response);

  if (! empty ($result)) {
    while (($variable = read_string ($result, " ")) != "") {
      $variable = strtolower  ($variable);
      $value    = read_string ($result, (chr (0x20) . chr (0xB6)));

      if ($RULES === true) {
        if ((@array_key_exists ($variable, $variables)) === true) {
          rule_add ($rules, $variables[$variable], $value);
        };
      };

	    switch ($variable) {
	      case "i1":
          $hostname = $value;

          break;

        case "g1":
          if ((int)$value == 1) {
            $pass = true;
          } else {
            $pass = false;
          };

          break;

        case "b1":
          $cur = (int)$value;

          break;

        case "a1":
          $max = (int)$value;

          break;

        case "e1":
          $map = $value;

          break;

        case "l1":
          if ($PLAYER === true) {
            if ((strlen ($value)) > 0) {
              read_byte ($value);

              $counter = 0;

              while (($name = read_string ($value, "/")) != "") {
                $player[$counter]['name'] = $name;

                $counter                  = $counter + 1;                
              };
            };
          };

          break;

        case "o1":
          if ($PLAYER === true) {
            if ((strlen ($value)) > 0) {
              read_byte ($value);

              $counter = 0;

              while (($frags = read_string ($value, "/")) != "") {
                $player[$counter]['frags'] = $frags;

                $counter                  = $counter + 1;                
              };
            };
          };

          break;

        case "m1":
          if ($PLAYER === true) {
            if ((strlen ($value)) > 0) {
              read_byte ($value);

              $counter = 0;

              while (($time = read_string ($value, "/")) != "") {
                $player[$counter]['time'] = get_rs_time ($time);

                $counter                  = $counter + 1;                
              };
            };
          };

          break;

        case "n1":
          if ($PLAYER === true) {
            if ((strlen ($value)) > 0) {
              read_byte ($value);

              $counter = 0;

              while (($ping = read_string ($value, "/")) != "") {
                $player[$counter]['ping'] = $ping;

                $counter                  = $counter + 1;                
              };
            };
          };

          break;
      };
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);
      status_add ($status, 'map',      $map);

      if (@isset ($pass)) {
        status_add ($status, 'password', $pass);
      };

      status_add ($status, 'current', $cur);
      status_add ($status, 'max',     $max);
    };
  };
?>
