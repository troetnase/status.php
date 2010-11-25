<?php
  set_magic_quotes_runtime (10);

  if (! empty ($_SERVER['HTTPS'])) {
    $PROTOCOL = "https";
  } else {
    $PROTOCOL = "http";
  };

  $AUTHOR  = array (
               'author' => 'Christian Celler',
               'email'  => 'titi@tuts.net',
               'url'    => 'http://www.tuts.net/~titulaer'
             );

  $PROJECT = "status";

  $VERSION = array (
               'major'   => 1,
               'minor'   => 0,
               'release' => 2
             );

  $SELF    = "{$PROTOCOL}://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}";
  $DIR     = "{$PROTOCOL}://{$_SERVER['SERVER_NAME']}"
             . dirname ($_SERVER['PHP_SELF']);

  $MODULES = array ();

  add_module ("date");
  add_module ("pcre");
  add_module ("posix");
  add_module ("sockets");

  define ("__READ",  0);
  define ("__WRITE", 1);
  define ("__ALL",   2);

  define ("__DIR",   0777 - umask ());
  define ("__EXEC",  0777 - umask ());
  define ("__FILE",  0666 - umask ());
?>
