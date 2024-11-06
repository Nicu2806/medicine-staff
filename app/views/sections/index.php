<?php
function PrintHeader()
{
  ?>
  <div class="dashboard-header-slider" role="region" aria-label="Hospital Monitoring Highlights">
    <div class="header-controls" aria-label="Slider Controls">
      <button class="prev-btn" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
      <div class="dots-container" role="tablist"></div>
      <button class="next-btn" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>

  <script>
    const HeaderSlides = [
      {
        image: 'https://www.gehealthcare.com/-/jssmedia/images/corporate/about-us/innovation/novii-patient-monitoring.jpg',
        title: 'Advanced Patient Monitoring',
        description: 'Real-time vital signs monitoring with AI-powered anomaly detection',
        alt: 'Modern hospital monitoring system showing patient vitals'
      },
      {
        image: 'https://www.philips.com/c-dam/corporate/newscenter/global/standard/resources/healthcare/2020/covid-19-healthcare-india/philips-patient-monitoring-during-covid-19.jpg',
        title: 'ICU Supervision',
        description: 'Comprehensive ICU monitoring with instant staff alerts',
        alt: 'ICU monitoring station with multiple patient displays'
      },
      {
        image: 'https://www.gehealthcare.co.uk/-/jssmedia/images/products/patient-monitoring/carescape-b650-patient-monitor.jpg',
        title: 'Equipment Tracking',
        description: 'Advanced medical equipment tracking and maintenance monitoring',
        alt: 'Medical equipment tracking system interface'
      }
    ];
    // Slider code here
  </script>
  <?php
}

function PrintQuickActions()
{
  ?>
  <div class="container">
    <div class="quick-actions-grid" role="toolbar" aria-label="Quick Actions">
      <div class="action-card">
        <button class="action-btn" onclick="showStaffLocations()">
          <i class="fas fa-user-md"></i>
          <span>Staff Locations</span>
          <div class="status-badge active">45 Active</div>
        </button>
      </div>
      <div class="action-card">
        <button class="action-btn urgent" onclick="showSafetyAlerts()">
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
  </div>
  <?php
}

function PrintLiveMetrics()
{
  ?>
  <div class="container">
    <div class="metrics-dashboard">
      <div class="metric-card"
           style="
                  background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://img.freepik.com/vektoren-kostenlos/gesundheitsfachmann-illustriert_23-2148487848.jpg');
       background-size: cover;
       background-position: center;
       background-repeat: no-repeat;
       width: 100%;
       height: auto;
       position: relative;
       margin: 0;
       padding: 0;
       align-items: center;
       text-align: center;
       font-weight: 400;
       font-size: 1.4em;
                "

           role="region" aria-label="Staff Statistics">
        <h3><i class="fas fa-user-md"></i> Active Staff</h3>
        <div class="metric-header">
          <div class="metric-value" id="activeStaffCount">45/50</div>
          <div class="metric-trend positive">↑ 2 from yesterday</div>
        </div>
        <div class="metric-details">
          <div class="detail-item">
            <span>Emergency</span>
            <span class="value">12</span>
          </div>
          <div class="detail-item">
            <span>Surgery</span>
            <span class="value">8</span>
          </div>
          <div class="detail-item">
            <span>ICU</span>
            <span class="value">15</span>
          </div>
          <div class="detail-item">
            <span>General</span>
            <span class="value">10</span>
          </div>
        </div>
        <canvas id="staffChart"></canvas>
      </div>

      <div class="metric-card" role="region" aria-label="Safety Compliance">
        <h3><i class="fas fa-shield-alt"></i> Safety Score</h3>
        <div class="metric-header">
          <div class="metric-value" id="safetyScore">94%</div>
          <div class="metric-trend positive">↑ 2% this week</div>
        </div>
        <div class="compliance-grid">
          <div class="compliance-item high">
            <span>PPE Usage</span>
            <span class="value">96%</span>
          </div>
          <div class="compliance-item medium">
            <span>Hand Hygiene</span>
            <span class="value">92%</span>
          </div>
          <div class="compliance-item high">
            <span>Zone Access</span>
            <span class="value">95%</span>
          </div>
          <div class="compliance-item high">
            <span>Equipment</span>
            <span class="value">93%</span>
          </div>
        </div>
        <canvas id="safetyChart"></canvas>
      </div>

      <div class="metric-card" role="region" aria-label="Zone Occupancy">
        <h3><i class="fas fa-hospital"></i> Zone Status</h3>
        <div class="metric-header">
          <div class="metric-value" id="zoneStatus">75% Avg</div>
          <div class="metric-trend warning">Critical in ICU</div>
        </div>
        <div class="zone-status-grid">
          <div class="zone-item critical">
            <span>ICU</span>
            <div class="progress-bar" style="--progress: 85%">
              <span class="value">85%</span>
            </div>
          </div>
          <div class="zone-item warning">
            <span>Emergency</span>
            <div class="progress-bar" style="--progress: 60%">
              <span class="value">60%</span>
            </div>
          </div>
          <div class="zone-item normal">
            <span>Surgery</span>
            <div class="progress-bar" style="--progress: 45%">
              <span class="value">45%</span>
            </div>
          </div>
          <div class="zone-item warning">
            <span>General</span>
            <div class="progress-bar" style="--progress: 75%">
              <span class="value">75%</span>
            </div>
          </div>
        </div>
        <canvas id="zoneChart"></canvas>
      </div>
    </div>
  </div>
  <?php
}

function PrintAlertsFeed()
{
  ?>
  <div class="container">
    <div class="alerts-feed" role="log" aria-label="Recent Alerts">
      <div class="alerts-header">
        <h2><i class="fas fa-bell"></i> Recent Alerts</h2>
        <div class="alerts-controls">
          <button class="filter-btn active">All</button>
          <button class="filter-btn">Critical</button>
          <button class="filter-btn">Warnings</button>
          <button class="filter-btn">Info</button>
        </div>
      </div>

      <div class="alerts-list" id="alertsList">
        <div class="alert-item critical">
          <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
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

        <div class="alert-item warning">
          <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
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

        <div class="alert-item info">
          <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
          <div class="alert-content">
            <div class="alert-header">
              <span class="alert-title">Equipment Maintenance</span>
              <span class="alert-time">1 hour ago</span>
            </div>
            <p class="alert-message">Scheduled maintenance required for 3 ventilators in ICU.</p>
            <div class="alert-actions">
              <button class="btn-acknowledge">Acknowledge</button>
              <button class="btn-details">View Details</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
}

?>
