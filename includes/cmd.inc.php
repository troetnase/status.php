<?php
  if (@file_exists ("{$BASE}/protocols/functions.inc.php")) {
    include ("./protocols/functions.inc.php");
  } else {
    die ("No protocol-information found!\n");
  };

  function show_list (&$games) {
    if (@is_array ($games)) {
      ksort ($games);

      print ("Available games:\n");
      print (m_str_pad ("Game", 40) . "   Shortname (Port(:Queryport))\n");

      foreach ($games as $game => $value) {
        if (@array_key_exists ("queryport", $value)) {
          if (@is_numeric ($value['queryport'])) {
            print (m_str_pad (encoding ($value['game']), 40) . " - "
                   . m_str_pad ($game, 9 , " ", STR_PAD_LEFT)
                   . " ({$value['gameport']}:{$value['queryport']})"
                   . "\n");
          } elseif (preg_match ("/[+|-]/", $games[$game]['queryport'])) {
            $queryport = eval ("return ({$value['gameport']} "
                               . $value['queryport'] . ");");

            print (m_str_pad ($value['game'], 40) . " - "
                   . m_str_pad ($game, 9 , " ", STR_PAD_LEFT)
                   . " ({$value['gameport']}:{$queryport})"
                   . "\n");
          };
        } else {
          print (m_str_pad ($value['game'], 40) . " - "
                 . m_str_pad ($game, 9 , " ", STR_PAD_LEFT)
                 . " ({$value['gameport']})"
                 . "\n");
        };
      };
    };
  };

  function usage () {
    die ("Usage: {$_SERVER['argv'][0]} [--ip IP --game GAME [--port PORT] [--rules] [--player]|--list] [--debug]\n");
  };

  $ENCODING = array (
                'module'  => 'mbstring',
                'strings' => 'UTF-8'
              );
?>
