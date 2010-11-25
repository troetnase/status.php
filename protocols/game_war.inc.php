<?php
  $protocol = "war";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "getinfo";

  $response = "/\xFF\xFF\xFF\xFFinfoResponse\x0A\\\/s";

  $result = server_query ($socket, $address, $queryport,
                          $send, $protocol, $response);

  if (! empty ($result)) {
    $array = explode ("\n", $result);
    $last  = end     ($array);

    if (empty ($last)) {
      array_pop ($array);
    };

    while (($variable = read_string ($array[0], "\\")) != "") {
      $value = read_string ($array[0], "\\");

      if ($RULES === true) {
        rule_add ($rules, $variable, $value);
      };

      switch (strtolower ($variable)) {
        case "sv_hostname":
          $hostname = preg_replace ("/\^[\d]{1}/", "", $value);

          break;

        case "mapname":
          $map = $value;

          break;

        case "g_needpass":
          if ((int)$value == 1) {
            $pass = true;
          } else {
            $pass = false;
          };

          break;

        case "clients":
          $cur = (int)$value;

          break;

        case "sv_maxclients":
          $max = (int)$value;

          break;
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
