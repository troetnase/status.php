<?php
  $protocol = "samp";

  $addr_arr   = explode (".", $address);

  $send     = "SAMP";

  foreach ($addr_arr as $token) {
    $send = $send . chr ($token);
  };

  $send     = $send . word_to_string ($queryport);

  $response = "/^{$send}i/s";

  $result   = server_query ($socket, $address, $queryport,
                            $send . "i", $protocol, $response);

  if (! empty ($result)) {
    $password = ord             (read_byte ($result));

    $cur      = read_word       ($result);
    $max      = read_word       ($result);

    $hostname = parse_samp_long ($result);
    $mode     = parse_samp_long ($result);
    $map      = parse_samp_long ($result);

    if ($password == 1) {
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

    if ($RULES === true) {
      unset ($result);

      $response = "/^{$send}r/";

      $result   = server_query ($socket, $address, $queryport,
                                $send . "r", $protocol, $response);

      if (! empty ($result)) {
        read_word ($result);

        while (($variable = parse_samp_short ($result)) != "") {
          rule_add ($rules, $variable, parse_samp_short ($result));
        };

        rule_add ($rules, "Mode", $mode);
      };
    };

    if ($PLAYER === true) {
      unset ($result);

      $response = "/^{$send}d/s";

      $result   = server_query ($socket, $address, $queryport,
                                $send . "d", $protocol, $response);

      if (! empty ($result)) {
        $header  = array (
                     'name'  => array ('type' => 'string', 'length' => 32, 'width' => 64),
                     'score' => array ('type' => 'int',    'length' => 11, 'width' => 22),
                     'ping'  => array ('type' => 'int',    'length' => 7,  'width' => 14)
                   );

        $player  = array ();

        $counter = read_word ($result);

        while (count ($player) < $counter) {
          read_byte ($result);

          $player[] = array (
                        'name'  => parse_samp_short ($result),
                        'score' => read_unsigned    ($result),
                        'ping'  => read_unsigned    ($result)
                      );
        };
      };
    };
  };
?>
