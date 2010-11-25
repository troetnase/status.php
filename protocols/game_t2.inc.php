<?php
  $protocol = "t2";

  $send     = chr (0x0e) . chr (0x02) . chr (0x01)
            . chr (0x02) . chr (0x03) . chr (0x04);

  $response = "/^\x10\x02\x01\x02\x03\x04/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $queryversion = parse_tribes2 ($result);
    $protocol     = (int)ord      (read_byte ($result));

    read_bytes ($result, 3);

    $min_protocol = (int)ord (read_byte ($result));

    read_bytes ($result, 3);

    $build = (int)ord (read_byte ($result))
           + ((int)ord (read_byte ($result)) << 8);

    read_bytes ($result, 2);

    $hostname = parse_tribes2 ($result);

    unset ($result);

    $send     = chr (0x12) . chr (0x02) . chr (0x01)
              . chr (0x02) . chr (0x03) . chr (0x04);

    $response = "/^\x14\x02\x01\x02\x03\x04/";

    $result   = server_query ($socket, $address, $queryport,
                              $send, $protocol, $response);

    if (! empty ($result)) {
      $mod      = parse_tribes2 ($result);
      $gametype = parse_tribes2 ($result);
      $map      = parse_tribes2 ($result);

      $flags    = (int)ord      (read_byte ($result));
      $cur      = (int)ord      (read_byte ($result));
      $max      = (int)ord      (read_byte ($result));

      if ($flags & 2) {
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
      };
    };
  };
?>
