<?php
  $protocol = "q4";

  $send     = chr (0xFF) . chr (0xFF) . "getInfo" . chr (0x00);

  $response = "/\xFF\xFFinfoResponse\\x00/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    switch (strtolower ($game)) {
      case "etqw":
      case "wolf":
        read_bytes ($result, 4);

        break;
    };

    read_bytes ($result, 4);

    $version = array (
                 'minor' => (int)ord (read_string ($result, "\0")),
                 'major' => (int)ord (read_string ($result, "\0"))
               );

    $version = (float)($version['major'] . "." . $version['minor']);

    switch (strtolower ($game)) {
      case "etqw":
      case "wolf":
        read_bytes ($result, 4);

        break;
    };

    while (($variable = read_string ($result, "\0")) != "") {
      $value = read_string ($result, "\0");

      if ($RULES === true) {
        rule_add ($rules, $variable, $value);
      };

      switch (strtolower ($variable)) {
        case "si_name":
          $hostname = preg_replace ("/\^([c|C][\d]{3}|([[:alnum:]]|;){1})/",
                                    "", $value);

          break;

        case "si_maxplayers":
          $max = (int)$value;

          break;

        case "fs_game":
          switch (strtolower ($game)) {
            case "q4":
              if ((! empty ($value)) && ((strtolower ($value)) != "q4mp")) {
                $mod = $value;
              };

              break;

            default:
              if (! empty ($value)) {
                $mod = $value;
              };

              break;
          };

          break;

        case "si_map":
          switch (strtolower ($game)) {
            case "d3":
              $map = preg_replace ("%^game/mp/%i", "", $value);

              break;

            case "etqw":
            case "wolf":
              $map = preg_replace ("%(^maps/|\.entities$)%i", "", $value);

              break;

            case "prey":
              $map = preg_replace ("%^game/%i", "", $value);

              break;

            case "q4":
              $map = preg_replace ("%^mp/%i", "", $value);

              break;

            default:
              $map = $value;

              break;
          };

          break;

        case "si_usepass":
          switch (strtolower ($game)) {
            case "d3":
            case "prey":
            case "q4":
              if ((int)$value == 1) {
                $pass = true;
              } else {
                $pass = false;
              };

              break;
          };

          break;

        case "si_needpass":
          switch (strtolower ($game)) {
            case "etqw":
            case "wolf":
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

    read_byte ($result);

    $cur    = 0;

    $player = array ();

    $header = array (
                'index' => array ('type' => 'int',    'length' => 6,  'width' => 12),
                'name'  => array ('type' => 'string', 'length' => 32, 'width' => 64),
                'ping'  => array ('type' => 'int',    'length' => 5,  'width' => 10)
              );

    switch (strtolower ($game)) {
//      case "q4"
//        $header['clan'] = array ('type' => 'string', 'length' => 8);
//
//        break;

      case "etqw":
        $bots = 0;

        if ($version < 10.19) {
          $header['rate'] = array ('type' => 'int', 'length' => 7, 'width' => 14);
        };

//        if ($version >= 10.17) {
//          $header['clan'] = array ('type' => 'string', 'length' => 8);
//        };
//
//        $header['bot']  = array ('type' => 'int',    'length' => 3);

        break;
    };

// 00000320  70 5f 63 61 6e 61 6c 73  2e 65 6e 74 69 74 69 65  |p_canals.entitie|
// 00000330  73 00 00 00 00 16 00 80  3e 00 00 67 6f 7a 75 74  |s.......>..gozut|
// 00000340  61 73 73 00 00 5e 30 34  50 5e 30 00 00 10 01 00  |ass..^04P^0.....|
// 00000350  00 00 00 00 00 00 00 01                           |........|
// 00000358

// 00000320  70 5f 63 61 6e 61 6c 73  2e 65 6e 74 69 74 69 65  |p_canals.entitie|
// 00000330  73 00 00 00 00 01 00 80  3e 00 00 67 6f 7a 75 74  |s.......>..gozut|
// 00000340  61 73 73 00 00 00 00 10  01 00 00 00 00 00 00 00  |ass.............|
// 00000350  00 01                                             |..|

// 00000320  70 5f 63 61 6e 61 6c 73  2e 65 6e 74 69 74 69 65  |p_canals.entitie|
// 00000330  73 00 00 00 10 01 00 00  00 00 00 00 00 00 01     |s..............|


    do {
      $player[$cur]          = array ();

      $player[$cur]['index'] = (int)ord (read_byte ($result));

      if ((((strtolower ($game)) == "wolf")
       && ($player[$cur]['index'] == 16))
       || ($player[$cur]['index'] == 32)) {
        array_pop ($player);

        break;
      };

      $player[$cur]['ping']  = read_word ($result);

      switch (strtolower ($game)) {
        case "etqw":
          if ($version < 10.19) {
            $player[$cur]['rate']  = read_unsigned ($result);
          };

          break;

        default:
          $player[$cur]['rate']  = read_unsigned ($result);

          break;
      };

      $player[$cur]['name'] = preg_replace ("/\^([c|C][\d]{3}|([[:alnum:]]|;){1})/",
                                            "", read_string ($result, "\0"));

      switch (strtolower ($game)) {
        case "q4":
          read_string ($result, "\0");

          break;

        case "etqw":
        case "wolf":
          if ($version >= 10.17) {
            if ((int)ord (read_byte ($result)) == 0) {
              $player[$cur]['name'] = preg_replace ("/\^([c|C][\d]{3}|([[:alnum:]]|;){1})/",
                                                    "", read_string ($result, "\0"))
                                    . $player[$cur]['name'];
            } else {
              $player[$cur]['name'] = $player[$cur]['name']
                                    . preg_replace ("/\^([c|C][\d]{3}|([[:alnum:]]|;){1})/",
                                                    "", read_string ($result, "\0"));
            };
          };

          if ((int)ord (read_byte ($result)) == 1) {
            $bots = $bots + 1;
          };

          break;
      };

      $cur = $cur + 1;
    } while (strlen ($result) > 8);

    if ($RULES === true) {
      if (strlen ($result) >= 4) {
        rule_add ($rules, "os", "0x" . m_str_pad (read_unsigned ($result),
                                                  2, "0", STR_PAD_LEFT));

        switch (strtolower ($game)) {
          case "etqw":
            if (! empty ($result)) {
              rule_add ($rules, "ranked",   (int)ord (read_byte ($result)));
              rule_add ($rules, "timeleft", read_unsigned ($result));
              rule_add ($rules, "state",    (int)ord (read_byte ($result)));

              if ($version >= 10.19) {
                $servertype = (int)ord (read_byte ($result));

                if ($servertype == 0) {
                  rule_add ($rules, "Servertype", "Gameserver");
                  rule_add ($rules, "joining",    (int)ord (read_byte ($result)));
                } elseif ($servertype == 1) {
                  rule_add ($rules, "Servertype",      "TV Server");
                  rule_add ($rules, "Cur. Spectators", read_unsigned ($result));
                  rule_add ($rules, "Max. Spectators", read_unsigned ($result));
                };
              };
            };

            break;
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
