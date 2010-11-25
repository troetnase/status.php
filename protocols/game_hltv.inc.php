<?php
  $protocol = "hl";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "TSource Engine Query";

  $response = "/\xFF\xFF\xFF\xFFm/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $hostip    = read_string ($result, "\0");
    $hostname  = read_string ($result, "\0");
    $map       = read_string ($result, "\0");
    $mod       = read_string ($result, "\0");
    $desc      = read_string ($result, "\0");

    $cur       = (int)ord    (read_byte ($result));
    $max       = (int)ord    (read_byte ($result));

    $version   = read_byte   ($result);
    $dedicated = read_byte   ($result);
    $os        = read_byte   ($result);

    $password  = (int)ord    (read_byte ($result));
    $ismod     = (int)ord    (read_byte ($result));

    if ($ismod == 1) {
      $modinfo     = read_string ($result, "\0");
      $url         = read_string ($result, "\0");
      $unused      = read_string ($result, "\0");

      $mod_version = read_long   ($result);
      $mod_size    = read_long   ($result);

      $sv_only     = (int)ord    (read_byte ($result));
      $cl_dll      = (int)ord    (read_byte ($result));
    };

    $secure = (int)ord (read_byte ($result));
    $bots   = (int)ord (read_byte ($result));

    if ($password == 1) {
      $pass = true;
    } else {
      $pass = false;
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);

      if ((@isset ($mod)) && ($mod != "valve")) {
        status_add ($status, 'mod', $mod);
      };

      status_add ($status, 'map',      $map);
      status_add ($status, 'password', $pass);
      status_add ($status, 'bots',     $bots);
      status_add ($status, 'current',  $cur);
      status_add ($status, 'max',      $max);
    };

    if ($RULES === true) {
      $RULES = false;
    };

    if (($PLAYER === true) && ($cur > 0)) {
      if (empty ($challenge)) {
        unset ($result);

        $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                  . chr (0xFF) . chr (0x57);

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
                          'name'  => read_string ($result, "\0"),
                          'score' => read_long   ($result),
                          'time'  => get_hl_time (read_float ($result))
                        );
          } while (strlen ($result) > 0);
        };
      };
    };
  };
?>
