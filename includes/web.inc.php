<?php
  function head ($title = NULL, $refresh = NULL) {
    global $SELF;

    print ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\""
           . " \"http://www.w3.org/TR/html4/loose.dtd\">\n");

    print ("<html>\n");
    print (" <head>\n");
    print ("  <link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"images/icon.ico\">\n");
    print ("  <style type=\"text/css\">\n");
    print ("   body      { background-color:#FFFFFF; font-family:Arial; }\n");
    print ("   a:link    { color:#000000; }\n");
    print ("   a:visited { color:#000000; }\n");
    print ("   a:active  { color:#000000; }\n");
    print ("  </style>\n");

    if (@isset ($refresh)) {
      if ((@array_key_exists ("QUERY_STRING", $_SERVER))
       && (! empty ($_SERVER['QUERY_STRING']))) {
        print ("  <meta http-equiv=\"refresh\" content=\"{$refresh}; URL={$SELF}?{$_SERVER['QUERY_STRING']}\">\n");
      } else {
        print ("  <meta http-equiv=\"refresh\" content=\"{$refresh}; URL={$SELF}\">\n");
      };
    };

    if (@isset ($title)) {
      print ("  <title>{$title}</title>\n");
    } else {
      print ("  <title>Server-Infos</title>\n");
    };

    print (" </head>\n");
    print (" <body>\n");
    print (" <script type=\"text/javascript\">\n");
    print ("  var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n");
    print ("  document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n");
    print (" </script>\n");
    print (" <script type=\"text/javascript\">\n");
    print ("  var pageTracker = _gat._getTracker(\"UA-5221846-2\");\n");
    print ("  pageTracker._trackPageview();\n");
    print (" </script>\n");
  };

  function footer () {
    global $AUTHOR, $PROJECT, $VERSION;

    print ("  <p align=\"center\"><font size=\"-2\">Version {$VERSION['major']}.{$VERSION['minor']}-{$VERSION['release']}, written by {$AUTHOR['author']} &lt;{$AUTHOR['email']}&gt;<br><a href=\"{$AUTHOR['url']}/{$PROJECT}-{$VERSION['major']}.{$VERSION['minor']}-{$VERSION['release']}.tar.gz\">Download</a> latest version</font></p>\n");

    print (" </body>\n");
    print ("</html>\n");
  };

  function get ($key, $regex = NULL) {
    if (@array_key_exists ($key, $_GET)) {
      if (@isset ($regex)) {
        if (preg_match ($regex, $_GET[$key])) {
          return $_GET[$key];
        };
      } else {
        return $_GET[$key];
      };
    };

    return NULL;
  };

  function escape_html ($string) {
    global $ENCODING;

    if (@is_array ($ENCODING)) {
      $encoding = mb_detect_encoding ($string, "ASCII, ISO-8859-1, UTF-8");

      if ($encoding != $ENCODING['strings']) {
        $string = mb_convert_encoding ($string, $ENCODING['strings']);
      };
    };

    return (htmlspecialchars ($string, ENT_QUOTES));
  };
?>
