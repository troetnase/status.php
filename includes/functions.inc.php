<?php
  function php_compare () {
    global $REQUIRED;

    if (@isset ($REQUIRED)) {
      if ((version_compare (PHP_VERSION, $REQUIRED)) == -1) {
        die ("Your php is too old, please update to version"
             . " {$REQUIRED} at least!\n");
      };
    };
  };

  function read_file ($file) {
    if ((@is_file ($file)) && (@is_readable ($file))) {
      return (@file_get_contents ($file));
    };

    return false;
  };

  function write_file ($file, $content = "", $mode = "w") {
    if (! empty ($file)) {
      if (! @is_dir (dirname ($file))) {
        if ((make_directory (dirname ($file))) !== true) {
          return false;
        };
      };

      if (@is_writable (dirname ($file))) {
        if ((@is_file ($file)) || (@is_link ($file))) {
          if ((delete ($file)) !== true) {
            return false;
          };
        };

        $fp = @fopen ($file, $mode);

        if ($fp !== false) {
          if ((@fwrite ($fp, $content, strlen ($content))) !== false) {
            @fclose ($fp);

            return true;
          };

          @fclose ($fp);
        };
      };
    };

    return false;
  };

  function make_directory ($directory, $mode = __DIR) {
    if (! empty ($directory)) {
      if (@file_exists ($directory)) {
        if (@is_dir ($directory)) {
          return true;
        };
      } else {
        if (@is_dir (dirname ($directory))) {
          if ((@mkdir ($directory, $mode)) !== false) {
            return true;
          };
        } else {
          if (! @file_exists (dirname ($directory))) {
            if ((make_directory (dirname ($directory), $mode)) !== false) {
              if ((@mkdir ($directory, $mode)) !== false) {
                return true;
              };
            };
          };
        };
      };
    };

    return false;
  };

  function delete ($parent) {
    @clearstatcache ();

    if (@is_link ($parent)) {
      if ((@unlink ($parent)) === true) {
        return true;
      };
    } elseif (@file_exists ($parent)) {
      if (@is_dir ($parent)) {
        if (($directory = @opendir ($parent)) !== false) {
          while (($content = @readdir ($directory)) !== false) {
            if (($content != ".") && ($content != "..")) {
              if ((delete ("{$parent}/{$content}")) === false) {
                @closedir ($directory);

                return false;
              };
            };
          };

          @closedir ($directory);

          if ((@rmdir ($parent)) === true) {
            return true;
          };
        };
      } else {
        if ((@unlink ($parent)) === true) {
          return true;
        };
      };
    } else {
      return true;
    };

    return false;
  };

  function mv ($source, $dest) {
    global $DEBUG;

    if ($DEBUG === true) {
      print ("Copy-source     : {$source}\n");
      print ("Copy-destination: {$dest}\n");
    };

    if (@file_exists ($source)) {
      if ((@is_dir ($dest)) === false) {
        if ((make_directory ($dest)) === false) {
          return false;
        };
      };

      if (@is_link ($source)) {
        if (($link = @readlink ($source)) !== false) {
          if ((mv ($link, $dest)) === false) {
            return false;
          };
        };
      } else {
        if ((@rename ($source, "{$dest}/" . basename ($source))) === false) {
          return false;
        };
      };
    };

    return true;
  };

  function cp ($source, $dest) {
    global $DEBUG;

    if ($DEBUG === true) {
      print ("Copy-source     : {$source}\n");
      print ("Copy-destination: {$dest}\n");
    };

    if (@file_exists ($source)) {
      if ((@is_dir ($dest)) === false) {
        if ((make_directory ($dest)) === false) {
          return false;
        };
      };

      if (@is_link ($source)) {
        if (($link = @readlink ($source)) !== false) {
          if ((cp ($link, $dest)) === false) {
            return false;
          };
        };
      } elseif (@is_dir ($source)) {
        if ((make_directory ("{$dest}/" . basename ($source))) !== false ) {
          if (($directory = @opendir ($source)) !== false) {
            while (($entry = @readdir ($directory)) !== false) {
              if (($entry != ".") && ($entry != "..")) {
                if ((cp ("{$source}/{$entry}", "{$dest}/"
                         . basename ($source))) === false) {
                  @closedir ($directory);

                  return false;
                };
              };
            };

            @closedir ($directory);
          } else {
            return false;
          };
        } else {
          return false;
        };
      } elseif (@is_file ($source)) {
        if ((@copy ($source, "{$dest}/" . basename ($source))) === false) {
          return false;
        };
      };
    };

    return true;
  };

  function is_empty_dir ($dir) {
    @clearstatcache ();

    if (@is_dir ($dir)) {
      if (($directory = @opendir ($dir)) !== false) {
        while (($content = @readdir ($directory)) !== false) {
          if (($content != ".") && ($content != "..")) {
            @closedir ($directory);

            return false;
          };
        };

        @closedir ($directory);
      };
    };

    return true;
  };

  function add_module ($module) {
    if (DEBUG === true) {
      print ("Checking for module {$module}\n");
    };

    if ((extension_loaded ($module)) === false) {
      if ((@dl ("{$module}.so")) === false) {
        die ("Extension {$module} not found!\n");
      };
    };
  };

  function is_ip ($ip) {
    if (preg_match ("/^((([01]?\d\d?|2[0-4]\d|25[0-5])\.){3}([01]?\d\d?|2[0-4]\d|25[0-5]))$/", $ip)) {
      return true;
    };

    return false;
  };

  function net_match ($address, $cidr) {
    list ($net, $mask) = explode ("/", $cidr);

    return (ip2long ($address) >> (32 - $mask) == ip2long ($net) >> (32 - $mask));
  };

  function get_cidr_from_mask ($mask) {
    return (32 - (strlen (decbin ((ip2long ($mask) * -1) -1))));
  };

  function get_mask_from_cidr ($cidr) {
    return (long2ip (bindec (str_repeat (1, $cidr)
                             . str_repeat (0, 32 - $cidr))));
  };

  function get_opt ($parameter, &$value = NULL, $pattern = NULL) {
    for ($i = 1; $i < $_SERVER['argc']; $i++) {
      if (strval ($_SERVER['argv'][$i]) == $parameter) {
        if ((func_num_args ()) >= 2) {
          if (@isset ($pattern)) {
            if (preg_match ($pattern, $_SERVER['argv'][$i+1])) {
              $value = $_SERVER['argv'][$i+1];

              return true;
            };
          } else {
            $value = $_SERVER['argv'][$i+1];

            return true;
          };
        } else {
          return true;
        };
      };
    };

    return false;
  };

  function del_opt ($parameter, $value = false) {
    for ($i = 0; $i < $_SERVER['argc']; $i++) {
      if (strval ($_SERVER['argv'][$i]) == $parameter) {

        if ($value === true) {
          array_splice ($_SERVER['argv'], $i, 2);

          $_SERVER['argc'] = $_SERVER['argc'] - 2;
        } else {
          array_splice ($_SERVER['argv'], $i, 1);

          $_SERVER['argc'] = $_SERVER['argc'] - 1;
        };

        break;
      };
    };
  };

  function signals ($signal) {
    switch ($signal) {
      case SIGTERM:
      case SIGINT:
        if ((defined ("CYCLE")) === true) {
          if ((defined ("TERM")) === true) {
            del_pid ();

            die     ("Exiting due to signal {$signal}!\n");
          } else {
            print  ("Caught signal {$signal}, cleaning up!\n");

            define ("TERM", true);
          };
        } else {
          del_pid ();

          die     ("Exiting due to signal {$signal}!\n");
        };

        break;

      case SIGCHLD:
        global $CHILDREN;

        $CHILDREN = $CHILDREN - 1;

        break;
    };
  };

  function set_encoding () {
    global $ENCODING;

    if ((@is_array ($ENCODING))
     && (@array_key_exists ("strings", $ENCODING))) {
      if (DEBUG === true) {
        print ("Default-Charset : {$ENCODING['strings']}\n");
      };

      ini_set ("default_charset", $ENCODING['strings']);
    };

    if ((@is_array ($ENCODING))
     && (@array_key_exists ("module", $ENCODING))) {
      if (DEBUG === true) {
        print ("Default-Encoding: {$ENCODING['strings']}\n");
        print ("Encoding via    : {$ENCODING['module']}\n");
      };

      add_module ($ENCODING['module']);

      switch (strtolower ($ENCODING['module'])) {
        case "iconv":
          iconv_set_encoding ("input_encoding",    $ENCODING['strings']);
          iconv_set_encoding ("output_encoding",   $ENCODING['strings']);
          iconv_set_encoding ("internal_encoding", $ENCODING['strings']);

          break;

        case "mbstring":
          mb_detect_order      ("ASCII, UTF-8, Windows-1252, ISO-8859-1");
          mb_internal_encoding ($ENCODING['strings']);

          break;
      };
    };
  };

  function encoding ($string, $from = "ISO-8859-1") {
    global $ENCODING;

    if ((@is_array ($ENCODING))
     && (@array_key_exists ("strings", $ENCODING))) {
      $to = $ENCODING['strings'];
    } else {
      $to = "UTF-8";
    };

    if ((@is_array ($ENCODING))
     && (@array_key_exists ("module", $ENCODING))) {
      switch (strtolower ($ENCODING['module'])) {
        case "recode":
          $string = recode ("{$from}..{$to}", $string);

          break;

        case "iconv":
          $string = iconv ($from, $to, $string);

          break;

        case "mbstring":
          $encoding = mb_detect_encoding ($string);

          if ($encoding != $ENCODING['strings']) {
            $string = mb_convert_encoding ($string, $to, $encoding);
          };

          break;
      };
    };

    return $string;
  };

  function m_str_pad ($string, $length, $pad = " ",
                      $type = STR_PAD_RIGHT, $encoding = NULL) {
    global $ENCODING;

    if ((@is_array ($ENCODING))
     && (@array_key_exists ("module", $ENCODING))) {
      switch (strtolower ($ENCODING['module'])) {
        case "recode":
          return (str_pad ($string, $length, $pad, $type));

          break;

        case "iconv":
          if ((function_exists ("iconv_strlen")) === true) {
            if (! @isset ($encoding)) {
              $encoding = iconv_get_encoding ("internal_encoding");
            };

            $iconv_length = $length - (iconv_strlen ($string, $encoding));

            if ($iconv_length <= 0) {
              return $string;
            };

            $pad = str_repeat ($pad, $iconv_length);

            switch ($type) {
              case STR_PAD_RIGHT:
                $pad = iconv_substr ($pad, 0, $iconv_length, $encoding);

                return ($string . $pad);

                break;

              case STR_PAD_LEFT:
                $pad = iconv_substr ($pad, 0, $iconv_length, $encoding);

                return ($pad . $string);

                break;

              case STR_PAD_BOTH:
                $left  = iconv_substr ($pad, 0, floor ($iconv_length / 2), $encoding);
                $right = iconv_substr ($pad, 0, ceil  ($iconv_length / 2), $encoding);

                return ($left . $string . $right);

                break;
            };
          } else {
            return (str_pad ($string, $length, $pad, $type));
          };

          break;

        case "mbstring":
          if ((function_exists ("mb_strlen")) === true) {
            if (! @isset ($encoding)) {
              $encoding = mb_internal_encoding ();
            };

            $mb_length = $length - (mb_strlen ($string, $encoding));

            if ($mb_length <= 0) {
              return $string;
            };

            $pad = str_repeat ($pad, $mb_length);

            switch ($type) {
              case STR_PAD_RIGHT:
                $pad = mb_substr ($pad, 0, $mb_length, $encoding);

                return ($string . $pad);

                break;

              case STR_PAD_LEFT:
                $pad = mb_substr ($pad, 0, $mb_length, $encoding);

                return ($pad . $string);

                break;

              case STR_PAD_BOTH:
                $left  = mb_substr ($pad, 0, floor ($mb_length / 2), $encoding);
                $right = mb_substr ($pad, 0, ceil  ($mb_length / 2), $encoding);

                return ($left . $string . $right);

                break;
            };
          } else {
            return (str_pad ($string, $length, $pad, $type));
          };

          break;
      };
    } else {
      return (str_pad ($string, $length, $pad, $type));
    };

    return false;
  };
?>
