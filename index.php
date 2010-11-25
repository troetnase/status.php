<?php
  $SCRIPT = __FILE__;
  $BASE   = dirname ($SCRIPT);

  include ("./includes/functions.inc.php");
  include ("./includes/strings.inc.php");
  include ("./includes/setup.inc.php");
  include ("./includes/web.inc.php");

  if (@file_exists ("{$BASE}/includes/{$PROJECT}.inc.php")) {
    include (str_replace ($BASE, ".",
             "{$BASE}/includes/{$PROJECT}.inc.php"));
  };

  php_compare  ();
  set_encoding ();

  if ((@is_array ($_GET)) && (count ($_GET) > 0)) {
    $address   = get ("ip",        "/^[\w\.-]+$/");
    $game      = get ("game",      "/^\w+$/");
    $port      = get ("port",      "/^\d+$/");
    $queryport = get ("queryport", "/^\d+$/");

    $flags     = get ("flags");

    if ((@array_key_exists ("rules", $flags))
     && (strtolower ($flags['rules']) == "true" )) {
      $RULES = true;
    };

    if ((@array_key_exists ("player", $flags))
     && (strtolower ($flags['player']) == "true" )) {
      $PLAYER = true;
    };

    if ((@isset ($address)) && (@isset ($game))) {
      if (! is_ip ($address)) {
        $name    = $address;

        $address = gethostbyname ($name);

        if ($name == $address) {
          head   ();
          form   ("Could not resolve hostname!");
          footer ();

          die    ();
        };
      };

      if ((@array_key_exists ($game, $games))
       && (@is_array ($games[$game]))) {
        if ((! @isset ($port)) || (empty ($port))) {
          $port = $games[$game]['gameport'];
        };

        if ((! @isset ($queryport)) || (empty ($queryport))) {
          if (@array_key_exists ("queryport", $games[$game])) {
            if (@is_numeric ($games[$game]['queryport'])) {
              $queryport = $games[$game]['queryport'];

              if ($games[$game]['queryport'] == $port) {
                $queryport = $queryport + 1;
              };
            } elseif (preg_match ("/[+|-]/", $games[$game]['queryport'])) {
              $queryport = eval ("return ({$port} "
                                 . $games[$game]['queryport'] . ");");
            };
          } elseif (@array_key_exists ("gameport", $games[$game])) {
            $queryport = $port;
          };
        };

        if (@isset ($queryport)) {
          if (@array_key_exists ("protocol", $games[$game])) {
            $file = "{$BASE}/protocols/protocol_"
                  . $games[$game]['protocol'] . ".inc.php";
          } else {
            $file = "{$BASE}/protocols/game_{$game}.inc.php";
          };

          if (@file_exists ($file)) {
            if (($socket = @socket_create (AF_INET, SOCK_DGRAM,
                                           SOL_UDP)) !== false) {
              include          (str_replace ($BASE, ".", $file));

              @socket_shutdown ($socket, __ALL);
              @socket_close    ($socket);

              unset            ($socket);

              if (@is_array ($status)) {
                head   (NULL, 30);
                form   ();

                print  ("  <table width=\"100%\" cellpadding=\"0\""
                        . " cellspacing=\"0\" border=\"0\">\n");

                print  ("   <tr>\n");
                print  ("    <td width=\"15%\">&nbsp;</td>\n");
                print  ("    <td width=\"70%\">\n");
                print  ("     <table width=\"100%\" cellpadding=\"0\""
                        . " cellspacing=\"0\" border=\"0\">\n");

                print  ("      <tr>\n");
                print  ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                print  ("      </tr>\n");
                print  ("      <tr>\n");
                print  ("       <td width=\"100%\" colspan=\"2\">Status</td>\n");
                print  ("      </tr>\n");
                print  ("      <tr>\n");
                print  ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                print  ("      </tr>\n");
                print  ("      <tr>\n");

                if ($queryport != $port) {
                  print ("       <td width=\"40%\">IP:Port:Query</td>\n");
                  print ("       <td width=\"60%\">&nbsp;{$address}:{$port}:{$queryport}</td>\n");
                } else {
                  print ("       <td width=\"40%\">IP:Port</td>\n");
                  print ("       <td width=\"60%\">&nbsp;{$address}:{$port}</td>\n");
                };

                print ("      </tr>\n");
                print ("      <tr>\n");
                print ("       <td width=\"40%\">Game</td>\n");
                print ("       <td width=\"60%\">&nbsp;"
                       . escape_html ($games[$game]['game']) . "</td>\n");

                print ("      </tr>\n");
                print ("      <tr>\n");

                if (! @array_key_exists ("protocol", $games[$game])) {
                  $protocol = $game;
                };

                print ("       <td width=\"40%\">Protocol</td>\n");
                print ("       <td width=\"60%\">&nbsp;{$protocol}</td>\n");
                print ("      </tr>\n");
                print ("      <tr>\n");
                print ("       <td width=\"40%\">Name</td>\n");
                print ("       <td width=\"60%\">&nbsp;"
                       . escape_html ($status['hostname']) . "</td>\n");

                print ("      </tr>\n");

                if (@isset ($status['mod'])) {
                  print ("      <tr>\n");
                  print ("       <td width=\"40%\">Mod</td>\n");
                  print ("       <td width=\"60%\">&nbsp;"
                         . escape_html ($status['mod']) . "</td>\n");

                  print ("      </tr>\n");
                };

                print ("      <tr>\n");
                print ("       <td width=\"40%\">Map</td>\n");
                print ("       <td width=\"60%\">&nbsp;"
                       . escape_html ($status['map']) . "</td>\n");

                print ("      </tr>\n");

                if (@isset ($status['password'])) {
                  print ("      <tr>\n");
                  print ("       <td width=\"40%\">Password</td>\n");

                  if ($status['password'] === true) {
                    print ("       <td width=\"60%\">&nbsp;true</td>\n");
                  } else {
                    print ("       <td width=\"60%\">&nbsp;false</td>\n");
                  };

                  print ("      </tr>\n");
                };

                if (@isset ($status['bots'])) {
                  print ("      <tr>\n");
                  print ("       <td width=\"40%\">Bots</td>\n");
                  print ("       <td width=\"60%\">&nbsp;{$status['bots']}</td>\n");
                  print ("      </tr>\n");
                };

                print ("      <tr>\n");
                print ("       <td width=\"40%\">Cur. player</td>\n");
                print ("       <td width=\"60%\">&nbsp;{$status['current']}</td>\n");
                print ("      </tr>\n");
                print ("      <tr>\n");
                print ("       <td width=\"40%\">Max. player</td>\n");
                print ("       <td width=\"60%\">&nbsp;{$status['max']}</td>\n");
                print ("      </tr>\n");

                if (($RULES === true) && (@is_array ($rules))) {
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">Rules</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                  print ("      </tr>\n");

                  foreach ($rules as $rule) {
                    print ("      <tr>\n");
                    print ("       <td width=\"40%\">"
                           . escape_html (key ($rule)) . "</td>\n");

                    print ("       <td width=\"60%\">&nbsp;"
                           . escape_html (current ($rule)) . "</td>\n");

                    print ("      </tr>\n");
                  };
                };

                if (($PLAYER === true) && (@is_array ($header))) {
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">Player</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">&nbsp;</td>\n");
                  print ("      </tr>\n");
                  print ("      <tr>\n");
                  print ("       <td width=\"100%\" colspan=\"2\">\n");
                  print ("        <table width=\"100%\" cellpadding=\"0\""
                         . " cellspacing=\"0\" border=\"0\">\n");

                  print ("         <tr>\n");

                  $counter = count ($header);

                  foreach ($header as $head => $value) {
                    switch (strtolower ($value['type'])) {
                      case "int":
                        print ("          <td width=\"{$value['width']}%\" align=\"right\">"
                               . ucfirst ($head) . "&nbsp;</td>\n");

                        break;

                      case "string":
                        print ("          <td width=\"{$value['width']}%\">"
                               . escape_html (ucfirst ($head)) . "</td>\n");

                        break;
                    };
                  };

                  print ("         </tr>\n");

                  if (((count ($player)) == 0) || (! @isset ($player))) {
                    print ("         <tr>\n");
                    print ("          <td width=\"100%\" colspan=\"{$counter}\">No player</td>\n");
                    print ("         </tr>\n");
                  } else {
                    foreach ($player as $item) {
                      print ("         <tr>\n");

                      foreach ($header as $head => $value) {
                        switch (strtolower ($value['type'])) {
                          case "int":
                            print ("          <td width=\"{$value['width']}%\" align=\"right\">"
                                   . "{$item[$head]}&nbsp;</td>\n");

                            break;

                          case "string":
                            print ("          <td width=\"{$value['width']}%\">"
                                   . escape_html ($item[$head]) . "</td>\n");

                            break;
                        };
                      };

                      print ("         </tr>\n");
                    };
                  };

                  print ("        </table>\n");
                  print ("       </td>\n");
                  print ("      </tr>\n");
                };

                print  ("     </table>\n");
                print  ("    </td>\n");
                print  ("    <td width=\"15%\">&nbsp;</td>\n");
                print  ("   </tr>\n");
                print  ("  </table>\n");

                footer ();
              } else {
                head   ();
                form   ("No query-response!");
                footer ();
              };
            } else {
              head   ();
              form   ("Socket-error!");
              footer ();
            };
          } else {
            head   ();
            form   ("No protocol-information found!");
            footer ();
          };
        } else {
          head   ();
          form   ("Queryport-error!");
          footer ();
        };
      } else {
        head   ();
        form   ("No game-configuration found!");
        footer ();
      };
    } else {
      head   ();
      form   ("No valid input!");
      footer ();
    };
  } else {
    head   ();
    form   ();
    footer ();
  };
?>
