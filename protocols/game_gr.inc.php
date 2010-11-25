<?php
  // TODO

  $protocol = "gr";

  $send     = chr (0xC0) . chr (0xDE) . chr (0xF1)
            . chr (0x11) . chr (0x42) . chr (0x06)
            . chr (0x00) . chr (0xF5) . chr (0x03)
            . chr (0x00) . chr (0x78) . chr (0x30) . chr (0x63);

  $result   = server_query ($socket, $address, $queryport,
                            $send, $protocol, $response);

  if (! empty ($result)) {
  };
?>
