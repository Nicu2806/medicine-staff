<?php
function PrintGlobalMap()
{
  $db = new Database();
  $locations = $db->SelectRow('locations', '*');
  $userSharedLocations = $db->SelectRow('user_shared_experiences', '*');
  ?>
  <!-- External CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>

  <style>
      .map-container {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          width: 100%;
          height: 100%;
          box-sizing: border-box;
          margin-top: 74px;
      }

      #map {
          width: 100%;
          height: 100%;
      }

      .map-legend {
          position: fixed;
          top: 0;
          margin-top: 85px;
          right: 10px;
          z-index: 1000;
          background: white;
          padding: 10px;
          border-radius: 5px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }

      .legend-item {
          display: flex;
          align-items: center;
          margin: 5px 0;
      }

      .legend-color {
          width: 20px;
          height: 20px;
          margin-right: 8px;
          border-radius: 50%;
      }

      .official-location {
          background: #4a90e2;
      }

      .user-shared-location {
          background: var(--indigo);
      }
  </style>

  <div class="map-container">
    <div id="map"></div>
  </div>

  <div class="map-legend">
    <div class="legend-item">
      <div class="legend-color official-location"></div>
      <span>Official Locations</span>
    </div>
    <div class="legend-item">
      <div class="legend-color user-shared-location"></div>
      <span>Community Shared Locations</span>
    </div>
  </div>
  <script>
    // Convert PHP arrays to JavaScript
    const locations = <?php echo json_encode($locations); ?>;
    const userSharedLocations = <?php echo json_encode($userSharedLocations); ?>;

    document.addEventListener('DOMContentLoaded', function () {
      function loadScript(url) {
        return new Promise((resolve, reject) => {
          const script = document.createElement('script');
          script.src = url;
          script.onload = resolve;
          script.onerror = reject;
          document.head.appendChild(script);
        });
      }

      async function initialize() {
        try {
          await loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');
          await loadScript('https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js');

          // Set default center to Moldova
          let centerLat = 47.0105;
          let centerLon = 28.5574;

          const macarte = L.map('map').setView([centerLat, centerLon], 8);

          L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
            minZoom: 1,
            maxZoom: 20
          }).addTo(macarte);

          // Create separate cluster groups for official and user locations
          const officialMarkerClusters = L.markerClusterGroup({
            chunkedLoading: true,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: true,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 13,
            maxClusterRadius: 40,
            spiderfyDistanceMultiplier: 2
          });

          const userMarkerClusters = L.markerClusterGroup({
            chunkedLoading: true,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: true,
            zoomToBoundsOnClick: true,
            disableClusteringAtZoom: 13,
            maxClusterRadius: 40,
            spiderfyDistanceMultiplier: 2
          });

          const allMarkers = [];

          // Custom icons
          const officialIcon = L.divIcon({
            html: `<div style="background-color: #4a90e2; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white;"></div>`,
            className: 'custom-div-icon',
            iconSize: [24, 24]
          });

          const userIcon = L.divIcon({
            html: `<div style="background-color: var(--indigo); width: 24px; height: 24px; border-radius: 50%; border: 2px solid white;"></div>`,
            className: 'custom-div-icon',
            iconSize: [24, 24]
          });

          // Add official locations
          if (locations && locations.length > 0) {
            locations.forEach(location => {
              if (location.coordinates) {
                const [lat, lon] = location.coordinates.split(',').map(coord => parseFloat(coord.trim()));
                if (!isNaN(lat) && !isNaN(lon)) {
                  const marker = L.marker([lat, lon], {icon: officialIcon});

                  let popupContent = `
                    <div style="min-width: 200px;">
                      <h3 style="margin: 0 0 8px 0;">${location.title}</h3>
                      ${location.city ? `<p style="margin: 4px 0;"><strong>City:</strong> ${location.city}</p>` : ''}
                      ${location.country ? `<p style="margin: 4px 0;"><strong>Country:</strong> ${location.country}</p>` : ''}
                      <p style="margin: 4px 0;"><em>Official Location</em></p>
                    </div>
                  `;

                  marker.bindPopup(popupContent);
                  officialMarkerClusters.addLayer(marker);
                  allMarkers.push(marker);
                }
              }
            });
          }

          // Add user shared locations
          if (userSharedLocations && userSharedLocations.length > 0) {
            userSharedLocations.forEach(location => {
              if (location.geo_location && location.geo_location.trim() !== '') {
                const [lat, lon] = location.geo_location.split(',').map(coord => parseFloat(coord.trim()));
                if (!isNaN(lat) && !isNaN(lon)) {
                  const marker = L.marker([lat, lon], {icon: userIcon});

                  let imagesParsed;
                  try {
                    imagesParsed = JSON.parse(location.images_path);
                  } catch (e) {
                    imagesParsed = [];
                  }

                  let popupContent = `
                    <div style="min-width: 200px;">
                      <h3 style="margin: 0 0 8px 0;">${location.location_name}</h3>
                      <p style="margin: 4px 0;"><strong>Description:</strong> ${location.location_description}</p>
                      <p style="margin: 4px 0;"><strong>Shared by:</strong> ${location.user_name}</p>
                      <p style="margin: 4px 0;"><em>Community Shared Location</em></p>
                      ${imagesParsed.length > 0 ? `<p style="margin: 4px 0;"><strong>Images:</strong> ${imagesParsed.length} attached</p>` : ''}
                    </div>
                  `;

                  marker.bindPopup(popupContent);
                  userMarkerClusters.addLayer(marker);
                  allMarkers.push(marker);
                }
              }
            });
          }

          // Add both cluster groups to map
          macarte.addLayer(officialMarkerClusters);
          macarte.addLayer(userMarkerClusters);

          // Fit bounds with padding if we have markers
          if (allMarkers.length > 0) {
            const group = L.featureGroup(allMarkers);
            macarte.fitBounds(group.getBounds().pad(0.5));
          }

        } catch (error) {
          console.error('Error loading map:', error);
        }
      }

      initialize();
    });
  </script>
  <?php
}

?>