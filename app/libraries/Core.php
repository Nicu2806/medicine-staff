<?php

class Core
{
  protected $currentController = 'Pages';
  protected $currentMethod = 'index';
  protected $params = [];

  public function __construct()
  {
    // Get full URL and parsed segments
    $url = $this->getUrl();
    $current_url = $_SERVER['REQUEST_URI'];
    $path = parse_url($current_url, PHP_URL_PATH);
    $segments = array_filter(explode('/', trim($path, "/")));

    // Process controller
    if (!empty($url) && isset($url[0]))
    {
      if (file_exists('./app/controllers/' . ucwords($url[0]) . '.php'))
      {
        $this->currentController = ucwords($url[0]);
        unset($url[0]);
      }
    }

    // Load and validate controller
    $controllerPath = './app/controllers/' . $this->currentController . '.php';
    if (!file_exists($controllerPath))
    {
      throw new Exception("Controller file not found: {$controllerPath}");
    }

    require_once $controllerPath;

    if (!class_exists($this->currentController))
    {
      throw new Exception("Controller class not found: {$this->currentController}");
    }

    // Instantiate controller
    $this->currentController = new $this->currentController;

    // Get method from URL or last segment
    if (!empty($url) && isset($url[1]))
    {
      if (method_exists($this->currentController, $url[1]))
      {
        $this->currentMethod = $url[1];
        unset($url[1]);
      }
    } else if (!empty($segments))
    {
      // Use last segment as method if no method is set
      $lastSegment = end($segments);
      if (method_exists($this->currentController, $lastSegment))
      {
        $this->currentMethod = $lastSegment;
      }
    }

    // Get remaining parameters
    $this->params = $url ? array_values($url) : [];

    // Add any remaining segments as parameters
    foreach ($segments as $segment)
    {
      if ($segment !== $this->currentController &&
        $segment !== $this->currentMethod &&
        !in_array($segment, $this->params))
      {
        $this->params[] = $segment;
      }
    }

    // Verify method exists
    if (!method_exists($this->currentController, $this->currentMethod))
    {
      throw new Exception("Method {$this->currentMethod} not found in controller " . get_class($this->currentController));
    }

    // Call the method with parameters
    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
  }

  public function getUrl()
  {
    if (isset($_GET['url']))
    {
      $url = rtrim($_GET['url'], '/');
      $url = filter_var($url, FILTER_SANITIZE_URL);
      if ($url === false)
      {
        return [];
      }
      return explode('/', $url);
    }
    return [];
  }
}