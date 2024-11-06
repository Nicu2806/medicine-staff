<?php
require_once '../config/config.php';
require_once '../libraries/Database.php';

class ShareExperienceHandler
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function handleSubmission()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    {
      return $this->sendResponse(false, 'Invalid request method');
    }

    try
    {
      // Validate all required fields
      $requiredFields = ['firstName', 'lastName', 'email', 'locationName', 'description', 'coordinates'];
      foreach ($requiredFields as $field)
      {
        if (empty($_POST[$field]))
        {
          throw new Exception("Missing required field: $field");
        }
      }

      // Create full name
      $userName = htmlspecialchars($_POST['firstName'] . ' ' . $_POST['lastName']);
      $userEmail = htmlspecialchars($_POST['email']);
      $locationName = htmlspecialchars($_POST['locationName']);
      $locationDescription = htmlspecialchars($_POST['description']);
      $geoLocation = htmlspecialchars($_POST['coordinates']);

      // Get image path from hidden input
      $imagesPath = isset($_POST['imagePath']) ? $_POST['imagePath'] : null;

      // Insert into database
      $data = [
        'user_name' => $userName,
        'user_email' => $userEmail,
        'location_name' => $locationName,
        'location_description' => $locationDescription,
        'geo_location' => $geoLocation,
        'images_path' => $imagesPath,
        'created_at' => date('Y-m-d H:i:s')
      ];

      $success = $this->db->InsertRow('user_shared_experiences', $data);

      if (!$success)
      {
        throw new Exception('Failed to save experience to database');
      }

      // Redirect on success
      header('Location: /shared-experiences.php?status=success');
      exit;

    } catch (Exception $e)
    {
      // Redirect with error
      header('Location: /shared-experiences.php?status=error&message=' . urlencode($e->getMessage()));
      exit;
    }
  }

  private function sendResponse($success, $message, $data = null)
  {
    header('Content-Type: application/json');
    echo json_encode([
      'success' => $success,
      'message' => $message,
      'data' => $data
    ]);
    exit;
  }
}

// Handle the request
$handler = new ShareExperienceHandler();
$handler->handleSubmission();