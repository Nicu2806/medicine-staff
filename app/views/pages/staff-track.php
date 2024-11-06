
<?php

require APP_ROOT . '/views/inc/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  AssetHelper::css('app/public/css/bootstrap.min.css');
  AssetHelper::renderCss();
  ?>
  <meta
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Monitor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      :root {
          --primary: #3b82f6;
          --success: #22c55e;
          --warning: #f59e0b;
          --danger: #ef4444;
          --gray-100: #f3f4f6;
          --gray-200: #e5e7eb;
          --gray-700: #374151;
          --gray-800: #1f2937;
      }

      * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: -apple-system, system-ui, sans-serif;
      }

      body {
          background: var(--gray-100);
          color: var(--gray-800);
          line-height: 1.5;
      }

      .dashboard {
          max-width: 1400px;
          margin: 2rem auto;
          padding: 0 1rem;
          margin-top: 100px;
      }

      .stats-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 1rem;
          margin-bottom: 2rem;
      }

      .stat-card {
          background: white;
          padding: 1.5rem;
          border-radius: 0.5rem;
          box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      }

      .stat-header {
          display: flex;
          align-items: center;
          gap: 1rem;
          margin-bottom: 1rem;
      }

      .stat-icon {
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 0.5rem;
          font-size: 1.25rem;
      }

      .stat-value {
          font-size: 2rem;
          font-weight: bold;
          margin: 0.5rem 0;
      }

      .section {
          background: white;
          padding: 1.5rem;
          border-radius: 0.5rem;
          margin-bottom: 2rem;
          box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      }

      .section-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1.5rem;
          padding-bottom: 1rem;
          border-bottom: 1px solid var(--gray-200);
      }

      .equipment-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 1.5rem;
      }

      .equipment-card {
          background: white;
          padding: 1.5rem;
          border-radius: 0.5rem;
          box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      }

      .equipment-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin-bottom: 1rem;
      }

      .equipment-title {
          display: flex;
          align-items: center;
          gap: 0.75rem;
      }

      .equipment-icon {
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 0.5rem;
          font-size: 1.25rem;
      }

      .progress-container {
          margin-top: 1rem;
      }

      .progress-bar {
          width: 100%;
          height: 10px;
          background: var(--gray-200);
          border-radius: 5px;
          overflow: hidden;
          margin: 0.5rem 0;
      }

      .progress-fill {
          height: 100%;
          border-radius: 5px;
          transition: width 0.3s ease;
      }

      .staff-status {
          display: flex;
          justify-content: space-between;
          margin-top: 0.5rem;
          font-size: 0.875rem;
          color: var(--gray-700);
      }

      .alert-badge {
          background: var(--danger);
          color: white;
          padding: 0.25rem 0.75rem;
          border-radius: 1rem;
          font-size: 0.875rem;
      }

      .schedule-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
          gap: 1rem;
      }

      .schedule-card {
          padding: 1rem;
          border: 1px solid var(--gray-200);
          border-radius: 0.5rem;
      }

      @media (max-width: 768px) {
          .stats-grid, .equipment-grid {
              grid-template-columns: 1fr;
          }

          .section-header {
              flex-direction: column;
              gap: 1rem;
          }
      }
  </style>
