<?php
// app/helpers/ExperienceHandler.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../libraries/Database.php';
require_once __DIR__ . '/../models/UserSharedExperience.php';

class ExperienceHandler
{
  public static function handleExperienceSubmission()
  {
    header('Content-Type: application/json');

    try
    {
      // Initialize database and UserSharedExperience class
      $database = new Database();
      $userExperience = new UserSharedExperience($database);

      // Prepare the experience data
      $experienceData = [
        'location_name' => $_POST['locationName'] ?? '',
        'location_description' => $_POST['description'] ?? '',
        'coordinates' => $_POST['coordinates'] ?? '',
        'user_name' => trim($_POST['firstName'] . ' ' . $_POST['lastName']),
        'user_email' => $_POST['email'] ?? '',
        'imagePath' => $_POST['imagePath'] ?? ''
      ];

      // Validate the data
      $validation = $userExperience->validateData($experienceData);

      if (!$validation['isValid'])
      {
        http_response_code(400);
        echo json_encode([
          'success' => false,
          'message' => 'Validation failed',
          'errors' => $validation['errors']
        ]);
        return;
      }

      // Save the experience
      $result = $userExperience->saveExperience($experienceData);

      if ($result['success'])
      {
        http_response_code(200);
      } else
      {
        http_response_code(500);
      }

      echo json_encode($result);

    } catch (Exception $e)
    {
      error_log("Error in ExperienceHandler: " . $e->getMessage());
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
      ]);
    }
  }
}
// Handle POST request

?>