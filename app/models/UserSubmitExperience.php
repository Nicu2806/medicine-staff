<?php

class UserSubmitExperience
{
  private $db;
  private $monthsInRomanian = [
    1 => 'Ianuarie',
    2 => 'Februarie',
    3 => 'Martie',
    4 => 'Aprilie',
    5 => 'Mai',
    6 => 'Iunie',
    7 => 'Iulie',
    8 => 'August',
    9 => 'Septembrie',
    10 => 'Octombrie',
    11 => 'Noiembrie',
    12 => 'Decembrie'
  ];
  private const BASE_UPLOAD_DIR = 'files/temp/';
  private const TABLE_NAME = 'user_shared_experiences';
  private const MAX_FILES = 5;
  private const MIN_FILES = 1;

  // Adaugă această metodă pentru a genera calea dinamică
  private function getUploadDir()
  {
    return self::BASE_UPLOAD_DIR .
      date('Y-') .
      $this->monthsInRomanian[(int)date('m')] .
      date('-d/');
  }
  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function printForm()
  {
    $message = '';
    if (isset($_POST['submit_experience']))
    {
      $message = $this->handleSubmission();
    }
    ?>
    <button id="shareBtn" class="floating-share-btn">
      <i class='bx bx-plus'></i>
    </button>

    <?php if ($message): ?>
    <div class="alert bg-transparent alert-<?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

    <div id="shareModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Share Your Experience</h2>
          <span class="close">&times;</span>
        </div>

        <div class="modal-body">
          <form id="shareExperienceForm" method="POST" enctype="multipart/form-data">
            <!-- Progress Steps -->
            <div class="steps">
              <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-title">Personal Info</div>
              </div>
              <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Location</div>
              </div>
              <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Upload</div>
              </div>
            </div>

            <!-- Step 1: Personal Info -->
            <div class="step-content active" data-step="1">
              <div class="form-group">
                <input type="text" name="firstName" placeholder="First Name" required>
              </div>
              <div class="form-group">
                <input type="text" name="lastName" placeholder="Last Name" required>
              </div>
              <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
              </div>
              <button type="button" class="next-btn">Next</button>
            </div>

            <!-- Step 2: Location -->
            <div class="step-content" data-step="2">
              <div class="form-group">
                <input type="text" name="locationName" placeholder="Location Name" required>
              </div>
              <div class="form-group">
                <textarea name="description" placeholder="Description" required></textarea>
              </div>
              <div class="form-group">
                <button type="button" class="location-btn" onclick="getLocation()">
                  Set Current Location
                </button>
                <input type="hidden" name="coordinates" id="coordinates">
              </div>
              <div class="form-navigation">
                <button type="button" class="prev-btn">Previous</button>
                <button type="button" class="next-btn">Next</button>
              </div>
            </div>

            <!-- Step 3: Upload -->
            <div class="step-content" data-step="3">
              <div class="form-group">
                <div class="upload-area">
                  <input type="file" name="images[]" accept="image/*" multiple id="imageInput">
                  <div class="upload-text">
                    <i class='bx bx-upload'></i>
                    <p>Drop images here or click to upload (0/5 images)</p>
                  </div>
                </div>
              </div>
              <div class="preview-container" id="previewContainer"></div>
              <div class="form-navigation">
                <button type="button" class="prev-btn">Previous</button>
                <button type="submit" class="submit-btn" name="submit_experience" disabled>Share Experience</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Constante
        const MAX_FILES = 5;
        const MIN_FILES = 1;

        // Elemente DOM
        const form = document.getElementById('shareExperienceForm');
        const modal = document.getElementById('shareModal');
        const shareBtn = document.getElementById('shareBtn');
        const closeBtn = document.querySelector('.close');
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const uploadArea = document.querySelector('.upload-area');
        const uploadText = document.querySelector('.upload-text p');
        const submitBtn = document.querySelector('.submit-btn');
        const steps = document.querySelectorAll('.step');
        const contents = document.querySelectorAll('.step-content');

        // Variabile de stare
        let currentStep = 1;
        let uploadedFiles = new Set();

        // Event Handlers pentru Modal
        shareBtn.onclick = () => modal.style.display = 'block';
        closeBtn.onclick = closeModal;
        window.onclick = (e) => {
          if (e.target === modal) closeModal();
        };

        function closeModal() {
          modal.style.display = 'none';
          resetForm();
        }

        function resetForm() {
          form.reset();
          uploadedFiles.clear();
          previewContainer.innerHTML = '';
          currentStep = 1;
          updateSteps();
          updateSubmitButtonState();
          updateUploadText();
        }

        // Event Handlers pentru Upload
        uploadArea.ondragover = (e) => {
          e.preventDefault();
          uploadArea.classList.add('dragover');
        };

        uploadArea.ondragleave = (e) => {
          e.preventDefault();
          uploadArea.classList.remove('dragover');
        };

        uploadArea.ondrop = (e) => {
          e.preventDefault();
          uploadArea.classList.remove('dragover');
          handleFiles(Array.from(e.dataTransfer.files));
        };

        uploadArea.onclick = () => imageInput.click();

        imageInput.onchange = (e) => handleFiles(Array.from(e.target.files));

        // Gestionare navigare form
        form.addEventListener('click', function(e) {
          if (e.target.matches('.next-btn')) {
            const stepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
            const requiredFields = stepContent.querySelectorAll('[required]');
            const isValid = Array.from(requiredFields).every(field => field.value.trim() !== '');

            if (isValid) {
              currentStep++;
              updateSteps();
            } else {
              alert('Please fill in all required fields');
            }
          } else if (e.target.matches('.prev-btn')) {
            currentStep--;
            updateSteps();
          }
        });

        // Validare form la submit
        form.onsubmit = function(e) {
          if (currentStep === 3 && uploadedFiles.size < MIN_FILES) {
            e.preventDefault();
            alert(`Please upload at least ${MIN_FILES} image`);
            return false;
          }
          return true;
        };

        // Funcții pentru gestionarea fișierelor
        function handleFiles(files) {
          const imageFiles = files.filter(file => file.type.startsWith('image/'));

          if (uploadedFiles.size + imageFiles.length > MAX_FILES) {
            alert(`You can only upload up to ${MAX_FILES} images`);
            return;
          }

          imageFiles.forEach(file => {
            if (uploadedFiles.size >= MAX_FILES) return;

            if (file.size > 5 * 1024 * 1024) {
              alert(`File ${file.name} is too large. Maximum size is 5MB`);
              return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
              if (uploadedFiles.size < MAX_FILES) {
                addPreview(e.target.result, file);
                uploadedFiles.add(file);
                updateFileInput();
                updateSubmitButtonState();
                updateUploadText();
              }
            };
            reader.readAsDataURL(file);
          });
        }

        function addPreview(src, file) {
          const div = document.createElement('div');
          div.className = 'image-preview';

          const img = document.createElement('img');
          img.src = src;

          const deleteBtn = document.createElement('button');
          deleteBtn.className = 'delete-btn';
          deleteBtn.innerHTML = '×';
          deleteBtn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            removePreview(div, file);
          };

          div.appendChild(img);
          div.appendChild(deleteBtn);

          // Animație
          div.style.opacity = '0';
          div.style.transform = 'scale(0.8)';
          previewContainer.appendChild(div);

          requestAnimationFrame(() => {
            div.style.transition = 'all 0.3s';
            div.style.opacity = '1';
            div.style.transform = 'scale(1)';
          });
        }

