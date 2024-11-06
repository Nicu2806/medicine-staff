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
  <title>Monitorizare Pacienți & Vizitatori</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
      :root {
          --primary: #3b82f6;
          --primary-light: #93c5fd;
          --success: #22c55e;
          --warning: #f59e0b;
          --danger: #ef4444;
          --info: #0ea5e9;
          --gray-50: #f9fafb;
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
          padding: 62px 1rem;
      }

      .overview-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
          gap: 1.5rem;
          margin-bottom: 2rem;
      }

      .stat-card {
          background: white;
          padding: 1.5rem;
          border-radius: 0.75rem;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }

      .stat-header {
          display: flex;
          align-items: center;
          gap: 1rem;
          margin-bottom: 0.5rem;
      }

      .stat-icon {
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 0.75rem;
          font-size: 1.25rem;
      }

      .stat-value {
          font-size: 2rem;
          font-weight: bold;
          margin: 0.5rem 0;
      }

      .stat-trend {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.875rem;
      }

      .trend-up {
          color: var(--success);
      }

      .trend-down {
          color: var(--danger);
      }

      .section {
          background: white;
          padding: 1.5rem;
          border-radius: 0.75rem;
          margin-bottom: 2rem;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }

      .section-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1.5rem;
          padding-bottom: 1rem;
          border-bottom: 1px solid var(--gray-200);
      }

      .rooms-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
          gap: 1rem;
      }

      .room-card {
          padding: 1rem;
          border-radius: 0.5rem;
          background: var(--gray-50);
          border: 1px solid var(--gray-200);
      }

      .room-status {
          display: inline-block;
          padding: 0.25rem 0.75rem;
          border-radius: 1rem;
          font-size: 0.875rem;
          margin-top: 0.5rem;
      }

      .status-occupied {
          background: #fee2e2;
          color: #dc2626;
      }

      .status-available {
          background: #dcfce7;
          color: #16a34a;
      }

      .status-cleaning {
          background: #fef3c7;
          color: #d97706;
      }

      .appointments-table {
          width: 100%;
          border-collapse: collapse;
      }

      .appointments-table th,
      .appointments-table td {
          padding: 1rem;
          text-align: left;
          border-bottom: 1px solid var(--gray-200);
      }

      .appointments-table th {
          background: var(--gray-50);
          font-weight: 500;
      }

      .appointment-status {
          display: inline-block;
          padding: 0.25rem 0.75rem;
          border-radius: 1rem;
          font-size: 0.875rem;
      }

      .icu-monitoring-grid {
          display: grid;
          grid-template-columns: 1fr 1fr 1fr;
          gap: 1.5rem;
          margin-top: 1rem;
      }

      @media (max-width: 1024px) {
          .icu-monitoring-grid {
              grid-template-columns: 1fr;
          }
      }

      .personnel-cards, .visitors-cards {
          display: grid;
          gap: 1rem;
      }

      .person-card {
          background: var(--gray-50);
          padding: 1rem;
          border-radius: 0.5rem;
          border: 1px solid var(--gray-200);
      }

      .person-header {
          display: flex;
          align-items: center;
          gap: 1rem;
          margin-bottom: 0.75rem;
      }

      .person-icon {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: var(--primary-light);
          display: flex;
          align-items: center;
          justify-content: center;
          color: var(--primary);
      }

      .person-details h4 {
          margin: 0;
          color: var(--gray-800);
      }

      .person-details p {
          margin: 0;
          font-size: 0.875rem;
          color: var(--gray-700);
      }

      .equipment-list {
          display: flex;
          flex-wrap: wrap;
          gap: 0.5rem;
          margin-top: 0.5rem;
          padding-top: 0.5rem;
          border-top: 1px solid var(--gray-200);
      }

      .equipment-tag {
          display: flex;
          align-items: center;
          gap: 0.25rem;
          padding: 0.25rem 0.5rem;
          border-radius: 1rem;
          font-size: 0.75rem;
          background: white;
          border: 1px solid var(--gray-200);
      }

      .equipment-tag i {
          font-size: 0.875rem;
      }

      .equipment-tag.verified {
          background: #dcfce7;
          color: #16a34a;
          border-color: #16a34a;
      }

      .equipment-tag.missing {
          background: #fee2e2;
          color: #dc2626;
          border-color: #dc2626;
      }

      .equipment-status-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
          gap: 1rem;
      }

      .equipment-card {
          background: var(--gray-50);
          padding: 1rem;
          border-radius: 0.5rem;
          border: 1px solid var(--gray-200);
          text-align: center;
      }

      .equipment-icon {
          font-size: 1.5rem;
          margin-bottom: 0.5rem;
      }

      .equipment-status {
          display: inline-block;
          padding: 0.25rem 0.75rem;
          border-radius: 1rem;
          font-size: 0.875rem;
          margin-top: 0.5rem;
      }

      .status-ok {
          background: #dcfce7;
          color: #16a34a;
      }

      .status-warning {
          background: #fef3c7;
          color: #d97706;
      }

      .status-critical {
          background: #fee2e2;
          color: #dc2626;
      }
  </style>
