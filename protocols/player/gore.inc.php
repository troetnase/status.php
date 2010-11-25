<?php
  $header = array (
              'name'   => array ('type' => 'string', 'length' => 32, 'width' => 60),
              'skill'  => array ('type' => 'int',    'length' => 5,  'width' => 10),
              'frags'  => array ('type' => 'int',    'length' => 5,  'width' => 10),
              'deaths' => array ('type' => 'int',    'length' => 6,  'width' => 12),
              'ping'   => array ('type' => 'int',    'length' => 4,  'width' => 8)
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
    } elseif (preg_match ("/skill_([\d]{1,2})/i", $setting, $count)) {
      $player[$count[1]]['skill'] = read_string ($result, "\\");
    } elseif (preg_match ("/ping_([\d]{1,2})/i", $setting, $count)) {
      $player[$count[1]]['ping'] = read_string ($result, "\\");
    } else {
      read_string ($result, "\\");
    };
  };
?>
