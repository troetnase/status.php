<?php
  $protocol = "bfh";

  $send     = "init";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol);

  if (! empty ($result)) {
    $challenge = read_string ($result, "\0");
  };

  unset ($result);

  if (! empty ($challenge)) {
    $send = "status" . $challenge;

    $result   = server_query ($socket, $address, $queryport,
                              $send, $protocol);

    if (! empty ($result)) {
      $hostname = read_string ($result, "\0");
      $cur      = read_string ($result, "\0");
      $max      = read_string ($result, "\0");
      $map      = read_string ($result, "\0");

      if (($max > 0) && (! empty ($hostname))) {
        status_add ($status, 'hostname', $hostname);

        if (@isset ($mod)) {
          status_add ($status, 'mod', $mod);
        };

        status_add ($status, 'map', $map);

        if (@isset ($pass)) {
          status_add ($status, 'password', $pass);
        };

        if (@isset ($bots)) {
          status_add ($status, 'bots', $bots);
        };

        status_add ($status, 'current', $cur);
        status_add ($status, 'max',     $max);
      };
    };
  };
?>