        function removePreview(div, file) {
          div.style.transition = 'all 0.3s';
          div.style.transform = 'scale(0)';
          div.style.opacity = '0';

          setTimeout(() => {
            div.remove();
            uploadedFiles.delete(file);
            updateFileInput();
            updateSubmitButtonState();
            updateUploadText();
          }, 300);
        }

        function updateFileInput() {
          try {
            const dt = new DataTransfer();
            uploadedFiles.forEach(file => dt.items.add(file));
            imageInput.files = dt.files;
          } catch (error) {
            console.error('Error updating file input:', error);
          }
        }

        function updateSubmitButtonState() {
          if (currentStep === 3) {
            submitBtn.disabled = uploadedFiles.size < MIN_FILES;
          }
        }

        function updateUploadText() {
          uploadText.textContent = uploadedFiles.size === 0
            ? `Drop images here or click to upload (0/${MAX_FILES} images)`
            : `${uploadedFiles.size}/${MAX_FILES} images selected`;
        }

        function updateSteps() {
          steps.forEach(step => {
            step.classList.toggle('active', parseInt(step.dataset.step) <= currentStep);
          });
          contents.forEach(content => {
            content.classList.toggle('active', parseInt(content.dataset.step) === currentStep);
          });
          updateSubmitButtonState();
        }

        // Start Location
        window.getLocation = function() {
          if (navigator.geolocation) {
            const btn = document.querySelector('.location-btn');
            btn.disabled = true;
            btn.textContent = 'Getting location...';

            navigator.geolocation.getCurrentPosition(
              position => {
                document.getElementById('coordinates').value =
                  `${position.coords.latitude},${position.coords.longitude}`;
                btn.textContent = 'Location Set';
                btn.classList.add('success');
                btn.disabled = false;
              },
              error => {
                btn.textContent = 'Location Failed';
                btn.classList.add('error');
                btn.disabled = false;
                console.error('Error getting location:', error);
              }
            );
          } else {
            alert('Geolocation is not supported by this browser');
          }
        };

