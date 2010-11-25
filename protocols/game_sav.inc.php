<?php
  $protocol = "sav";

  $send     = chr (0x9E) . chr (0x4C) . chr (0x23)
            . chr (0x00) . chr (0x00) . chr (0xC8)
            . "STAT";

  $response = "/^\x9E\x4C\x23\\x00\\x00\xC9.STAT\xFF/s";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    while (($variable = read_string ($result, chr (0xFE))) != "") {
      $value = preg_replace ("/\^[gbywmk]/", "",
                             read_string ($result, chr (0xFF)));

      if ($RULES === true) {
        rule_add ($rules, $variable, $value);
      };

      switch (strtolower ($variable)) {
        case "name":
          $hostname = trim (preg_replace ("/\^[\d]{3}|\^clan\s*[\d]+\^"
                                          . "|\^icon\s+[\da-z\/\-_\.\\\]+\^/i", "", $value));

          break;

        case "cnum":
          $cur = (int)$value;

          break;

        case "cmax":
          $max = (int)$value;

          break;

        case "world":
          $map = $value;

          break;

        case "pass":
          if ((int)$value == 1) {
            $pass = true;
          } else {
            $pass = false;
          };

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
