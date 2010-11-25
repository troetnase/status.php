<?php
  $protocol = "gs2";

  $send     = chr (0xFE) . chr (0xFD) . chr (0x00)
            . "STAT" . chr (0xFF) . chr (0xFF) . chr (0x00);

  $response = "/^\\x00STAT/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    while (($variable = read_string ($result, "\0")) != "") {
      $value = read_string ($result, "\0");

      if ($RULES === true) {
        rule_add ($rules, $variable, $value);
      };
  
      switch (strtolower ($variable)) {
        case "hostname":
          switch ($game) {
            case "swat4":
              $hostname = preg_replace ("/(\[(b|B)\]|\[(c|C)=[\da-f]{6}\])/i", "", $value);

              break;

            default:
              $hostname = preg_replace ("/[^a-z\d\s\[\]\(\)\.,:\-_\{\}\*\+~#=!\"\$&\|<>]/i",
                                        "", $value);

              break;
          };

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
        read_byte ($result);

        $player  = array ();

        $counter = (int)ord (read_byte ($result));

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
