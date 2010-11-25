<?php
  $protocol = "ffow";
  $protocol = "hl2";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "FLSQ";

  $response = "/^\xFF\xFF\xFF\xFFI/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $protocol   = (int)ord (read_byte ($response));
    $hostname   = read_string ($result, "\0");
    $map        = read_string ($result, "\0");
    $mod        = read_string ($result, "\0");
    $gametype   = read_string ($result, "\0");
    $desc       = read_string ($result, "\0");
    $version    = read_string ($result, "\0");

    $gameport   = read_short  ($result);

    $cur        = (int)ord    (read_byte ($result));
    $max        = (int)ord    (read_byte ($result));

    $dedicated  = read_byte   ($result);
    $os         = read_byte   ($result);
    $password   = read_byte   ($result);
    $anticheat  = read_byte   ($result);

    $frametime  = (int)ord    (read_byte ($result));
    $round      = (int)ord    (read_byte ($result));
    $maxrounds  = (int)ord    (read_byte ($result));

    $seconds    = read_short  ($result);

    if ($password == 1) {
      $pass = "true";
    } else {
      $pass = "false";
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);
      status_add ($status, 'map', $map);

      if (@isset ($pass)) {
        status_add ($status, 'password', $pass);
      };

      status_add ($status, 'current', $cur);
      status_add ($status, 'max',     $max);
    };

    if ($RULES === true) {
      unset ($result);

      $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                . chr (0xFF) . chr (0x57);

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
          rule_add ($rules, "Version",   $version);

          switch ($gametype) {
            default:
              rule_add ($rules, "Gametype",   $gametype);

              break;
          };

          while (($variable = read_string ($result, "\0")) != "") {
            $value = read_string ($result, "\0");

            rule_add ($rules, $variable, $value);
          };
        };
      };
    };
  };
?>
