<?php
  $protocol = "q2";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "status";

  $response = "/\xFF\xFF\xFF\xFFprint\x0A\\\\/s";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $array = explode ("\n", $result);
    $last  = end     ($array);

    if (empty ($last)) {
      array_pop ($array);
    };

    $cur = (count ($array)) - 1;

    while (($variable = read_string ($array[0], "\\")) != "") {
      $value = read_string ($array[0], "\\");

      if ($RULES === true) {
        rule_add ($rules, $variable, $value);
      };

      switch (strtolower ($variable)) {
        case "hostname":
          $hostname = $value;

          break;

        case "mapname":
          $map = $value;

          break;

        case "maxclients":
          $max = (int)$value;

          break;

        case "needpass":
          if ((int)$value == 1) {
            $pass = true;
          } else {
            $pass = false;
          };

          break;
      };
    };

    if ($PLAYER === true) {
      if ($cur > 0) {
        array_shift ($array);

        $player = array ();

        if (@file_exists ("{$BASE}/protocols/player/{$game}.inc.php")) {
          include ("{$BASE}/protocols/player/{$game}.inc.php");
        };
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
