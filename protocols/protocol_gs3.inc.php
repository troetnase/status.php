<?php
  $protocol = "gs3";

  $send     = chr (0xFE) . chr (0xFD) . chr (0x00)
            . "STAT" . chr (0xFF) . chr (0x00)
            . chr (0x00) . chr (0x01);

  $response = "/\\x00STATsplitnum\\x00../s";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    while (($variable = read_string ($result, "\0")) != "") {
      $value = read_string ($result, "\0");

      if ($RULES === true) {
        if ((strtolower ($game) != "vc2") || ($variable != "extinfo")) {
          rule_add ($rules, $variable, $value);
        };
      };

      switch (strtolower ($variable)) {
        case "hostname":
          $hostname = $value;

          break;

        case "mapname":
          $map = $value;

          break;

        case "numplayers":
          $cur = (int)$value;

          break;

        case "maxplayers":
          $max = (int)$value;

          break;

        case "gamevariant":
          switch (strtolower ($game)) {
            case "bf2":
              if ((strtolower ($value)) != (strtolower ($game))) {
                $mod = $value;
              };

              break;
          };

          break;

        case "password":
          if (is_numeric ($value)) {
            if ((int)$value == 1) {
              $pass = true;
            } else {
              $pass = false;
            };
          } else {
            if (strtolower ($value) == "true") {
              $pass = true;
            } else {
              $pass = false;
            };
          };

          break;
      };
    };

    if ($PLAYER === true) {
      if ((strlen ($result)) > 0) {
        $player  = array ();

        if (@file_exists ("{$BASE}/protocols/player/{$game}.inc.php")) {
          include ("{$BASE}/protocols/player/{$game}.inc.php");
        };
      };
    };

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
?>
