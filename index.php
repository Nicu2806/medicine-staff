<?php
if ($_SERVER['HTTP_HOST'] === 'travelnotes.free.nf')
{  // doar pe domeniul de producție
  if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')
  {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
  }
}

error_reporting(-1);
ini_set('display_errors', 'On');

require_once './app/bootstrap.php';

$init = new Core();