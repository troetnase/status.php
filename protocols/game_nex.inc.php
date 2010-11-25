<?php
  $protocol = "nex";

  $send     = chr (0xFF) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . "getinfo";

  $response = "/\xFF\xFF\xFF\xFFinfoResponse\x0A\\\/s";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    while (($variable = read_string ($result, "\\")) != "") {
      $value = read_string ($result, "\\");

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
      status_add ($status, 'current',  $cur);
      status_add ($status, 'max',      $max);
    };
  };
?>
