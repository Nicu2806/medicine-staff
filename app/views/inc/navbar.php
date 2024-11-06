<?php
function PrintNavBar()
{
  // Get current URL path
  $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $current_path = trim($current_path, '/');

  // Default active state for home
  $isHome = ($current_path === '');
  ?>
  <nav class="nav">
    <div class="logo">
      <img src="../../../images/logo.png" style="width: 150px">
    </div>
    <div class="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="nav-links">
      <a href="/" class="<?php echo $isHome ? 'active' : ''; ?>">Live Monitor</a>
      <a href="/staff-track" class="<?php echo $current_path === 'staff-track' ? 'active' : ''; ?>">Staff Track</a>
      <a href="/patient-watch" class="<?php echo $current_path === 'patient-watch' ? 'active' : ''; ?>">Patient Watch</a>
      <a href="/data-reports" class="<?php echo $current_path === 'data-reports' ? 'active' : ''; ?>">Data Reports</a>
    </div>
    <div class="overlay"></div>
  </nav>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const hamburger = document.querySelector('.hamburger');
      const navLinks = document.querySelector('.nav-links');
      const overlay = document.querySelector('.overlay');

      function toggleMenu() {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
      }

      hamburger.addEventListener('click', toggleMenu);
      overlay.addEventListener('click', toggleMenu);

      // Close menu when clicking links on mobile
      const links = document.querySelectorAll('.nav-links a');
      links.forEach(link => {
        link.addEventListener('click', () => {
          if (window.innerWidth <= 768) {
            toggleMenu();
          }
        });
      });
    });
  </script>
  <?php
}

?>