<?php
  if (@file_exists ("{$BASE}/protocols/games.inc.php")) {
    include ("./protocols/games.inc.php");
  } else {
    die ("No protocol-information found!\n");
  };

  if (@file_exists ("{$BASE}/protocols/master.inc.php")) {
    include ("./protocols/master.inc.php");
  } else {
    die ("No master-information found!\n");
  };

  function server_query (&$socket, $address, $port, $data,
                         $protocol, $ignore = NULL) {
    $output = NULL;

    if (DEBUG === true) {
      print ("Destination  : {$address}\n");
      print ("Port         : {$port}\n");
    };

    @socket_set_option ($socket, SOL_SOCKET,
                        SO_RCVBUF, 16384);

    $counter = 0;
    $start   = gettimeofday ();

    $last    = false;

    @socket_set_nonblock ($socket);

    @socket_sendto       ($socket, $data, strlen ($data),
                          0, $address, $port);

    if (DEBUG === true) {
      @socket_getsockname ($socket, $addr, $source);

      print               ("Local address: {$addr}\n");
      print               ("Local port   : {$source}\n");
    };

    do {
      $now  = gettimeofday ();

      $usec = (($now['sec']  - $start['sec']) * 1000000)
            + (($now['usec'] - $start['usec']));

      $len  = @socket_recvfrom ($socket, $response,
                                16384, 0, $src, $port);

      if ($len > 0) {
        if ($address !== $src) {
          usleep (50);

          continue;
        };

        if ((assembly ($result, $response, $protocol,
                       $counter, $last)) === true) {
          break;
        };
      };

      usleep (50);
    } while ($usec < 2000000);

    if (DEBUG === true) {
      print ("Packet-count : {$counter}\n");
    };

    for ($i = 1; $i <= $counter; $i++) {
      if (DEBUG === true) {
        $pid = posix_getpid ();

        write_file ("/tmp/{$pid}.{$i}", $result[$i]);
      };

      $output = mangle ($output, $result[$i], $protocol);
    };

    if (@isset ($ignore)) {
      if (DEBUG === true) {
        print ("Sending : {$data}\n");
        print ("Ignoring: {$ignore}\n");
      };

      $output = preg_replace ($ignore, "", $output,
                              -1, $replaced);

      if (DEBUG === true) {
        print ("Replaced {$replaced} times\n");
      };
    };

    return $output;
  };

  function get_gs3_queryid (&$data, &$final) {
    if (! preg_match ('/^\\x00STATsplitnum\\x00/', $data)) {
      $final = true;

      return 1;
    } else {
      $queryid = (((int)ord ($data[14])) & 15) + 1;
      $last    = ((int)ord ($data[14]) >> 4);

      if ($last == 8) {
        $final = true;
      } else {
        $final = false;
      };

      return $queryid;
    };
  };

  function get_ffow_queryid (&$data, &$final) {
    if (preg_match ('/^\xFF\xFF\xFF\xFE/', $data)) {
      $queryid = (int)ord ($data[8]) + 1;
      $last    = (int)ord ($data[9]);

      if ($queryid == $last) {
        $final = true;
      };

      return $queryid;
    } else {
      $final = true;

      return 1;
    };
  };

  function get_hl_queryid (&$data, &$final) {
    if (preg_match ('/^\xFE\xFF\xFF\xFF/', $data)) {
      $counter = ((int)ord ($data[8])) & 15;
      $queryid = ((int)ord ($data[8]) >> 4) + 1;

      if ($queryid == $counter) {
        $final = true;
      } else {
        $final = false;
      };

      return $queryid;
    } else {
      $final = true;

      return 1;
    };
  };

  function get_gs_queryid (&$data, &$final) {
    if (! preg_match ('/\\\queryid\\\[\d]+\.([\d])\\\final\\\$|'
                      . '\\\final\\\[\\\]queryid\\\[\d]+\.([\d])$/',
                      $data, $matches)) {
      $final = false;
    } else {
      $queryid = (array_slice ($matches, -1, 1));

      $final   = true;
    };

    if (@isset ($queryid)) {
      return $queryid[0];
    } else {
      return false;
    };
  };

  function assembly (&$result, &$data, $protocol,
                     &$counter, &$last) {
    switch (strtolower ($protocol)) {
      case "ffow":
      case "gs":
      case "gs3":
      case "gs4":
      case "hl":
      case "hl2":
      case "ut2kx":
        $counter = $counter + 1;
        $final   = false;

        switch (strtolower ($protocol)) {
          case "ffow":
            if ((! preg_match ('/^(\xFF\xFF\xFF\xFF|'
                               . '\xFF\xFF\xFF\xFE)/', $data))
             || (strlen ($data) < 7)) {
              unset ($result);
              unset ($data);

              $counter = 0;

              return true;
            } elseif ((preg_match ('/^\xFF\xFF\xFF\xFE/', $data))
             && (strlen ($data) < 15)) {
              unset ($result);
              unset ($data);

              $counter = 0;

              return true;
            };

            $queryid = get_ffow_queryid ($data, $final);

            break;

          case "gs":
            $queryid = get_gs_queryid ($data, $final);

            break;

          case "gs3":
          case "gs4":
            if (strlen ($data) < 12) {
              unset ($result);
              unset ($data);

              $counter = 0;

              return true;
            };

            $queryid = get_gs3_queryid ($data, $final);

            break;

          case "hl":
          case "hl2":
            if ((! preg_match ('/^(\xFF\xFF\xFF\xFF|'
                               . '\xFE\xFF\xFF\xFF)/', $data))
             || (strlen ($data) < 5)) {
              unset ($result);
              unset ($data);

              $counter = 0;

              return true;
            } elseif ((preg_match ('/^\xFE\xFF\xFF\xFF/', $data))
             && (strlen ($data) <= 9)) {
              unset ($result);
              unset ($data);

              $counter = 0;

              return true;
            };

            $queryid = get_hl_queryid ($data, $final);

            break;

          case "ut2kx":
            if ((strlen ($data)) < 450) {
              $final = true;
            };

            $queryid = $counter;

            break;
        };

        if ($queryid !== false) {
          $result[$queryid] = $data;

          if ($final === true) {
            $last = $queryid;

            if ($queryid == $counter) {
              return true;
            } else {
              return false;
            };
          } else {
            if ($last !== false) {
              if ($counter == $last) {
                return true;
              } else {
                return false;
              };
            } else {
              return false;
            };
          };
        };

        break;

      default:
        switch (strtolower ($protocol)) {
          case "gs2":
            if (strlen ($data) < 15) {
              unset ($result);
              unset ($data);

              $counter = 0;

              return true;
            };

            break;
        };

        $counter          = $counter + 1;
        $result[$counter] = $data;

        break;
    };

    return true;
  };

  function parse_ut2kx (&$string) {
    $size = (int)(ord (substr ($string, 0, 1)));

    if ($size >= 64) {
      $split = (int)(ord (substr ($string, 1, 2)));
      $size  = ($size - 64) + (64 * $split);

      $start = 2;
    } else {
      $start = 1;
    };

    if ($size > 0) {
      $value = substr ($string, $start, $size - 1);
    } else {
      $value = NULL;
    };

    $string = substr ($string, $size + $start);

    return $value;
  };

  function parse_samp_short (&$string) {
    $size = (int)ord (read_byte ($string));

    if ($size > 0) {
      $value = substr ($string, 0, $size);
    } else {
      $value = NULL;
    };

    $string = substr ($string, $size);

    return $value;
  };

  function parse_samp_long (&$string) {
    $size = (int)read_long ($string);

    if ($size > 0) {
      $value = substr ($string, 0, $size);
    } else {
      $value = NULL;
    };

    $string = substr ($string, $size);

    return $value;
  };

  function parse_ase (&$string) {
    $size = (int)(ord (substr ($string, 0, 1)));

    if ($size > 0) {
      $value = substr ($string, 1, $size - 1);
    } else {
      $value = NULL;
    };

    $string = substr ($string, $size);

    return $value;
  };

  function parse_tribes2 (&$string) {
    $size = (int)(ord (substr ($string, 0, 1)));

    if ($size > 0) {
      $value = substr ($string, 1, $size);
    } else {
      $value = NULL;
    };

    $string = substr ($string, $size + 1);

    return $value;
  };

  function status_add (&$status, $key, $value, $overwrite = false) {
    if (! @is_array ($status)) {
      $status = array ();
    };

    if ((! @array_key_exists ($key, $status)) || ($overwrite === true)) {
      $status[$key] = $value;
    };
  };

  function rule_add (&$rules, $key, $value) {
    if (! @is_array ($rules)) {
      $rules = array ();
    };

    $rules[] = array ($key => $value);
  };

  function get_hl_time ($time) {
    $h = m_str_pad (floor ($time / 3600), 2, 0, STR_PAD_LEFT);
    $m = m_str_pad (floor (($time - ($h * 3600)) / 60), 2, 0, STR_PAD_LEFT);
    $s = m_str_pad (floor ($time - (($h * 3600)
                                  + ($m * 60))), 2, 0, STR_PAD_LEFT);

    return "{$h}:{$m}:{$s}";
  };

  function get_rs_time ($time) {
    $array = explode (":", $time);

    $h = m_str_pad (floor ($array[0] / 60), 2, 0, STR_PAD_LEFT);
    $m = m_str_pad ($array[0] % 60, 2, 0, STR_PAD_LEFT);
    $s = m_str_pad ($array[1], 2, 0, STR_PAD_LEFT);

    return "{$h}:{$m}:{$s}";
  };

  function mangle ($response, $packet, $protocol) {
    switch (strtolower ($protocol)) {
      case "gs3":
      case "gs4":
        if (! preg_match ('/^\\x00STATsplitnum\\x00/', $packet)) {
          return $packet;
        } else {
          if (! empty ($response)) {
            read_last_byte   ($response);
            read_last_string ($response, "\0");

            read_bytes       ($packet, 16);
            read_string      ($packet, "\0");
            read_byte        ($packet);

            return           ($response . chr (0x00) . $packet);
          } else {
            return $packet;
          };
        }; 

        break;

      default:
        if (! empty ($response)) {
          return ($response . $packet);
        } else {
          return $packet;
        };

        break;
    };
  };

  function strip_colors ($string, $game) {
    return $string;
  };
?>
