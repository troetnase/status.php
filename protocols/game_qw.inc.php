<?php
  $protocol = "qw";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "status";

  $response = "/\xFF\xFF\xFF\xFFn\\\\/";

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

        case "map":
          $map = $value;

          break;

        case "maxclients":
          $max = (int)$value;

          break;
      };
    };

    if ($PLAYER === true) {
      if ($cur > 0) {
        array_shift ($array);

        $player  = array ();

        if (@file_exists ("{$BASE}/protocols/player/{$game}.inc.php")) {
          include ("{$BASE}/protocols/player/{$game}.inc.php");
        };
      };
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);
      status_add ($status, 'map',      $map);
      status_add ($status, 'current',  $cur);
      status_add ($status, 'max',      $max);
    };
  };
?>
