<?php

class Pages extends Controller
{
  public function index()
  {
    if (isLoggedIn())
    {
      redirect('posts');
    }
    $data = [
      'title' => 'PHP MVC Framework',
      'description' => 'Simple social network built using PHP/MVC.'
    ];
    $this->view('pages/index', $data);
  }

  public function map()
  {
    if (isLoggedIn())
    {
      redirect('posts');
    }
    $data = [
      'title' => 'PHP MVC Framework',
      'description' => 'Simple social network built using PHP/MVC.'
    ];
    $this->view('pages/map', $data);
  }

  // Adăugăm metoda pentru staff-track
  public function staffTrack()
  {

    $data = [
      'title' => 'Staff Tracking System',
      'description' => 'Monitor and track medical staff in real-time'
    ];

    // Folosim handlePage pentru a încărca view-ul
    $this->handlePage('staff-track', $data);
  }

  public function patientWatch()
  {

    $data = [
      'title' => 'Staff Tracking System',
      'description' => 'Monitor and track medical staff in real-time'
    ];

    // Folosim handlePage pentru a încărca view-ul
    $this->handlePage('patient-watch', $data);
  }
}