<?php

class UserSharedExperience
{
  private $db;
  private $table = 'user_shared_experiences';

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  /**
   * Save a new user shared experience to the database
   *
   * @param array $data The experience data
   * @return array Response with status and message
   */
  public function saveExperience($data)
  {
    try
    {
      $values = [
        'location_description' => $data['location_description'],
        'geo_location' => $data['coordinates'],
        'location_name' => $data['location_name'],
        'user_name' => $data['user_name'],
        'user_email' => $data['user_email'],
        'images_path' => $data['imagePath']
      ];

      if ($this->db->InsertRow($this->table, $values))
      {
        return [
          'success' => true,
          'message' => 'Experience shared successfully',
          'id' => $this->db->GetLastInsertId()
        ];
      } else
      {
        throw new Exception("Failed to save experience");
      }

    } catch (Exception $e)
    {
      error_log("Error saving experience: " . $e->getMessage());
      return [
        'success' => false,
        'message' => 'Failed to save experience: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Validate experience data before saving
   *
   * @param array $data The data to validate
   * @return array Validation result with status and errors
   */
  public function validateData($data)
  {
    $errors = [];

    if (empty($data['location_name']))
    {
      $errors[] = "Location name is required";
    }

    if (empty($data['location_description']))
    {
      $errors[] = "Location description is required";
    }

    if (empty($data['coordinates']))
    {
      $errors[] = "Coordinates are required";
    }

    if (empty($data['user_name']))
    {
      $errors[] = "User name is required";
    }

    if (empty($data['user_email']))
    {
      $errors[] = "Email is required";
    } elseif (!filter_var($data['user_email'], FILTER_VALIDATE_EMAIL))
    {
      $errors[] = "Invalid email format";
    }

    if (empty($data['imagePath']))
    {
      $errors[] = "Image is required";
    }

    return [
      'isValid' => empty($errors),
      'errors' => $errors
    ];
  }

  /**
   * Get an experience by ID
   *
   * @param int $id The experience ID
   * @return object|null The experience data or null if not found
   */
  public function getExperienceById($id)
  {
    try
    {
      $where = "id = " . $this->db->FormatSqlString($id);
      $result = $this->db->SelectRow($this->table, '*', $where, 'LIMIT 1');
      return !empty($result) ? $result[0] : null;
    } catch (Exception $e)
    {
      error_log("Error fetching experience: " . $e->getMessage());
      return null;
    }
  }

  /**
   * Get all experiences
   *
   * @param int $limit Optional limit
   * @param int $offset Optional offset
   * @return array Array of experiences
   */
  public function getAllExperiences($limit = null, $offset = null)
  {
    try
    {
      $suffix = '';
      if ($limit !== null)
      {
        $suffix = "LIMIT $limit";
        if ($offset !== null)
        {
          $suffix .= " OFFSET $offset";
        }
      }

      return $this->db->SelectRow($this->table, '*', '1', "ORDER BY created_at DESC $suffix");
    } catch (Exception $e)
    {
      error_log("Error fetching experiences: " . $e->getMessage());
      return [];
    }
  }

  /**
   * Update an existing experience
   *
   * @param int $id Experience ID
   * @param array $data Updated data
   * @return array Response with status and message
   */
  public function updateExperience($id, $data)
  {
    try
    {
      $values = array_intersect_key($data, array_flip([
        'location_description',
        'geo_location',
        'location_name',
        'user_name',
        'user_email',
        'images_path'
      ]));

      $where = "id = " . $this->db->FormatSqlString($id);

      if ($this->db->UpdateRow($this->table, $values, $where))
      {
        return [
          'success' => true,
          'message' => 'Experience updated successfully'
        ];
      } else
      {
        throw new Exception("Failed to update experience");
      }
    } catch (Exception $e)
    {
      error_log("Error updating experience: " . $e->getMessage());
      return [
        'success' => false,
        'message' => 'Failed to update experience: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Delete an experience
   *
   * @param int $id Experience ID
   * @return array Response with status and message
   */
  public function deleteExperience($id)
  {
    try
    {
      $where = "id = " . $this->db->FormatSqlString($id);

      if ($this->db->DeleteRow($this->table, $where))
      {
        return [
          'success' => true,
          'message' => 'Experience deleted successfully'
        ];
      } else
      {
        throw new Exception("Failed to delete experience");
      }
    } catch (Exception $e)
    {
      error_log("Error deleting experience: " . $e->getMessage());
      return [
        'success' => false,
        'message' => 'Failed to delete experience: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Count total experiences
   *
   * @return int Total number of experiences
   */
  public function getTotalCount()
  {
    return $this->db->CountRows($this->table);
  }
}