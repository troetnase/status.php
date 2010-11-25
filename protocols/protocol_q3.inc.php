<?php
  $protocol = "q3";

  switch (strtolower ($game)) {
    case "mohaa":
      $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                . chr (0xFF) . chr (0x02) . "getstatus";

      break;

    default:
      $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
                . chr (0xFF) . "getstatus";

      break;
  };

  $result = server_query ($socket, $address, $queryport,
                          $send, $protocol);

  if (! empty ($result)) {
    switch (strtolower ($game)) {
      case "mohaa":
        read_bytes ($result, 21);

        break;

      default:
        read_bytes ($result, 20);

        break;
    };

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
        case "sv_hostname":
          $hostname = preg_replace ("/\^[\da-f]{1}/i", "", $value);

          break;

        case "sv_maxclients":
          $max = (int)$value;

          break;

        case "version":
          switch (strtolower ($game)) {
            case "mohaa":
              if (preg_match ("/spearhead/i", $value)) {
                $mod = "spearhead";
              } elseif (preg_match ("/breakthrough/i", $value)) {;
                $mod = "breakthrough";
              };

              break;
          };

          break;

        case "mapname":
          switch (strtolower ($game)) {
            case "mohaa":
              $map = preg_replace ("%^\w+/%i", "", $value);

              break;

            default:
              $map = $value;

              break;
          };

          break;

        case "fs_game":
          switch (strtolower ($game)) {
            case "cod4":
            case "cod5":
              $mod = preg_replace ("%^mods/%i", "", $value);

              break;

            default:
              $mod = $value;

              break;
          };

          break;

        case "g_needpass":
          switch (strtolower ($game)) {
            case "et":
            case "oa":
            case "q3":
            case "jk2":
            case "jk3":
            case "trm":
            case "urt":
            case "wop":
            case "rtcw":
            case "sof2":
            case "mohaa":
            case "stvef":
            case "stef2":
              if ((int)$value == 1) {
                $pass = true;
              } else {
                $pass = false;
              };

              break;
          };

          break;

        case "pswrd":
          switch (strtolower ($game)) {
            case "cod":
            case "cod2":
            case "cod4":
            case "cod5":
              if ((int)$value == 1) {
                $pass = true;
              } else {
                $pass = false;
              };

              break;
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

      if (@isset ($mod)) {
        status_add ($status, 'mod', $mod);
      };

      status_add ($status, 'map', $map);

      if (@isset ($pass)) {
        status_add ($status, 'password', $pass);
      };

      status_add ($status, 'current', $cur);
      status_add ($status, 'max',     $max);
    };
  };
?>