</head>
<body>
<div class="dashboard">
  <!-- Overview Stats -->
  <div class="overview-grid">
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon" style="background: #e0f2fe; color: #0369a1;">
          <i class="fas fa-procedures"></i>
        </div>
        <h3>Inpatients</h3>
      </div>
      <div class="stat-value" id="patientsCount">156</div>
      <div class="stat-trend trend-up">
        <i class="fas fa-arrow-up"></i>
        <span>12 new today</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon" style="background: #dcfce7; color: #059669;">
          <i class="fas fa-users"></i>
        </div>
        <h3>Active Visitors</h3>
      </div>
      <div class="stat-value" id="visitorsCount">45</div>
      <div class="stat-trend trend-up">
        <i class="fas fa-arrow-up"></i>
        <span>8 in the last hour</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
          <i class="fas fa-door-closed"></i>
        </div>
        <h3>Rooms Occupied</h3>
      </div>
      <div class="stat-value" id="occupiedRoomsCount">48/60</div>
      <div class="stat-trend">
        <span>80% occupancy</span>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <h2>Room Status</h2>
      <div class="filters">
        <select class="filter-select">
          <option>All Floors</option>
          <option>Floor 1</option>
          <option>Floor 2</option>
          <option>Floor 3</option>
        </select>
        <select class="filter-select">
          <option>All Departments</option>
          <option>Surgery</option>
          <option>Cardiology</option>
          <option>Pediatrics</option>
        </select>
      </div>
    </div>
    <div class="rooms-grid" id="roomsGrid">
      <!-- Room cards will be generated by JavaScript -->
    </div>
  </div>

  <!-- Access Zones -->
  <div class="section">
    <div class="section-header">
      <h2>ICU Monitoring</h2>
      <div class="filters">
        <select class="filter-select" id="icuFilterTime">
          <option>Real Time</option>
          <option>Last Hour</option>
          <option>Today</option>
        </select>
      </div>
    </div>
    <div class="icu-monitoring-grid">
      <div class="icu-personnel-list">
        <h3>Medical Staff Present</h3>
        <div id="icuPersonnel" class="personnel-cards"></div>
      </div>
      <div class="icu-visitors-list">
        <h3>Visitors</h3>
        <div id="icuVisitors" class="visitors-cards"></div>
      </div>
      <div class="icu-equipment-status">
        <h3>Protection Equipment Status</h3>
        <div id="icuEquipment" class="equipment-status-grid"></div>
      </div>
    </div>
  </div>
  <script>
    // Initialize main functionality when document is loaded
    document.addEventListener('DOMContentLoaded', function () {
      initializeAll();
      // Set up real-time updates
      setInterval(updateRealTimeData, 30000); // Update every 30 seconds
    });

    function initializeAll() {
      generateRooms();
      generateAppointments();
      initializeZonesMap();
      updateStatistics();
      setupEventListeners();
    }

    // Room Management
    function generateRooms() {
      const roomsGrid = document.getElementById('roomsGrid');
      roomsGrid.innerHTML = ''; // Clear existing rooms

      for (let i = 1; i <= 12; i++) {
        const status = getRandomStatus();
        const roomCard = document.createElement('div');
        roomCard.className = 'room-card';
        roomCard.innerHTML = `
            <h4>Room ${i}</h4>
            <p>Floor ${Math.ceil(i / 4)}</p>
            <span class="room-status status-${status.class}">${status.text}</span>
            <div class="room-details">
                <p>Beds: ${status.class === 'occupied' ? '2/2' : '0/2'}</p>
                ${status.class === 'occupied' ? '<p>Last check: 30 minutes ago</p>' : ''}
            </div>
        `;
        roomsGrid.appendChild(roomCard);
      }
      updateOccupancyStats();
    }

    function initializeICUMonitoring() {
      updateICUPersonnel();
      updateICUVisitors();
      updateICUEquipment();

      // Set up real-time updates
      setInterval(() => {
        updateICUPersonnel();
        updateICUVisitors();
        updateICUEquipment();
      }, 30000); // Update every 30 seconds
    }

    function updateICUPersonnel() {
      const personnelContainer = document.getElementById('icuPersonnel');
      const personnel = [
        {
          name: 'Dr. Maria Popescu',
          role: 'ICU Doctor',
          time: '2 hours',
          equipment: [
            {name: 'N95 Mask', status: true},
            {name: 'Sterile Gown', status: true},
            {name: 'Cap', status: true},
            {name: 'Sterile Gloves', status: true}
          ]
        },
        {
          name: 'Dr. Ioan Radu',
          role: 'ICU Doctor',
          time: '1 hour',
          equipment: [
            {name: 'N95 Mask', status: true},
            {name: 'Sterile Gown', status: true},
            {name: 'Cap', status: true},
            {name: 'Sterile Gloves', status: false}
          ]
        },
        {
          name: 'Nurse Elena Dimitriu',
          role: 'Nurse',
          time: '3 hours',
          equipment: [
            {name: 'N95 Mask', status: true},
            {name: 'Sterile Gown', status: true},
            {name: 'Cap', status: true},
            {name: 'Sterile Gloves', status: true}
          ]
        }
      ];

      personnelContainer.innerHTML = personnel.map(person => `
 <div class="person-card">
   <div class="person-header">
     <div class="person-icon">
       <i class="fas fa-user-md"></i>
     </div>
     <div class="person-details">
       <h4>${person.name}</h4>
       <p>${person.role} • Present for ${person.time}</p>
     </div>
   </div>
   <div class="equipment-list">
     ${person.equipment.map(item => `
       <span class="equipment-tag ${item.status ? 'verified' : 'missing'}">
         <i class="fas ${getEquipmentIcon(item.name)}"></i>
         ${item.name}
       </span>
     `).join('')}
   </div>
 </div>
`).join('');
    }

    function updateICUVisitors() {
      const visitorsContainer = document.getElementById('icuVisitors');
      const visitors = [
        {
          name: 'Andrei Popescu',
          relation: 'Family Member',
          time: '15 min',
          equipment: [
            {name: 'Mask', status: true},
            {name: 'Gown', status: true},
            {name: 'Cap', status: true},
            {name: 'Boot Covers', status: true}
          ]
        },
        {
          name: 'Maria Ionescu',
          relation: 'Family Member',
          time: '10 min',
          equipment: [
            {name: 'Mask', status: true},
            {name: 'Gown', status: true},
            {name: 'Cap', status: false},
            {name: 'Boot Covers', status: true}
          ]
        }
      ];

      visitorsContainer.innerHTML = visitors.map(visitor => `
  <div class="person-card">
    <div class="person-header">
      <div class="person-icon">
        <i class="fas fa-user"></i>
      </div>
      <div class="person-details">
        <h4>${visitor.name}</h4>
        <p>${visitor.relation} • Present for ${visitor.time}</p>
      </div>
    </div>
    <div class="equipment-list">
      ${visitor.equipment.map(item => `
        <span class="equipment-tag ${item.status ? 'verified' : 'missing'}">
          <i class="fas ${getEquipmentIcon(item.name)}"></i>
          ${item.name}
        </span>
      `).join('')}
    </div>
  </div>
`).join('');
    }

    function getRandomStatus() {
      const statuses = [
        {class: 'occupied', text: 'Occupied'},
        {class: 'available', text: 'Available'},
        {class: 'cleaning', text: 'Cleaning'}
      ];
      const weights = [0.7, 0.2, 0.1]; // 70% occupied, 20% available, 10% cleaning

      const random = Math.random();
      let sum = 0;
      for (let i = 0; i < weights.length; i++) {
        sum += weights[i];
        if (random < sum) return statuses[i];
      }
      return statuses[0];
    }

    function updateICUEquipment() {
      const equipmentContainer = document.getElementById('icuEquipment');
      const equipment = [
        {name: 'N95 Masks', count: 45, status: 'ok'},
        {name: 'Sterile Gowns', count: 28, status: 'warning'},
        {name: 'Caps', count: 150, status: 'ok'},
        {name: 'Sterile Gloves', count: 200, status: 'ok'},
        {name: 'Boot Covers', count: 80, status: 'warning'}
      ];

      equipmentContainer.innerHTML = equipment.map(item => `
    <div class="equipment-card">
      <div class="equipment-icon">
        <i class="fas ${getEquipmentIcon(item.name)}"></i>
      </div>
      <h4>${item.name}</h4>
      <p>Available: ${item.count}</p>
      <span class="equipment-status status-${item.status}">
        ${getStatusText(item.status)}
      </span>
    </div>
  `).join('');
    }

    function getEquipmentIcon(name) {
      const icons = {
        'Mască N95': 'fa-head-side-mask',
        'Mască': 'fa-head-side-mask',
        'Halat Steril': 'fa-user-shield',
        'Halat': 'fa-user-shield',
        'Bonetă': 'fa-hard-hat',
        'Mănuși Sterile': 'fa-hand-sparkles',
        'Vizieră': 'fa-shield-virus',
        'Botoșei': 'fa-socks'
      };
      return icons[name] || 'fa-box';
    }

    function getStatusText(status) {
      const texts = {
        'ok': 'Stoc Suficient',
        'warning': 'Stoc Limitat',
        'critical': 'Stoc Critic'
      };
      return texts[status] || status;
    }

    // Initialize ICU monitoring when document is loaded
    document.addEventListener('DOMContentLoaded', function () {
      initializeICUMonitoring();
    });

    // Appointments Management
    function generateAppointments() {
      const appointmentsTable = document.getElementById('appointmentsTable');
      appointmentsTable.innerHTML = '';

      const currentHour = new Date().getHours();
      let appointments = generateDayAppointments(currentHour);

      appointments.forEach(appointment => {
        const row = createAppointmentRow(appointment);
        appointmentsTable.appendChild(row);
      });

      updateAppointmentStats();
    }

    function generateDayAppointments(currentHour) {
      const appointments = [];
      const types = ['Consultație', 'Tratament', 'Analize'];
      const doctors = [
        'Dr. Popescu Maria', 'Dr. Ionescu Dan', 'Dr. Popa Elena',
        'Dr. Dumitrescu Ion', 'Dr. Radu Alexandra'
      ];

      for (let hour = 9; hour <= 17; hour++) {
        for (let minute of ['00', '30']) {
          if (hour < currentHour) continue;

          const time = `${hour}:${minute}`;
          const status = determineAppointmentStatus(hour, currentHour);

          appointments.push({
            time: time,
            patientName: `Pacient ${Math.floor(Math.random() * 100)}`,
            type: types[Math.floor(Math.random() * types.length)],
            doctor: doctors[Math.floor(Math.random() * doctors.length)],
            status: status
          });
        }
      }
      return appointments;
    }

    function determineAppointmentStatus(appointmentHour, currentHour) {
      if (appointmentHour < currentHour) {
        return {class: 'status-cleaning', text: 'Finalizat'};
      } else if (appointmentHour === currentHour) {
        return {class: 'status-occupied', text: 'În Desfășurare'};
      } else {
        return {class: 'status-available', text: 'În Așteptare'};
      }
    }

    function createAppointmentRow(appointment) {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${appointment.time}</td>
        <td>${appointment.patientName}</td>
        <td>${appointment.type}</td>
        <td>${appointment.doctor}</td>
        <td><span class="appointment-status ${appointment.status.class}">${appointment.status.text}</span></td>
        <td>
            <button onclick="showAppointmentDetails('${appointment.patientName}')"
                    style="padding: 0.25rem 0.5rem; background: var(--primary); color: white; border: none; border-radius: 0.25rem; cursor: pointer;">
                Detalii
            </button>
        </td>
    `;
      return row;
    }

    // Zones Map Management
    function initializeZonesMap() {
      const zonesMap = document.getElementById('zonesMap');
      zonesMap.innerHTML = `
        <div id="mapContainer" style="width: 100%; height: 100%; position: relative;">
            <!-- Simplified hospital layout -->
            <div id="zoneOverlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
        </div>
    `;
      updateZoneHeatmap();
    }

    function updateZoneHeatmap() {
      const overlay = document.getElementById('zoneOverlay');
      overlay.innerHTML = '';

      // Create a grid of zones
      for (let i = 0; i < 6; i++) {
        for (let j = 0; j < 6; j++) {
          const zone = document.createElement('div');
          zone.style.position = 'absolute';
          zone.style.left = `${j * 16.66}%`;
          zone.style.top = `${i * 16.66}%`;
          zone.style.width = '16.66%';
          zone.style.height = '16.66%';
          zone.style.backgroundColor = getRandomZoneColor();
          zone.style.opacity = '0.5';
          overlay.appendChild(zone);
        }
      }
    }

    function getRandomZoneColor() {
      const colors = [
        'rgba(59, 130, 246, 0.5)', // primary
        'rgba(34, 197, 94, 0.5)',  // success
        'rgba(245, 158, 11, 0.5)'  // warning
      ];
      const weights = [0.5, 0.3, 0.2];

      const random = Math.random();
      let sum = 0;
      for (let i = 0; i < weights.length; i++) {
        sum += weights[i];
        if (random < sum) return colors[i];
      }
      return colors[0];
    }

    // Statistics Management
    function updateStatistics() {
      // Update patient count
      const patientsCount = 150 + Math.floor(Math.random() * 20);
      document.getElementById('patientsCount').textContent = patientsCount;

      // Update visitors count
      const visitorsCount = 40 + Math.floor(Math.random() * 15);
      document.getElementById('visitorsCount').textContent = visitorsCount;

      updateAppointmentStats();
      updateOccupancyStats();
    }

    function updateAppointmentStats() {
      const totalAppointments = document.querySelectorAll('#appointmentsTable tr').length;
      const remainingAppointments = document.querySelectorAll('.status-available').length;
      document.getElementById('appointmentsCount').textContent = totalAppointments;
      document.querySelector('#appointmentsCount + .stat-trend span').textContent =
        `${remainingAppointments} rămase`;
    }

    function updateOccupancyStats() {
      const occupiedRooms = document.querySelectorAll('.status-occupied').length;
      const totalRooms = document.querySelectorAll('.room-card').length;
      document.getElementById('occupiedRoomsCount').textContent = `${occupiedRooms}/${totalRooms}`;
      const occupancyRate = Math.round((occupiedRooms / totalRooms) * 100);
      document.querySelector('#occupiedRoomsCount + .stat-trend span').textContent =
        `${occupancyRate}% ocupare`;
    }

    // Real-time Updates
    function updateRealTimeData() {
      updateStatistics();
      updateRandomRoom();
      updateRandomAppointment();
      updateZoneHeatmap();
    }

    function updateRandomRoom() {
      const rooms = document.querySelectorAll('.room-card');
      const randomRoom = rooms[Math.floor(Math.random() * rooms.length)];
      const status = getRandomStatus();
      randomRoom.querySelector('.room-status').className = `room-status status-${status.class}`;
      randomRoom.querySelector('.room-status').textContent = status.text;
    }

    function updateRandomAppointment() {
      const appointments = document.querySelectorAll('#appointmentsTable tr');
      if (appointments.length > 0) {
        const randomAppointment = appointments[Math.floor(Math.random() * appointments.length)];
        const status = determineAppointmentStatus(new Date().getHours(), new Date().getHours());
        const statusCell = randomAppointment.querySelector('.appointment-status');
        statusCell.className = `appointment-status ${status.class}`;
        statusCell.textContent = status.text;
      }
    }

    // Event Listeners
    function setupEventListeners() {
      // Floor filter
      document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', function (e) {
          console.log('Filter changed:', e.target.value);
          // Add your filter logic here
        });
      });

      // Room click events
      document.querySelectorAll('.room-card').forEach(room => {
        room.addEventListener('click', function () {
          showRoomDetails(this);
        });
      });
    }

    // UI Interaction Functions
    function showAppointmentDetails(patientName) {
      alert(`Detalii programare pentru ${patientName}`);
      // Implement your detailed view logic here
    }

    function showRoomDetails(roomElement) {
      const roomNumber = roomElement.querySelector('h4').textContent;
      alert(`Detalii pentru ${roomNumber}`);
      // Implement your room details view logic here
    }

    // Error Handling
    function handleError(error, context) {
      console.error(`Error in ${context}:`, error);
      // Implement your error handling logic here
    }

    // Initialize everything when the page loads
    window.addEventListener('load', function () {
      try {
        initializeAll();
      } catch (error) {
        handleError(error, 'initialization');
      }
    });
  </script>
</body>
</html>