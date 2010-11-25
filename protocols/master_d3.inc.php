<?php
  $send     = chr (0xFF) . chr (0xFF) . "getServers"
            . chr (0x00) . chr (0x28) . chr (0x00) . chr (0x01)
            . chr (0x00) . chr (0x00) . chr (0x0A);

  $response = "/\xFF\xFFinfoResponse\\x00/";

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
    while (($variable = read_string ($result, "\0")) != "") {
      $value = read_string ($result, "\0");
    };
  };
?>
