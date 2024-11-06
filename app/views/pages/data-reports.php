<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detecții în timp real</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          margin: 20px;
          background-color: #f0f0f0;
      }

      .container {
          max-width: 1200px;
          margin: 0 auto;
          background-color: white;
          padding: 20px;
          border-radius: 10px;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
          display: grid;
          grid-template-columns: 2fr 1fr;
          gap: 20px;
      }

      .video-container {
          width: 100%;
          position: relative;
      }

      #videoFeed {
          width: 100%;
          height: auto;
          border-radius: 5px;
      }

      .detection-info {
          padding: 15px;
          background-color: #f8f9fa;
          border-radius: 5px;
      }

      .detection-item {
          margin: 10px 0;
          padding: 10px;
          background-color: #e9ecef;
          border-radius: 5px;
      }
  </style>
</head>
<body>
<div class="container">
  <div class="video-container">
    <img id="videoFeed" src="http://172.20.10.2:5000/video_feed" alt="Live video feed"/>
  </div>

  <div class="detection-info">
    <h2>Ultima detecție:</h2>
    <p id="timestamp">Timestamp: <span></span></p>
    <p id="totalDetections">Total detecții: <span></span></p>
    <div id="detections">
      <h3>Detecții:</h3>
    </div>
  </div>
</div>

<script>
  function updateDetectionData() {
    fetch('detections.json')
      .then(response => response.json())
      .then(data => {
        if (data.length > 0) {
          const lastDetection = data[data.length - 1];

          document.querySelector('#timestamp span').textContent = lastDetection.timestamp;
          document.querySelector('#totalDetections span').textContent = lastDetection.total_detections;

          const detectionsDiv = document.querySelector('#detections');
          detectionsDiv.innerHTML = '<h3>Detecții:</h3>';

          lastDetection.detections.forEach(detection => {
            const detectionElement = document.createElement('div');
            detectionElement.className = 'detection-item';
            detectionElement.innerHTML = `
                                <p>Tip: ${detection.type}</p>
                                <p>Încredere: ${(detection.confidence * 100).toFixed(2)}%</p>
                            `;
            detectionsDiv.appendChild(detectionElement);
          });
        }
      })
      .catch(error => console.error('Eroare la încărcarea datelor:', error));
  }

  setInterval(updateDetectionData, 1000);
  updateDetectionData();
</script>
</body>
</html>