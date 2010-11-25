<?php
  $protocol = "gs";

  $send     = "\\basic\\\\info\\\\rules\\";
  $response = "/^\\\\/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $cur = 0;
    $max = 0;

    while (($variable = read_string ($result, "\\")) != "") {
      if ((strtolower ($variable)) == "queryid") {
        read_string ($result, "\\");

        continue;
      } elseif ((strtolower ($variable)) == "final") {
        break;
      };

      $value = read_string ($result, "\\");

      if ($RULES === true) {
        rule_add ($rules, $variable, $value);
      };

      switch (strtolower ($variable)) {
        case "hostname":
          $hostname = $value;

          break;

        case "numplayers":
          $cur = (int)$value;

          break;

        case "maxplayers":
          $max = (int)$value;

          break;

        case "mapname":
          $map = $value;

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
      unset ($result);

      $send     = "\\players\\";
      $response = "/^\\\\/";

      $result   = server_query ($socket, $address, $queryport,
                                $send, $protocol, $response);

      if (! empty ($result)) {
        $player  = array ();

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
