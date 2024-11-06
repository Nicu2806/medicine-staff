<?php

require_once(SECTIONS_PATH . 'index.php');
require APP_ROOT . '/views/inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
  AssetHelper::css('app/public/css/bootstrap.min.css');
  AssetHelper::renderCss();
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical Staff Monitor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      :root {
          --primary-color: #2563eb;
          --success-color: #059669;
          --warning-color: #d97706;
          --danger-color: #dc2626;
          --background-color: #f1f5f9;
          --card-background: #ffffff;
          --text-primary: #1e293b;
          --text-secondary: #64748b;
      }

      * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Inter', -apple-system, sans-serif;
      }

      body {
          background-color: var(--background-color);
          color: var(--text-primary);
          line-height: 1.5;
      }

      .dashboard-container {
          max-width: 1400px;
          margin-top: 100px;
          margin-right: auto;
          margin-bottom: 0;
          margin-left: auto;
          padding: 0 1rem;
      }

      /* Quick Actions Styling */
      .quick-actions-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
      }

      .action-card {
          background: var(--card-background);
          border-radius: 1rem;
          transition: transform 0.2s, box-shadow 0.2s;
      }

      .action-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }

      .action-btn {
          width: 100%;
          padding: 1.5rem;
          border: none;
          background: none;
          cursor: pointer;
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 0.75rem;
      }

      .action-btn i {
          font-size: 1.5rem;
          color: var(--primary-color);
      }

      .status-badge {
          padding: 0.25rem 0.75rem;
          border-radius: 1rem;
          font-size: 0.875rem;
          font-weight: 500;
      }

      .status-badge.active { background: #e0f2fe; color: #0369a1; }
      .status-badge.warning { background: #fef3c7; color: #92400e; }
      .status-badge.info { background: #e0e7ff; color: #3730a3; }
      .status-badge.success { background: #dcfce7; color: #166534; }

      /* Metrics Dashboard Styling */
      .metrics-dashboard {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
      }

      .metric-card {
          background: var(--card-background);
          border-radius: 1rem;
          padding: 1.5rem;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }

      .metric-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1.5rem;
      }

      .metric-value {
          font-size: 2rem;
          font-weight: 700;
          color: var(--primary-color);
      }

      .metric-trend {
          padding: 0.25rem 0.75rem;
          border-radius: 0.5rem;
          font-size: 0.875rem;
      }

      .metric-trend.positive {
          background: #dcfce7;
          color: #166534;
      }

      .metric-trend.warning {
          background: #fef3c7;
          color: #92400e;
      }

      /* Alerts Feed Styling */
      .alerts-feed {
          background: var(--card-background);
          border-radius: 1rem;
          padding: 1.5rem;
          margin-bottom: 2rem;
      }

      .alerts-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1.5rem;
      }

      .alerts-controls {
          display: flex;
          gap: 0.5rem;
      }

      .filter-btn {
          padding: 0.5rem 1rem;
          border: 1px solid #e2e8f0;
          border-radius: 0.5rem;
          background: none;
          cursor: pointer;
          transition: all 0.2s;
      }

      .filter-btn.active {
          background: var(--primary-color);
          color: white;
          border-color: var(--primary-color);
      }

      .alert-item {
          display: flex;
          gap: 1rem;
          padding: 1rem;
          border-radius: 0.5rem;
          margin-bottom: 1rem;
      }

      .alert-item.critical { background: #fee2e2; }
      .alert-item.warning { background: #fef3c7; }
      .alert-item.info { background: #e0f2fe; }

      .alert-content {
          flex: 1;
      }

      .alert-header {
          display: flex;
          justify-content: space-between;
          margin-bottom: 0.5rem;
      }

      .alert-title {
          font-weight: 600;
      }

      .alert-time {
          color: var(--text-secondary);
          font-size: 0.875rem;
      }

      .alert-actions {
          display: flex;
          gap: 0.5rem;
          margin-top: 0.75rem;
      }

      .alert-actions button {
          padding: 0.375rem 0.75rem;
          border-radius: 0.375rem;
          border: none;
          cursor: pointer;
          font-size: 0.875rem;
      }

      .btn-acknowledge {
          background: white;
          color: var(--text-primary);
      }

      .btn-details {
          background: var(--primary-color);
          color: white;
      }

      @media (max-width: 768px) {
          .quick-actions-grid {
              grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          }

          .metrics-dashboard {
              grid-template-columns: 1fr;
          }

          .alerts-header {
              flex-direction: column;
              gap: 1rem;
          }

          .alerts-controls {
              width: 100%;
              overflow-x: auto;
          }
      }
  </style>
</head>
<body>
<div class="dashboard-container">
  <section class="quick-actions" aria-label="Quick Actions">
    <div class="quick-actions-grid">
      <div class="action-card">
        <button class="action-btn" onclick="showStaffLocations()">
          <i class="fas fa-user-md"></i>
          <span>Staff Locations</span>
          <div class="status-badge active">45 Active</div>
        </button>
      </div>
      <div class="action-card">
        <button class="action-btn" onclick="showSafetyAlerts()">
          <i class="fas fa-exclamation-triangle"></i>
          <span>Safety Alerts</span>
          <div class="status-badge warning">3 New</div>
        </button>
      </div>
      <div class="action-card">
        <button class="action-btn" onclick="showCapacityStatus()">
          <i class="fas fa-hospital"></i>
          <span>Zone Capacity</span>
          <div class="status-badge info">85% Full</div>
        </button>
      </div>
      <div class="action-card">
        <button class="action-btn" onclick="showProtocolStatus()">
          <i class="fas fa-clipboard-check"></i>
          <span>Protocol Status</span>
          <div class="status-badge success">94% Compliant</div>
        </button>
      </div>
    </div>
  </section>

  <section class="metrics-section">
    <div class="metrics-dashboard">
      <!-- Staff Metrics Card -->
      <div class="metric-card">
        <h3><i class="fas fa-user-md"></i> Active Staff</h3>
        <div class="metric-header">
          <div class="metric-value">45/50</div>
          <div class="metric-trend positive">↑ 2 from yesterday</div>
        </div>
        <div class="metric-details">
          <canvas id="staffChart" height="200"></canvas>
        </div>
      </div>

      <!-- Safety Score Card -->
      <div class="metric-card">
        <h3><i class="fas fa-shield-alt"></i> Safety Score</h3>
        <div class="metric-header">
          <div class="metric-value">94%</div>
          <div class="metric-trend positive">↑ 2% this week</div>
        </div>
        <div class="metric-details">
          <canvas id="safetyChart" height="200"></canvas>
        </div>
      </div>

      <!-- Zone Status Card -->
      <div class="metric-card">
        <h3><i class="fas fa-hospital"></i> Zone Status</h3>
        <div class="metric-header">
          <div class="metric-value">75%</div>
          <div class="metric-trend warning">Critical in ICU</div>
        </div>
        <div class="metric-details">
          <canvas id="zoneChart" height="200"></canvas>
        </div>
      </div>
    </div>
  </section>

  <section class="alerts-section">
    <div class="alerts-feed">
      <div class="alerts-header">
        <h2><i class="fas fa-bell"></i> Recent Alerts</h2>
        <div class="alerts-controls">
          <button class="filter-btn active">All</button>
          <button class="filter-btn">Critical</button>
          <button class="filter-btn">Warnings</button>
          <button class="filter-btn">Info</button>
        </div>
      </div>
      <div class="alerts-list">
        <!-- Critical Alert -->
        <div class="alert-item critical">
          <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="alert-content">
            <div class="alert-header">
              <span class="alert-title">ICU Capacity Critical</span>
              <span class="alert-time">2 min ago</span>
            </div>
            <p class="alert-message">ICU Zone reaching maximum capacity (85%). Consider patient redistribution.</p>
            <div class="alert-actions">
              <button class="btn-acknowledge">Acknowledge</button>
              <button class="btn-details">View Details</button>
            </div>
          </div>
        </div>

        <!-- Warning Alert -->
        <div class="alert-item warning">
          <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="alert-content">
            <div class="alert-header">
              <span class="alert-title">Hand Hygiene Compliance</span>
              <span class="alert-time">15 min ago</span>
            </div>
            <p class="alert-message">Hand hygiene compliance in Emergency department below threshold (92%).</p>
            <div class="alert-actions">
              <button class="btn-acknowledge">Acknowledge</button>
              <button class="btn-details">View Details</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
  // Wait for DOM to be fully loaded
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initializeCharts();

    // Initialize animations
    initAnimations();

    // Add click handlers for filter buttons
    document.querySelectorAll('.filter-btn').forEach(button => {
      button.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        filterAlerts(this.textContent.toLowerCase());
      });
    });

    // Start real-time updates
    startRealTimeUpdates();
  });

  function initializeCharts() {
    // Staff Chart
    const staffCtx = document.getElementById('staffChart').getContext('2d');
    new Chart(staffCtx, {
      type: 'line',
      data: {
        labels: ['6AM', '9AM', '12PM', '3PM', '6PM', '9PM'],
        datasets: [{
          label: 'Active Staff',
          data: [30, 45, 40, 50, 45, 35],
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37, 99, 235, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            mode: 'index',
            intersect: false,
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 60,
            ticks: {
              stepSize: 10
            }
          }
        }
      }
    });

    // Safety Chart
    const safetyCtx = document.getElementById('safetyChart').getContext('2d');
    new Chart(safetyCtx, {
      type: 'doughnut',
      data: {
        labels: ['Compliant', 'Non-Compliant'],
        datasets: [{
          data: [94, 6],
          backgroundColor: ['#059669', '#e11d48'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20
            }
          }
        },
        cutout: '70%'
      }
    });

    // Zone Chart
    const zoneCtx = document.getElementById('zoneChart').getContext('2d');
    new Chart(zoneCtx, {
      type: 'bar',
      data: {
        labels: ['ICU', 'Emergency', 'Surgery', 'General'],
        datasets: [{
          label: 'Occupancy',
          data: [85, 60, 45, 75],
          backgroundColor: [
            'rgba(220, 38, 38, 0.8)',  // Red for ICU
            'rgba(234, 179, 8, 0.8)',   // Yellow for Emergency
            'rgba(37, 99, 235, 0.8)',   // Blue for Surgery
            'rgba(5, 150, 105, 0.8)'    // Green for General
          ],
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              callback: function(value) {
                return value + '%';
              }
            }
          }
        }
      }
    });
  }

  // Quick Actions Functions
  function showStaffLocations() {
    updateMetricCard('staff');
    showNotification('Updating staff locations...', 'info');
  }

  function showSafetyAlerts() {
    updateMetricCard('safety');
    showNotification('Loading safety alerts...', 'warning');
  }

  function showCapacityStatus() {
    updateMetricCard('capacity');
    showNotification('Checking capacity status...', 'info');
  }

  function showProtocolStatus() {
    updateMetricCard('protocol');
    showNotification('Loading protocol status...', 'info');
  }

  function updateMetricCard(type) {
    const cards = document.querySelectorAll('.metric-card');
    cards.forEach(card => {
      card.classList.remove('active');
      if (card.dataset.type === type) {
        card.classList.add('active');
      }
    });
  }

  // Notifications System
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'info' ? 'info-circle' : 'exclamation-triangle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => notification.classList.add('show'), 10);

    // Remove after 3 seconds
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  // Real-time updates
  function startRealTimeUpdates() {
    setInterval(() => {
      updateMetrics();
      checkAlerts();
    }, 30000); // Update every 30 seconds
  }

  function updateMetrics() {
    // Update staff count
    const staffCount = Math.floor(Math.random() * 10) + 40; // Random between 40-50
    updateStaffMetrics(staffCount);

    // Update safety score
    const safetyScore = Math.floor(Math.random() * 5) + 92; // Random between 92-97
    updateSafetyMetrics(safetyScore);

    // Update zone status
    updateZoneMetrics();

    // Update charts
    updateCharts();
  }

  function updateStaffMetrics(count) {
    const staffElement = document.querySelector('#activeStaffCount');
    if (staffElement) {
      staffElement.textContent = `${count}/50`;
      const trend = count > parseInt(staffElement.dataset.previous || '0') ? 'positive' : 'negative';
      staffElement.dataset.previous = count;
      updateTrendIndicator('staff', trend);
    }
  }

  function updateSafetyMetrics(score) {
    const safetyElement = document.querySelector('#safetyScore');
    if (safetyElement) {
      safetyElement.textContent = `${score}%`;
      const trend = score > parseInt(safetyElement.dataset.previous || '0') ? 'positive' : 'negative';
      safetyElement.dataset.previous = score;
      updateTrendIndicator('safety', trend);
    }
  }

  function updateZoneMetrics() {
    const zones = ['ICU', 'Emergency', 'Surgery', 'General'];
    zones.forEach(zone => {
      const occupancy = Math.floor(Math.random() * 30) + 60; // Random between 60-90
      const element = document.querySelector(`#${zone.toLowerCase()}Occupancy`);
      if (element) {
        element.style.width = `${occupancy}%`;
        element.textContent = `${occupancy}%`;
        updateZoneStatus(zone, occupancy);
      }
    });
  }

  function updateZoneStatus(zone, occupancy) {
    const statusElement = document.querySelector(`#${zone.toLowerCase()}Status`);
    if (statusElement) {
      let status = 'Normal';
      let color = 'green';

      if (occupancy > 85) {
        status = 'Critical';
        color = 'red';
      } else if (occupancy > 70) {
        status = 'Warning';
        color = 'orange';
      }

      statusElement.textContent = status;
      statusElement.className = `status-badge ${color}`;
    }
  }

  function updateTrendIndicator(metric, trend) {
    const element = document.querySelector(`#${metric}Trend`);
    if (element) {
      element.className = `metric-trend ${trend}`;
      element.textContent = trend === 'positive' ? '↑ Increasing' : '↓ Decreasing';
    }
  }

  function checkAlerts() {
    // Simulate random alerts
    if (Math.random() > 0.7) { // 30% chance of new alert
      const alertTypes = ['critical', 'warning', 'info'];
      const randomType = alertTypes[Math.floor(Math.random() * alertTypes.length)];
      const alertMessages = {
        critical: 'Critical capacity reached in ICU',
        warning: 'Staff shortage predicted in Emergency',
        info: 'Equipment maintenance scheduled'
      };

      addNewAlert({
        type: randomType,
        title: alertMessages[randomType],
        message: `Automated alert: ${alertMessages[randomType]}. Please check details.`,
        time: 'Just now'
      });
    }
  }

  function addNewAlert(alertData) {
    const alertsList = document.querySelector('.alerts-list');
    if (!alertsList) return;

    // Create new alert element
    const alertElement = document.createElement('div');
    alertElement.className = `alert-item ${alertData.type} fade-in`;
    alertElement.innerHTML = `
        <div class="alert-icon">
            <i class="fas fa-${getAlertIcon(alertData.type)}"></i>
        </div>
        <div class="alert-content">
            <div class="alert-header">
                <span class="alert-title">${alertData.title}</span>
                <span class="alert-time">${alertData.time}</span>
            </div>
            <p class="alert-message">${alertData.message}</p>
            <div class="alert-actions">
                <button class="btn-acknowledge">Acknowledge</button>
                <button class="btn-details">View Details</button>
            </div>
        </div>
    `;

    // Add new alert at the top
    alertsList.insertBefore(alertElement, alertsList.firstChild);

    // Remove oldest alert if more than 5
    const alerts = alertsList.querySelectorAll('.alert-item');
    if (alerts.length > 5) {
      alerts[alerts.length - 1].remove();
    }

    // Trigger animation
    setTimeout(() => alertElement.classList.add('active'), 10);
  }

  function getAlertIcon(type) {
    switch(type) {
      case 'critical':
        return 'exclamation-circle';
      case 'warning':
        return 'exclamation-triangle';
      case 'info':
      default:
        return 'info-circle';
    }
  }

  function filterAlerts(type) {
    const alerts = document.querySelectorAll('.alert-item');
    alerts.forEach(alert => {
      if (type === 'all' || alert.classList.contains(type)) {
        alert.style.display = 'flex';
      } else {
        alert.style.display = 'none';
      }
    });
  }

  // Animations
  function initAnimations() {
    // Add fade-in animation to metric cards
    const cards = document.querySelectorAll('.metric-card');
    cards.forEach((card, index) => {
      setTimeout(() => {
        card.classList.add('fade-in');
      }, index * 100);
    });

    // Add slide-in animation to alerts
    const alerts = document.querySelectorAll('.alert-item');
    alerts.forEach((alert, index) => {
      setTimeout(() => {
        alert.classList.add('slide-in');
      }, index * 100);
    });
  }

  // Handle window resize
  window.addEventListener('resize', function() {
    // Update all charts
    Chart.instances.forEach(chart => {
      chart.resize();
    });
  });

  function handleError(error, context) {
    console.error(`Error in ${context}:`, error);
    showNotification(`An error occurred in ${context}. Please refresh the page.`, 'error');
  }

  // Initialize everything when the page loads
  window.addEventListener('load', function() {
    try {
      initializeCharts();
      initAnimations();
      startRealTimeUpdates();
    } catch (error) {
      handleError(error, 'initialization');
    }
  });
</script>
</body>
</html>
