<?php
  function read_string (&$string, $delim) {
    if ((substr ($string, 0, strlen ($delim))) == $delim) {
      $value  = NULL;
      $string = substr ($string, strlen ($delim));
    } else {
      $pos = strpos ($string, $delim);

      if ($pos !== false) {
        $value  = substr ($string, 0, $pos);
        $string = substr ($string, $pos + strlen ($delim));
      } else {
        $value  = $string;
        $string = NULL;
      };
    };

    return $value;
  };

  function read_bytes (&$string, $length) {
    if ((strlen ($string)) > $length) {
      $value  = substr ($string, 0, $length);
      $string = substr ($string, $length);
    } elseif ((strlen ($string)) > 0) {
      $value  = $string;
      $string = NULL;
    } else {
      $value = NULL;
    };

    return $value;
  };

  function read_byte (&$string) {
    $value  = substr ($string, 0, 1);
    $string = substr ($string, 1);

    return $value;
  };

  function read_short (&$string) {
    $value = @unpack ("n*", (read_bytes ($string, 2)));

    return $value[1];
  };

  function read_word (&$string) {
    $value = @unpack ("S", (read_bytes ($string, 2)));

    return $value[1];
  };

  function read_long (&$string) {
    $value = @unpack ("l", (read_bytes ($string, 4)));

    return $value[1];
  };

  function read_unsigned (&$string) {
    $value = @unpack ("L", (read_bytes ($string, 4)));

    return $value[1];
  };

  function read_float (&$string) {
    $value = @unpack ("f", (read_bytes ($string, 4)));

    return $value[1];
  };

  function unsigned_to_string ($value) {
    return (pack ("L", $value));
  };

  function long_to_string ($value) {
    return (pack ("l", $value));
  };

  function word_to_string ($value) {
    return (pack ("S", $value));
  };

  function read_last_byte (&$string) {
    $value  = substr ($string, -1);
    $string = substr ($string, 0, -1);

    return $value;
  };

  function read_last_string (&$string, $delim) {
    if ((substr ($string, (strlen ($delim) * -1))) == $delim) {
      $value  = NULL;
      $string = substr ($string, 0, (strlen ($delim) * -1));
    } else {
      $pos = strrpos ($string, $delim);

      if ($pos !== false) {
        $value  = substr ($string, $pos + strlen ($delim));
        $string = substr ($string, 0, $pos);
      } else {
        $value  = $string;
        $string = NULL;
      };
    };

    return $value;
  };
?>
