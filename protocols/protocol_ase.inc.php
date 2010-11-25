<?php
  $protocol = "ase";

  $send     = "s";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol);

  if (! empty ($result)) {
    read_bytes ($result, 4);

    $gamename = parse_ase ($result);
    $hostport = parse_ase ($result);

    switch ($game) {
      case "fc":
        $hostname = preg_replace ("/\\\$[\d]{1}/", "", parse_ase ($result));

        break;

      default:
        $hostname = parse_ase ($result);

        break;
    };

    $mod      = parse_ase      ($result);
    $map      = parse_ase      ($result);
    $version  = parse_ase      ($result);
    $password = (int)parse_ase ($result);
    $cur      = (int)parse_ase ($result);
    $max      = (int)parse_ase ($result);

    if ($password == 1) {
      $pass = true;
    } else {
      $pass = false;
    };

    switch ($game) {
      case "mta":
        parse_ase ($result);
        parse_ase ($result);

        break;
    };

    while ((int)ord (substr ($result, 0, 1)) > 1) {
      rule_add ($rules, parse_ase ($result), parse_ase ($result));
    };

    rule_add  ($rules, "gamename", $gamename);
    rule_add  ($rules, "gametype", $mod);
    rule_add  ($rules, "Version",  $version);

    read_byte ($result);

    if ($RULES !== true) {
      unset ($rules);
    };

    if ($PLAYER === true) {
      if ($cur > 0) {
        $player  = array ();

        $counter = 0;

        if (@file_exists ("{$BASE}/protocols/player/{$game}.inc.php")) {
          include ("{$BASE}/protocols/player/{$game}.inc.php");
        };
      };
    };

    if (($max > 0) && (! empty ($hostname))) {
      status_add ($status, 'hostname', $hostname);
      status_add ($status, 'map',      $map);
      status_add ($status, 'password', $pass);
      status_add ($status, 'current',  $cur);
      status_add ($status, 'max',      $max);
    };
  };
?>
