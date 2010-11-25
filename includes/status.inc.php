<?php
  if (@file_exists ("{$BASE}/protocols/functions.inc.php")) {
    include ("./protocols/functions.inc.php");
  } else {
    die ("No protocol-information found!\n");
  };

  function form ($message = NULL) {
    global $address, $game, $port, $queryport, $games, $RULES, $PLAYER, $SELF;

    print ("  <form action=\"{$SELF}\" method=\"get\">\n");
    print ("  <table width=\"100%\" cellpadding=\"0\""
           . " cellspacing=\"0\" border=\"0\">\n");

    print ("   <tr>\n");
    print ("    <td width=\"15%\">&nbsp;</td>\n");
    print ("    <td width=\"70%\" align=\"center\"><img src=\"images/status.png\" alt=\"Status\"></td>\n");
    print ("    <td width=\"15%\">&nbsp;</td>\n");
    print ("   </tr>\n");
    print ("   <tr>\n");
    print ("    <td width=\"100%\" colspan=\"3\">&nbsp;</td>\n");
    print ("   </tr>\n");

    if (@isset ($message)) {
      print ("   <tr>\n");
      print ("    <td width=\"100%\" colspan=\"3\""
             . "align=\"center\">{$message}</td>\n");

      print ("   </tr>\n");
      print ("   <tr>\n");
      print ("    <td width=\"100%\" colspan=\"3\">&nbsp;</td>\n");
      print ("   </tr>\n");
    };

    print ("   <tr>\n");
    print ("    <td width=\"15%\">&nbsp;</td>\n");
    print ("    <td width=\"70%\" align=\"center\">\n");
    print ("     <table width=\"50%\" cellpadding=\"0\""
           . " cellspacing=\"0\" border=\"0\">\n");
    print ("      <tr>\n");
    print ("       <td width=\"25%\">IP:</td>\n");
    print ("       <td width=\"75%\" colspan=\"2\">\n");

    if (@isset ($address)) {
      print ("        <input type=\"text\" name=\"ip\" size=\"40\" value=\"{$address}\">\n");
    } else {
      print ("        <input type=\"text\" name=\"ip\" size=\"40\">\n");
    };

    print ("       </td>\n");
    print ("      </tr>\n");

    print ("      <tr>\n");
    print ("       <td width=\"25%\">Serverport:</td>\n");
    print ("       <td width=\"75%\" colspan=\"2\">\n");

    if (@isset ($port)) {
      print ("        <input type=\"text\" name=\"port\""
             . " size=\"40\" value=\"{$port}\">\n");
    } else {
      print ("        <input type=\"text\" name=\"port\" size=\"40\">\n");
    };

    print ("       </td>\n");
    print ("      </tr>\n");

    print ("      <tr>\n");
    print ("       <td width=\"25%\">Queryport:</td>\n");
    print ("       <td width=\"75%\" colspan=\"2\">\n");

    if (@isset ($queryport)) {
      print ("        <input type=\"text\" name=\"queryport\""
             . " size=\"40\" value=\"{$queryport}\">\n");
    } else {
      print ("        <input type=\"text\" name=\"queryport\" size=\"40\">\n");
    };

    print ("       </td>\n");
    print ("      </tr>\n");

    print ("      <tr>\n");
    print ("       <td width=\"25%\">Game:</td>\n");
    print ("       <td width=\"75%\" colspan=\"2\">\n");
    print ("        <select name=\"game\">\n");

    foreach ($games as $key => $value) {
      if ($game == $key) {
        print ("         <option value=\"{$key}\" selected>"
               . escape_html ($value['game']) . "</option>\n");
      } else {
        print ("         <option value=\"{$key}\">"
               . escape_html ($value['game']) . "</option>\n");
      };
    };

    print ("        </select>\n");
    print ("       </td>\n");
    print ("      </tr>\n");

    print ("      <tr>\n");
    print ("       <td width=\"25%\">Rules:</td>\n");
    print ("       <td width=\"75%\" colspan=\"2\">\n");

    if ($RULES === true) {
      print ("        <input type=\"checkbox\" name=\"flags[rules]\""
             . " value=\"true\" checked>\n");
    } else {
      print ("        <input type=\"checkbox\" name=\"flags[rules]\""
             . " value=\"true\">\n");
    };

    print ("       </td>\n");
    print ("      </tr>\n");
    print ("      <tr>\n");
    print ("       <td width=\"25%\">Player:</td>\n");
    print ("       <td width=\"25%\">\n");

    if ($PLAYER === true) {
      print ("        <input type=\"checkbox\" name=\"flags[player]\""
             . " value=\"true\" checked>\n");
    } else {
      print ("        <input type=\"checkbox\" name=\"flags[player]\""
             . " value=\"true\">\n");
    };

    print ("       </td>\n");
    print ("       <td width=\"50%\" align=\"right\">\n");
    print ("        <input type=\"submit\" value=\"Query\">\n");
    print ("       </td>\n");
    print ("      </tr>\n");
    print ("     </table>\n");
    print ("    </td>\n");
    print ("    <td width=\"15%\">&nbsp;</td>\n");
    print ("   </tr>\n");
    print ("  </table>\n");
    print ("  </form>\n");
  };

  $ENCODING = array (
                'module'  => 'mbstring',
                'strings' => 'UTF-8'
              );
?>
