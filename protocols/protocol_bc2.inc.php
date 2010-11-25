<?php
  $protocol = "bc2";

  $sequence = 0;

  $request  = array (
                'serverInfo'
              );

  $send     = build_bc2 ($request, $sequence);

  if ((connect ($socket, $address, $queryport)) === true) {
    if ((write ($socket, $send)) !== false) {
      if (($result = read ($socket)) !== false) {
        read_bytes ($result, 12);

        $error = parse_bc2  ($result);

        if (strtolower ($error) == "ok") {
          $hostname = parse_bc2 ($result);
          $cur      = parse_bc2 ($result);
          $max      = parse_bc2 ($result);

          parse_bc2 ($result);

          $map = preg_replace ("/^levels\//i", "", parse_bc2 ($result));

          if (($max > 0) && (! empty ($hostname))) {
            status_add ($status, 'hostname', $hostname);
            status_add ($status, 'map',      $map);
            status_add ($status, 'current',  $cur);
            status_add ($status, 'max',      $max);
          };
        };
      };

      $request = array (
                   'quit'
                 );

      $send    = build_bc2 ($request, $sequence);

      write ($socket, $send);
    };
  };
?>
