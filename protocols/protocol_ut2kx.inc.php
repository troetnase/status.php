<?php
  $protocol = "ut2kx";

  switch ($game) {
    case "ut2k3":
      $send     = chr (0x79) . chr (0x00) . chr (0x00)
                . chr (0x00) . chr (0x00);

      $response = "/\x79\\x00\\x00\\x00\\x00/";

      break;

    default:
      $send     = chr (0x80) . chr (0x00) . chr (0x00)
                . chr (0x00) . chr (0x00);

      $response = "/\x80\\x00\\x00\\x00\\x00/";

      break;
  };

  $result = server_query ($socket, $address, $queryport,
                          $send, $protocol, $response);

  if (! empty ($result)) {
    $id = (int)read_long ($result);

    parse_ut2kx ($result);

    $port     = (int)read_long ($result);
    $qport    = (int)read_long ($result);

    $hostname = preg_replace   ("/[^a-z\d\s\[\]\(\)\.,:\-_\{\}\*\+~#=!\"\$&\|<>]/i",
                                "", parse_ut2kx ($result));

    $map      = parse_ut2kx    ($result);
    $mod      = parse_ut2kx    ($result);
    $cur      = (int)read_long ($result);
    $max      = (int)read_long ($result);

    read_long ($result);

    switch ($game) {
      case "ut2k3":
        break;

      default:
        read_long   ($result);

        parse_ut2kx ($result);

        break;
    };

    if ($RULES === true) {
      rule_add ($rules, "GameType", $mod);
    };

    unset ($result);

    switch ($game) {
      case "ut2k3":
        $send     = chr (0x79) . chr (0x00) . chr (0x00)
                  . chr (0x00) . chr (0x01);

        $response = "/\x79\\x00\\x00\\x00\x01/";

        break;

      default:
        $send     = chr (0x80) . chr (0x00) . chr (0x00)
                  . chr (0x00) . chr (0x01);

        $response = "/\x80\\x00\\x00\\x00\x01/";

        break;
    };

    $result = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

    if (! empty ($result)) {
      while (($variable = parse_ut2kx ($result)) != "") {
        $value = parse_ut2kx ($result);

        if ($RULES === true) {
          rule_add ($rules, $variable, $value);
        };

        switch ($game) {
          case "ut2k3":
            if (strtolower ($variable) == "password") {
              $password = $value;
            };

            break;

          default:
            if (strtolower ($variable) == "gamepassword") {
              $password = $value;
            };

            break;
        };
      };

      if (strtolower ($password) == "true") {
        $pass = true;
      } else {
        $pass = false;
      };

      if (($max > 0) && (! empty ($hostname))) {
        status_add ($status, 'hostname', $hostname);
        status_add ($status, 'map',      $map);
        status_add ($status, 'password', $pass);
        status_add ($status, 'current',  $cur);
        status_add ($status, 'max',      $max);
      };
    };

    if (($PLAYER === true) && ($cur > 0)) {
      unset ($result);

      switch ($game) {
        case "ut2k3":
          $send     = chr (0x79) . chr (0x00) . chr (0x00)
                    . chr (0x00) . chr (0x02);

          $response = "/\x79\\x00\\x00\\x00\x02/";

          break;

        default:
          $send     = chr (0x80) . chr (0x00) . chr (0x00)
                    . chr (0x00) . chr (0x02);

          $response = "/\x80\\x00\\x00\\x00\x02/";

          break;
      };

      $result = server_query ($socket, $address, $queryport,
                              $send, $protocol, $response);

      if (! empty ($result)) {
        $header = array (
                    'name'  => array ('type' => 'string', 'length' => 32, 'width' => 64),
                    'ping'  => array ('type' => 'string', 'length' => 9,  'width' => 18),
                    'score' => array ('type' => 'int',    'length' => 9,  'width' => 18)
                  );

        do {
          read_long ($result);

          $player[] = array (
                        'name'  => parse_ut2kx   ($result),
                        'score' => read_long     ($result),
                        'ping'  => read_unsigned ($result)
                      );

          read_long ($result);
        } while (strlen ($result) > 0);
      };
    };
  };
?>
