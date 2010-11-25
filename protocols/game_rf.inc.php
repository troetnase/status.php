<?php
  $protocol = "rf";

  $send     = chr (0x00) . chr (0x00)
            . chr (0x00) . chr (0x00);

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol);

  if (! empty ($result)) {
    read_byte ($result);

    $result = substr ($result, strpos ($result, "\0"));

    read_byte ($result);

    $hostname = read_string ($result, "\0");
    $mod      = (int)ord    (read_byte ($result));
    $cur      = (int)ord    (read_byte ($result));
    $max      = (int)ord    (read_byte ($result));
    $map      = read_string ($result, "\0");

    read_byte ($result);

    $password = (int)ord (read_byte ($result));

    if (($password == 6) || ($password == 7)) {
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
?>
