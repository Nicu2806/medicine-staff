<?php

class AssetHelper
{
  private static $cssFiles = [];
  private static $jsFiles = [];
  private static $version;

  public static function init()
  {
    if ($_SERVER['HTTP_HOST'] === 'travelnotes.free.nf')
    {
      self::$version = defined('ASSETS_VERSION') ? ASSETS_VERSION : '1.0.0';
    } else
    {
      self::$version = date('YmdHi');
    }
  }

  public static function css($path)
  {
    if (!in_array($path, self::$cssFiles))
    {
      self::$cssFiles[] = $path;
    }
  }

  public static function js($path)
  {
    if (!in_array($path, self::$jsFiles))
    {
      self::$jsFiles[] = $path;
    }
  }

  public static function renderCss()
  {
    foreach (self::$cssFiles as $file)
    {
      $url = self::getAssetUrl($file);
      echo sprintf('<link rel="stylesheet" href="%s">' . PHP_EOL,
        htmlspecialchars($url));
    }
  }

  public static function renderJs()
  {
    foreach (self::$jsFiles as $file)
    {
      $url = self::getAssetUrl($file);
      echo sprintf('<script src="%s"></script>' . PHP_EOL,
        htmlspecialchars($url));
    }
  }

  public static function getAssetUrl($path)
  {
    if (strpos($path, 'http') === 0)
    {
      return $path;
    }

    return $path . '?v=' . self::$version;
  }

  public static function debug()
  {
    echo "<!-- CSS Files loaded: -->\n";
    foreach (self::$cssFiles as $file)
    {
      echo "<!-- " . self::getAssetUrl($file) . " -->\n";
    }

    echo "<!-- JS Files loaded: -->\n";
    foreach (self::$jsFiles as $file)
    {
      echo "<!-- " . self::getAssetUrl($file) . " -->\n";
    }
  }
}