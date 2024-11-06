
document.addEventListener('DOMContentLoaded', function () {
  // Elements
  const shareBtn = document.getElementById('shareBtn');
  const modal = document.getElementById('shareModal');
  const closeBtn = document.querySelector('.close');
  const form = document.getElementById('shareForm');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.getElementById('submitBtn');
  const steps = document.querySelectorAll('.step');
  const stepContents = document.querySelectorAll('.step-content');
  const getCurrentLocationBtn = document.getElementById('getCurrentLocation');

  // Upload Elements
  const uploadArea = document.querySelector('#uploadArea');
  const dropZoon = document.querySelector('#dropZoon');
  const loadingText = document.querySelector('#loadingText');
  const fileInput = document.querySelector('#fileInput');
  const previewImage = document.querySelector('#previewImage');
  const fileDetails = document.querySelector('#fileDetails');
  const uploadedFile = document.querySelector('#uploadedFile');
  const uploadedFileInfo = document.querySelector('#uploadedFileInfo');
  const uploadedFileName = document.querySelector('.uploaded-file__name');
  const uploadedFileIconText = document.querySelector('.uploaded-file__icon-text');
  const uploadedFileCounter = document.querySelector('.uploaded-file__counter');

  let currentStep = 1;
  const totalSteps = 3;

  // Accepted image types
  const imagesTypes = ["jpeg", "jpg", "png", "svg", "gif"];

  // Modal Control
  shareBtn.addEventListener('click', () => {
    modal.classList.add('show');
  });

  closeBtn.addEventListener('click', () => {
    modal.classList.remove('show');
    resetForm();
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.remove('show');
      resetForm();
    }
  });

  // Form Reset
  function resetForm() {
    form.reset();
    currentStep = 1;
    updateSteps();
    resetUploadArea();
  }

  function resetUploadArea() {
    dropZoon.classList.remove('drop-zoon--Uploaded');
    uploadArea.classList.remove('upload-area--open');
    fileDetails.classList.remove('file-details--open');
    uploadedFile.classList.remove('uploaded-file--open');
    uploadedFileInfo.classList.remove('uploaded-file__info--active');
    previewImage.setAttribute('src', '');
    uploadedFileName.innerHTML = '';
    uploadedFileCounter.innerHTML = '0%';
  }

  // Step Navigation
  function updateSteps() {
    // Update steps visualization
    steps.forEach(step => {
      const stepNum = parseInt(step.dataset.step);
      if (stepNum <= currentStep) {
        step.classList.add('active');
      } else {
        step.classList.remove('active');
      }
    });

    // Update step content visibility
    stepContents.forEach(content => {
      const contentStep = parseInt(content.dataset.step);
      if (contentStep === currentStep) {
        content.classList.add('active');
      } else {
        content.classList.remove('active');
      }
    });

    // Update navigation buttons
    prevBtn.disabled = currentStep === 1;
    if (currentStep === totalSteps) {
      nextBtn.style.display = 'none';
      submitBtn.style.display = 'block';
    } else {
      nextBtn.style.display = 'block';
      submitBtn.style.display = 'none';
    }
  }

  prevBtn.addEventListener('click', () => {
    if (currentStep > 1) {
      currentStep--;
      updateSteps();
    }
  });

  nextBtn.addEventListener('click', () => {
    if (validateCurrentStep()) {
      if (currentStep < totalSteps) {
        currentStep++;
        updateSteps();
      }
    }
  });

  // Form Validation
  function validateCurrentStep() {
    const currentStepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
    const inputs = currentStepContent.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('error');
      } else {
        input.classList.remove('error');
      }
    });

    return isValid;
  }

  // Geolocation
  getCurrentLocationBtn.addEventListener('click', () => {
    if (navigator.geolocation) {
      getCurrentLocationBtn.disabled = true;
      getCurrentLocationBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Getting location...';

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const coordinates = `${position.coords.latitude},${position.coords.longitude}`;
          document.getElementById('coordinates').value = coordinates;
          getCurrentLocationBtn.innerHTML = '<i class="bx bx-check"></i> Location Set';
          getCurrentLocationBtn.classList.add('success');
        },
        (error) => {
          getCurrentLocationBtn.innerHTML = '<i class="bx bx-x"></i> Location Failed';
          getCurrentLocationBtn.classList.add('error');
          console.error('Error getting location:', error);
        }
      );
    }
  });

  // File Upload Functionality
  dropZoon.addEventListener('dragover', (event) => {
    event.preventDefault();
    dropZoon.classList.add('drop-zoon--over');
  });

  dropZoon.addEventListener('dragleave', (event) => {
    dropZoon.classList.remove('drop-zoon--over');
  });

  dropZoon.addEventListener('drop', (event) => {
    event.preventDefault();
    dropZoon.classList.remove('drop-zoon--over');
    const file = event.dataTransfer.files[0];
    uploadFile(file);
  });

  dropZoon.addEventListener('click', () => {
    fileInput.click();
  });

  fileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    uploadFile(file);
  });

  async function uploadFile(file) {
    const fileReader = new FileReader();
    const fileType = file.type;
    const fileSize = file.size;

    if (!fileValidate(fileType, fileSize)) {
      return;
    }

    try {
      // Show loading state
      dropZoon.classList.add('drop-zoon--Uploaded');
      loadingText.style.display = "block";
      previewImage.style.display = 'none';

      // Reset previous upload state
      uploadedFile.classList.remove('uploaded-file--open');
      uploadedFileInfo.classList.remove('uploaded-file__info--active');

      // Create FormData and append file
      const formData = new FormData();
      formData.append('image', file);

      // Send file to server with better error handling
      const response = await fetch('/app/helpers/ImageUploadHandler.php', {
        method: 'POST',
        body: formData
      });

      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Server returned non-JSON response. Please check server configuration.');
      }

      // Parse JSON response
      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.error || `HTTP error! status: ${response.status}`);
      }

      if (result.success) {
        // Show preview
        fileReader.addEventListener('load', () => {
          setTimeout(() => {
            uploadArea.classList.add('upload-area--open');
            loadingText.style.display = "none";
            previewImage.style.display = 'block';

            fileDetails.classList.add('file-details--open');
            uploadedFile.classList.add('uploaded-file--open');
            uploadedFileInfo.classList.add('uploaded-file__info--active');
          }, 500);

          previewImage.setAttribute('src', fileReader.result);
          uploadedFileName.innerHTML = file.name;

          // Add the server path to the form data
          const existingPathInput = document.querySelector('input[name="imagePath"]');
          if (existingPathInput) {
            existingPathInput.value = result.path;
          } else {
            const pathInput = document.createElement('input');
            pathInput.type = 'hidden';
            pathInput.name = 'imagePath';
            pathInput.value = result.path;
            document.getElementById('shareForm').appendChild(pathInput);
          }

          progressMove();
        });

        fileReader.readAsDataURL(file);
      } else {
        throw new Error(result.error || 'Upload failed');
      }
    } catch (error) {
      console.error('Upload error:', error);

      // More user-friendly error message
      let errorMessage = 'Upload failed: ';
      if (error.message.includes('Server returned non-JSON response')) {
        errorMessage += 'Server configuration error. Please contact support.';
      } else if (error.message.includes('HTTP error')) {
        errorMessage += 'Connection problem. Please try again.';
      } else {
        errorMessage += error.message || 'Unknown error occurred';
      }

      alert(errorMessage);

      // Reset UI on error
      dropZoon.classList.remove('drop-zoon--Uploaded');
      loadingText.style.display = "none";
      previewImage.style.display = 'none';
      uploadedFile.classList.remove('uploaded-file--open');
      uploadedFileInfo.classList.remove('uploaded-file__info--active');
    }
  }

  function progressMove() {
    let counter = 0;

    setTimeout(() => {
      let counterIncrease = setInterval(() => {
        if (counter === 100) {
          clearInterval(counterIncrease);
        } else {
          counter += 10;
          uploadedFileCounter.innerHTML = `${counter}%`;
        }
      }, 100);
    }, 600);
  }

  function fileValidate(fileType, fileSize) {
    let isImage = imagesTypes.filter(type => fileType.indexOf(`image/${type}`) !== -1);

    if (isImage[0] === 'jpeg') {
      uploadedFileIconText.innerHTML = 'jpg';
    } else {
      uploadedFileIconText.innerHTML = isImage[0];
    }

    if (isImage.length !== 0) {
      if (fileSize <= 2000000) { // 2MB
        return true;
      } else {
        alert('Please Your File Should be 2 Megabytes or Less');
        return false;
      }
    } else {
      alert('Please make sure to upload An Image File Type');
      return false;
    }
  }

  // Form Submission
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!validateCurrentStep()) return;

    const formData = new FormData(form);
    const file = fileInput.files[0];
    if (file) {
      formData.append('image', file);
    }

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Sharing...';

      // Get current date for file storage
      const currentDate = new Date().toISOString().split('T')[0];
      formData.append('date', currentDate);

      // Example API endpoint - replace with your actual endpoint
      const response = await fetch('/api/share-experience', {
        method: 'POST',
        body: formData
      });

      if (response.ok) {
        modal.classList.remove('show');
        alert('Experience shared successfully!');
        resetForm();
      } else {
        throw new Error('Failed to share experience');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Failed to share experience. Please try again.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = 'Share Experience';
    }
  });

  // Initialize steps on load
  updateSteps();
});