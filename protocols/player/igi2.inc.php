<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 67),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 11),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 13),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 9)
            );

  while (($setting = read_string ($result, "\\")) != "") {
    if ((strtolower ($setting)) == "queryid") {
      read_string ($result, "\\");

      continue;
    } elseif ((strtolower ($setting)) == "final") {
      break;
    };

    if (preg_match ("/player_([\d]{1,2})/i", $setting, $count)) {
      $player[$count[1]]         = array ();

      $player[$count[1]]['name'] = read_string ($result, "\\");
    } elseif (preg_match ("/frags_([\d]{1,2})/i", $setting, $count)) {
      $player[$count[1]]['frags'] = read_string ($result, "\\");
    } elseif (preg_match ("/deaths_([\d]{1,2})/i", $setting, $count)) {
      $player[$count[1]]['deaths'] = read_string ($result, "\\");
    } elseif (preg_match ("/ping_([\d]{1,2})/i", $setting, $count)) {
      $player[$count[1]]['ping'] = read_string ($result, "\\");
    } else {
      read_string ($result, "\\");
    };
  };
?>
