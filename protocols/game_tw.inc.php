<?php
  $protocol = "tw";

  $send     = chr (0x20) . chr (0x00) . chr (0x00) . chr (0x00)
            . chr (0x00) . chr (0x00) . chr (0xFF) . chr (0xFF)
            . chr (0xFF) . chr (0xFF) . "gief";

  $response = "/^\x20\\x00\\x00\\x00..\xFF\xFF\xFF\xFFinfo/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    $version  = read_string ($result, "\0");
    $hostname = read_string ($result, "\0");
    $map      = read_string ($result, "\0");
    $gametype = read_string ($result, "\0");
    $password = read_string ($result, "\0");
    $ping     = read_string ($result, "\0");
    $cur      = read_string ($result, "\0");
    $max      = read_string ($result, "\0");

    if ($password == 0) {
      $pass = "false";
    } else {
      $pass = "true";
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);
      status_add ($status, 'map', $map);

      if (@isset ($pass)) {
        status_add ($status, 'password', $pass);
      };

      status_add ($status, 'current', $cur);
      status_add ($status, 'max',     $max);
    };

    if ($RULES === true) {
      switch ($gametype) {
        case 0:
          rule_add ($rules, "Gametype", "dm");

          break;

        case 1:
          rule_add ($rules, "Gametype", "tdm");

          break;

        case 2:
          rule_add ($rules, "Gametype", "ctf");

          break;

        default:
          rule_add ($rules, "Gametype", "unknown");

          break;
      };

      rule_add ($rules, "Ping", $ping);
    };

    if ($PLAYER === true) {
      if ($cur > 0) {
        $player = array ();

        $header = array (
                    'name'  => array ('type' => 'string', 'length' => 32, 'width' => 80),
                    'score' => array ('type' => 'int',    'length' => 5,  'width' => 20)
                  );

        do {
          $player[] = array (
                        'name'  => read_string ($result, "\0"),
                        'score' => read_string ($result, "\0")
                      );
        } while (strlen ($result) > 0);
      };
    };
  };
?>