</head>
<body>
<div class="dashboard">
  <!-- Stats Overview -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon" style="background: #e0f2fe; color: #0369a1;">
          <i class="fas fa-user-md"></i>
        </div>
        <h3>Active Staff</h3>
      </div>
      <div class="stat-value">24/30</div>
      <div class="stat-desc">Staff present on shift</div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon" style="background: #dcfce7; color: #059669;">
          <i class="fas fa-shield-alt"></i>
        </div>
        <h3>Total Compliance</h3>
      </div>
      <div class="stat-value">86%</div>
      <div class="stat-desc">Complete equipment</div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
          <i class="fas fa-clock"></i>
        </div>
        <h3>Schedule</h3>
      </div>
      <div class="stat-value">6/8</div>
      <div class="stat-desc">Hours remaining in shift</div>
    </div>
  </div>

  <!-- Equipment Status -->
  <div class="section">
    <div class="section-header">
      <h2>Personal Equipment Monitoring</h2>
      <select id="departmentFilter">
        <option value="all">All Departments</option>
        <option value="emergency">Emergency</option>
        <option value="icu">Intensive Care</option>
        <option value="surgery">Surgery</option>
      </select>
    </div>

    <div class="equipment-grid">
      <!-- Complete Equipment Card -->
      <div class="equipment-card">
        <div class="equipment-header">
          <div class="equipment-title">
            <div class="equipment-icon" style="background: #dcfce7; color: #059669;">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>Complete Equipment</h3>
          </div>
          <span class="staff-count">22/30</span>
        </div>
        <div class="progress-container">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 73%; background: var(--success);"></div>
          </div>
          <div class="staff-status">
            <span>73% of staff</span>
            <span class="alert-badge">7 need verification</span>
          </div>
        </div>
      </div>

      <!-- Masks Card -->
      <div class="equipment-card">
        <div class="equipment-header">
          <div class="equipment-title">
            <div class="equipment-icon" style="background: #e0f2fe; color: #0369a1;">
              <i class="fas fa-head-side-mask"></i>
            </div>
            <h3>Masks</h3>
          </div>
          <span class="staff-count">26/30</span>
        </div>
        <div class="progress-container">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 87%; background: var(--primary);"></div>
          </div>
          <div class="staff-status">
            <span>87% of staff</span>
            <span class="alert-badge">4 missing</span>
          </div>
        </div>
      </div>

      <!-- Protective Gear Card -->
      <div class="equipment-card">
        <div class="equipment-header">
          <div class="equipment-title">
            <div class="equipment-icon" style="background: #fef3c7; color: #d97706;">
              <i class="fas fa-tshirt"></i>
            </div>
            <h3>Protective Gowns</h3>
          </div>
          <span class="staff-count">28/30</span>
        </div>
        <div class="progress-container">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 93%; background: var(--warning);"></div>
          </div>
          <div class="staff-status">
            <span>93% of staff</span>
            <span class="alert-badge">2 missing</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Schedule Overview -->
  <div class="section">
    <div class="section-header">
      <h2>Current Schedule</h2>
      <div class="date">November 6, 2024</div>
    </div>
    <div class="schedule-grid">
      <div class="schedule-card">
        <h4>Morning Shift</h4>
        <p>07:00 - 15:00</p>
        <strong>10 employees</strong>
      </div>

      <div class="schedule-card">
        <h4>Afternoon Shift</h4>
        <p>15:00 - 23:00</p>
        <strong>8 employees</strong>
      </div>

      <div class="schedule-card">
        <h4>Night Shift</h4>
        <p>23:00 - 07:00</p>
        <strong>6 employees</strong>
      </div>
    </div>
  </div>
</div>

<script>
  function updateEquipmentStats() {
    // Simulate real-time updates
    function getRandomPercentage(min, max) {
      return Math.floor(Math.random() * (max - min + 1) + min);
    }

    // Update complete equipment stats
    const completeEquipment = getRandomPercentage(70, 95);
    const completeCount = Math.floor((30 * completeEquipment) / 100);
    document.querySelectorAll('.equipment-card')[0].querySelector('.progress-fill').style.width = `${completeEquipment}%`;
    document.querySelectorAll('.equipment-card')[0].querySelector('.staff-status span').textContent = `${completeEquipment}% from staff`;
    document.querySelectorAll('.equipment-card')[0].querySelector('.staff-count').textContent = `${completeCount}/30`;

    // Update masks stats
    const masksPercentage = getRandomPercentage(80, 98);
    const masksCount = Math.floor((30 * masksPercentage) / 100);
    document.querySelectorAll('.equipment-card')[1].querySelector('.progress-fill').style.width = `${masksPercentage}%`;
    document.querySelectorAll('.equipment-card')[1].querySelector('.staff-status span').textContent = `${masksPercentage}% from staff`;
    document.querySelectorAll('.equipment-card')[1].querySelector('.staff-count').textContent = `${masksCount}/30`;

    // Update protective gear stats
    const gearPercentage = getRandomPercentage(85, 100);
    const gearCount = Math.floor((30 * gearPercentage) / 100);
    document.querySelectorAll('.equipment-card')[2].querySelector('.progress-fill').style.width = `${gearPercentage}%`;
    document.querySelectorAll('.equipment-card')[2].querySelector('.staff-status span').textContent = `${gearPercentage}% from staff`;
    document.querySelectorAll('.equipment-card')[2].querySelector('.staff-count').textContent = `${gearCount}/30`;

    // Update overall compliance in stats overview
    const overallCompliance = Math.floor((completeEquipment + masksPercentage + gearPercentage) / 3);
    document.querySelectorAll('.stat-value')[1].textContent = `${overallCompliance}%`;
  }

  // Update stats every 30 seconds
  setInterval(updateEquipmentStats, 30000);

  // Initial update
  updateEquipmentStats();

  // Department filter functionality
  document.getElementById('departmentFilter').addEventListener('change', function(e) {
    console.log('Filtering by department:', e.target.value);
    // Add your department filtering logic here
  });
</script>
</body>
</html>