        // Inițializare
        updateSubmitButtonState();
        updateUploadText();
      });
    </script>
    <?php
  }

  private function handleSubmission()
  {
    Logger::Info("Processing form submission");

    // Log POST data (excluding sensitive information)
    Logger::Debug("POST data received: " . json_encode([
        'locationName' => $_POST['locationName'] ?? 'not set',
        'description_length' => strlen($_POST['description'] ?? ''),
        'has_coordinates' => isset($_POST['coordinates']),
      ]));

    // Validare câmpuri
    $requiredFields = ['firstName', 'lastName', 'email', 'locationName', 'description'];
    foreach ($requiredFields as $field) {
      if (empty($_POST[$field])) {
        Logger::Warning("Required field missing: {$field}");
        return "All fields are required";
      }
    }

    // Debug pentru a vedea ce primim în $_FILES
    Logger::Debug("Raw FILES data: " . json_encode($_FILES));

    // Verificare imagini - validare mai detaliată
    if (!isset($_FILES['images'])) {
      Logger::Error("No 'images' key in FILES array");
      return "No images were uploaded";
    }

    if (!is_array($_FILES['images']['name'])) {
      Logger::Error("Images not received as array");
      return "Invalid image upload format";
    }

    // Filtrează array-ul pentru a elimina slot-urile goale
    $validFiles = array_filter($_FILES['images']['name'], function($value) {
      return !empty($value);
    });

    if (empty($validFiles)) {
      Logger::Error("No valid images found in upload");
      return "Please select at least one image";
    }

    // Log numărul de fișiere valide
    Logger::Debug("Valid files count: " . count($validFiles));

    // Verificare și procesare imagini
    $uploadedFiles = [];
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->getUploadDir();

    // Verificare/creare director upload
    if (!is_dir($uploadDir)) {
      Logger::Info("Creating upload directory: {$uploadDir}");
      if (!mkdir($uploadDir, 0755, true)) {
        Logger::Error("Failed to create upload directory");
        return "Failed to create upload directory";
      }
    }

    // Procesare fiecare imagine
    foreach ($_FILES['images']['name'] as $key => $name) {
      // Skip dacă nu există fișier
      if (empty($name)) {
        continue;
      }

      // Verificări pentru fiecare fișier
      if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
        Logger::Error("Upload error for file {$name}: " . $_FILES['images']['error'][$key]);
        continue;
      }

      // Verificare dimensiune (max 5MB)
      if ($_FILES['images']['size'][$key] > 5 * 1024 * 1024) {
        Logger::Error("File too large: {$name}");
        return "File {$name} is too large. Maximum size is 5MB";
      }

      // Verificare tip fișier
      $tmp_name = $_FILES['images']['tmp_name'][$key];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime_type = finfo_file($finfo, $tmp_name);
      finfo_close($finfo);

      if (!str_starts_with($mime_type, 'image/')) {
        Logger::Error("Invalid file type for {$name}: {$mime_type}");
        return "File {$name} is not a valid image";
      }

      // Generare nume unic pentru fișier
      $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $filename = uniqid('img_') . '.' . $extension;
      $destination = $uploadDir . $filename;

      // Log detalii procesare
      Logger::Debug("Processing image: " . json_encode([
          'original_name' => $name,
          'new_name' => $filename,
          'size' => $_FILES['images']['size'][$key],
          'type' => $mime_type
        ]));

      if (move_uploaded_file($tmp_name, $destination)) {
        Logger::Info("File uploaded successfully: {$filename}");
        $uploadedFiles[] = $this->getUploadDir() . $filename;
      } else {
        Logger::Error("Failed to move uploaded file: {$filename}");
        return "Failed to upload image: {$name}";
      }
    }

    if (empty($uploadedFiles)) {
      Logger::Error("No files were successfully uploaded");
      return "No images were uploaded successfully";
    }

    // Pregătire date pentru baza de date
    $data = [
      'location_name' => $_POST['locationName'],
      'location_description' => $_POST['description'],
      'geo_location' => $_POST['coordinates'] ?? '',
      'user_name' => trim($_POST['firstName'] . ' ' . $_POST['lastName']),
      'user_email' => $_POST['email'],
      'images_path' => json_encode($uploadedFiles),
      'created_at' => date('Y-m-d H:i:s'),
    ];

    try {
      if ($this->db->InsertRow(self::TABLE_NAME, $data)) {
        Logger::Info("Experience saved successfully with " . count($uploadedFiles) . " images");
        return "Experience shared successfully with " . count($uploadedFiles) . " images";
      } else {
        Logger::Error("Database insertion failed");
        // Cleanup uploaded files
        foreach ($uploadedFiles as $file) {
          $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
          if (file_exists($fullPath)) {
            unlink($fullPath);
          }
        }
        return "Failed to save experience";
      }
    } catch (Exception $e) {
      Logger::Error("Database error: " . $e->getMessage());
      // Cleanup uploaded files
      foreach ($uploadedFiles as $file) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
        if (file_exists($fullPath)) {
          unlink($fullPath);
        }
      }
      return "An error occurred while saving the experience";
    }
  }
}