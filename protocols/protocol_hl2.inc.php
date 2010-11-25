<?php
  $protocol = "hl2";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "TSource Engine Query" . chr (0x00);

  $response = "/^\xFF\xFF\xFF\xFFI./s";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $hostname  = encoding    (read_string ($result, "\0"));

    $map       = read_string ($result, "\0");
    $mod       = read_string ($result, "\0");
    $desc      = read_string ($result, "\0");

    $appid     = (int)(read_word ($result));

    $cur       = (int)ord    (read_byte   ($result));
    $max       = (int)ord    (read_byte   ($result));
    $bots      = (int)ord    (read_byte   ($result));

    $dedicated = read_byte   ($result);
    $os        = read_byte   ($result);

    $password  = (int)ord    (read_byte   ($result));
    $secure    = (int)ord    (read_byte   ($result));

    switch ($game) {
      case "ship":
        $mode  = (int)ord    (read_byte ($result));
        $count = (int)ord    (read_byte ($result));
        $time  = (int)ord    (read_byte ($result));

        break;
    };

    $version   = read_string ($result, "\0");

    if ($password == 1) {
      $pass = true;
    } else {
      $pass = false;
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);
      status_add ($status, 'map',      $map);
      status_add ($status, 'password', $pass);
      status_add ($status, 'bots',     $bots);
      status_add ($status, 'current',  $cur);
      status_add ($status, 'max',      $max);
    };

    if ($RULES === true) {
      unset ($result);

      $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                . chr (0xFF) . chr (0x56) . chr (0xFF)
                . chr (0xFF) . chr (0xFF) . chr (0xFF);

      $response = "/\xFF\xFF\xFF\xFFA/";

      $result   = server_query ($socket, $address, $queryport,
                                $send, $protocol, $response);

      if (! empty ($result)) {
        $challenge = read_long ($result);

        unset ($result);

        $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                  . chr (0xFF) . chr (0x56) . long_to_string ($challenge);

        $response = "/\xFE\xFF\xFF\xFF.....|\xFF\xFF\xFF\xFFE../s";

        $result   = server_query ($socket, $address, $queryport,
                                  $send, $protocol, $response);

        if (! empty ($result)) {
          while (($variable = read_string ($result, "\0")) != "") {
            $value = encoding (read_string ($result, "\0"));

            rule_add ($rules, $variable, $value);
          };
        };
      };
    };

    if (($PLAYER === true) && ($cur > 0)) {
      if (empty ($challenge)) {
        unset ($result);

        $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                  . chr (0xFF) . chr (0x55) . chr (0xFF)
                  . chr (0xFF) . chr (0xFF) . chr (0xFF);

        $response = "/\xFF\xFF\xFF\xFFA/";

        $result   = server_query ($socket, $address, $queryport,
                                  $send, $protocol, $response);

        if (! empty ($result)) {
          $challenge = read_long ($result);
        };
      };

      if (! empty ($challenge)) {
        unset ($result);

        $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                  . chr (0xFF) . chr (0x55) . long_to_string ($challenge);

        $response = "/(\xFE\xFF\xFF\xFF.....)|(\xFF\xFF\xFF\xFFD.)/s";

        $result   = server_query ($socket, $address, $queryport,
                                  $send, $protocol, $response);

        if (! empty ($result)) {
          $player = array ();

          $header = array (
                      'index' => array ('type' => 'int',    'length' => 5,  'width' => 10),
                      'name'  => array ('type' => 'string', 'length' => 32, 'width' => 64),
                      'score' => array ('type' => 'int',    'length' => 5,  'width' => 10),
                      'time'  => array ('type' => 'int',    'length' => 8,  'width' => 16)
                    );

          do {
            $player[] = array (
                          'index' => (int)ord    (read_byte  ($result)),
                          'name'  => trim        (encoding   (read_string ($result, "\0"))),
                          'score' => read_long   ($result),
                          'time'  => get_hl_time (read_float ($result))
                        );
          } while (strlen ($result) > 0);
        };
      };
    };
  };
?>
