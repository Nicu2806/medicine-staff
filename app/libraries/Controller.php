<?php

class Controller
{
  // Metodele existente rămân la fel (model și view)
  public function model($model)
  {
    require_once './app/models/' . $model . '.php';
    return new $model();
  }

  public function view($view, $data = [])
  {
    if (file_exists('./app/views/' . $view . '.php'))
    {
      require_once('./app/views/' . $view . '.php');
    } else if ($view == 'pages/admin/shared-experiences.php')
    {
      require_once('app/views/pages/admin/shared-experiences.php');
    } else
    {
      die('View does not exists');
    }
  }

  // Metodă modificată pentru a include și staff-track
  protected function handlePage($pageName, $data)
  {
    $validPages = ['index', 'map', 'staff-track', 'patient-watch', 'data-reports']; // Adăugăm staff-track în paginile valide

    if (in_array($pageName, $validPages) && file_exists('./app/views/pages/' . $pageName . '.php'))
    {
      $this->view('pages/' . $pageName, $data);
    } else
    {
      $this->view('pages/index', $data);
    }
  }
}
