<?php
// Prevent any output before JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Ensure clean output buffer
ob_start();

// Set proper JSON content type header
header('Content-Type: application/json');

// Disable error display but log them
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

class ImageUploadHandler
{
  private $uploadDir;
  private $monthsInRomanian = [
    1 => 'ianuarie',
    2 => 'februarie',
    3 => 'martie',
    4 => 'aprilie',
    5 => 'mai',
    6 => 'iunie',
    7 => 'iulie',
    8 => 'august',
    9 => 'septembrie',
    10 => 'octombrie',
    11 => 'noiembrie',
    12 => 'decembrie'
  ];

  public function __construct()
  {
    // Get current date components
    $year = date('Y');
    $month = $this->monthsInRomanian[(int)date('m')];
    $day = date('d');

    // Create directory path
    $datePath = "$year-$month-$day";
    $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/files/temp/$datePath/";

    // Create directories if they don't exist
    if (!file_exists($this->uploadDir))
    {
      if (!@mkdir($this->uploadDir, 0777, true))
      {
        throw new Exception("Failed to create upload directory");
      }
    }
  }

  public function handleUpload($file)
  {
    try
    {
      if (!isset($file) || !isset($file['tmp_name']))
      {
        throw new Exception("No file uploaded");
      }

      // Validate file
      $this->validateFile($file);

      // Generate unique filename
      $filename = $this->generateUniqueFilename($file['name']);

      // Full path where file will be saved
      $targetPath = $this->uploadDir . $filename;

      // Move uploaded file
      if (move_uploaded_file($file['tmp_name'], $targetPath))
      {
        $relativePath = "files/temp/" . date('Y-') .
          $this->monthsInRomanian[(int)date('m')] .
          date('-d/') . $filename;

        return [
          'success' => true,
          'path' => $relativePath,
          'filename' => $filename
        ];
      }

      throw new Exception("Failed to move uploaded file.");

    } catch (Exception $e)
    {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

  private function validateFile($file)
  {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK)
    {
      throw new Exception("Upload failed with error code: " . $file['error']);
    }

    // Check file size (2MB limit)
    if ($file['size'] > 2 * 1024 * 1024)
    {
      throw new Exception("File size must be less than 2MB");
    }

    // Verify MIME type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes))
    {
      throw new Exception("Invalid file type. Only JPG, PNG, GIF and SVG are allowed.");
    }
  }

  private function generateUniqueFilename($originalName)
  {
    // Get file extension
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Generate unique name using timestamp and random string
    $uniqueName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

    return $uniqueName;
  }
}

try {
  // Ensure no output has been sent yet
  if (headers_sent($filename, $linenum)) {
    throw new Exception("Headers already sent in $filename on line $linenum");
  }

  // Check request method
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
  }

  // Check for file upload
  if (!isset($_FILES['image'])) {
    throw new Exception('No file uploaded');
  }

  $uploader = new ImageUploadHandler();
  $result = $uploader->handleUpload($_FILES['image']);

  // Clear any output buffered content
  while (ob_get_level()) {
    ob_end_clean();
  }

  // Send JSON response
  echo json_encode($result);
  exit;

} catch (Exception $e) {
  // Clear any output buffered content
  while (ob_get_level()) {
    ob_end_clean();
  }

  // Send error response
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'error' => $e->getMessage()
  ]);
  exit;
}