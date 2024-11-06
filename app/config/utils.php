<?php

class Utils
{
  private static $metaTags = [];
  private static $canonicalUrl = null;

  public static function PrintGTM()
  {
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9CEY707B8G"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }

      gtag('js', new Date());
      gtag('config', 'G-9CEY707B8G');
    </script>
    <?php
  }

  /**
   * Sets page meta title
   * @param string $title
   */
  public static function SetMetaTitle($title)
  {
    if (!isset($title) || $title == '')
      return;

    global $PAGE_TITLE;
    $PAGE_TITLE = $title;
  }

  /**
   * Gets default page title for the controller
   * @param string $controller
   * @param string $lang
   * @return string
   */
  public static function GetPageTitle($controller, $lang = null)
  {
    if (!isset($lang))
      $lang = self::GetCurrentLanguage();

    $titles = &$GLOBALS['PAGE_TITLES_' . strtoupper($lang)];

    if (isset($controller) && array_key_exists($controller, $titles))
      return $titles[$controller];
    else
      return $titles['#default-value#'];
  }

  /**
   * Prints <title> tag
   * @global string $PAGE_TITLE
   */
  public static function PrintMetaTitleTag()
  {
    global $PAGE_TITLE;
    print '<title>' . (isset($PAGE_TITLE) ? $PAGE_TITLE : SITE_NAME) . '</title>' . PHP_EOL;
  }

  public static function SetMetaDescription($content, $type = 'description', $property = false)
  {
    if (!empty($content))
    {
      if ($property)
      {
        self::SetMetaTag('property', $type, $content);
      } else
      {
        self::SetMetaTag('name', $type, $content);
      }
    }
  }

  public static function SetMetaTag($attributeName, $attributeValue, $content)
  {
    global $META_TAGS;
    if (!isset($META_TAGS))
    {
      $META_TAGS = array();
    }
    if (!isset($META_TAGS[$attributeName]))
    {
      $META_TAGS[$attributeName] = array();
    }
    $META_TAGS[$attributeName][$attributeValue] = $content;
  }

  /**
   * Set canonical URL meta tag
   * @param string $url The canonical URL
   */
  public static function SetMetaCanonical($url)
  {
    self::$canonicalUrl = $url;
    self::SetMetaTag('property', 'og:url', $url);
  }

  /**
   * Set meta property tags (for Open Graph, Twitter Cards, etc)
   * @param string $property The property name
   * @param string $content The property content
   */
  public static function SetMetaProperty($property, $content)
  {
    self::SetMetaTag('property', $property, $content);
  }

  public static function PrintMetaTags()
  {
    global $META_TAGS;

    // Print canonical URL if set
    if (self::$canonicalUrl)
    {
      echo '<link rel="canonical" href="' . htmlspecialchars(self::$canonicalUrl) . '" />' . PHP_EOL;
    }

    if (empty($META_TAGS))
    {
      return;
    }

    foreach ($META_TAGS as $attributeName => $tag)
    {
      foreach ($tag as $attributeValue => $content)
      {
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $attributeValue = htmlspecialchars($attributeValue, ENT_QUOTES, 'UTF-8');
        $attributeName = htmlentities($attributeName, ENT_QUOTES, 'UTF-8');
        print "<meta $attributeName=\"$attributeValue\" content=\"$content\"/>\n";
      }
    }
  }

  /**
   * Helper function to get current language
   * @return string
   */
  private static function GetCurrentLanguage()
  {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
  }

  /**
   * @deprecated use SetMetaTitle
   */
  public static function SetPageTitle($title)
  {
    self::SetMetaTitle($title);
  }

  public static function PrintFavicons()
  {
    ?>
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <?php
  }
}