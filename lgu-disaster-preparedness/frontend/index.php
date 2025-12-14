<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy all session data
    session_destroy();
    // Clear all session variables
    $_SESSION = array();
    // Redirect to login
    header("Location: admin-login.php");
    exit;
}

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGU Disaster Preparedness Training & Simulation</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="images/logo.svg" alt="Logo" class="logo" style="width: 200px; height: 60px;">
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li><a href="index.php" class="menu-item active">Dashboard</a></li>
                    <li><a href="admin-training-modules.php" class="menu-item">Training Module</a></li>
                    <li><a href="admin-scenario-design.php" class="menu-item">Scenario Design</a></li>
                    <li><a href="admin-simulation-planning.php" class="menu-item">Simulation Planning</a></li>
                    <li><a href="admin-registration-attendance.php" class="menu-item">Registration & Attendance</a></li>
                    <li><a href="admin-evaluation-scoring.php" class="menu-item">Evaluation & Scoring</a></li>
                    <li><a href="admin-inventory.php" class="menu-item">Inventory</a></li>
                    <li><a href="admin-certificate-issuance.php" class="menu-item">Certificate Issuance</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <button onclick="confirmLogout()" class="btn-logout" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5M17 16l4-4m0 0l-4-4m4 4H9"></path>
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
            
            <script>
            function confirmLogout() {
                Swal.fire({
                    title: 'Confirm Logout',
                    text: 'Are you sure you want to logout? You will need to log in again to access the system.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c33',
                    cancelButtonColor: '#999',
                    confirmButtonText: 'Yes, Logout',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php?logout=1';
                    }
                });
            }
            </script>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <nav class="top-menu">
                    <a href="#home">Home</a>
                    <a href="#about">About</a>
                    <a href="#certificates">Certificates</a>
                    <a href="#contact">Contact Us</a>
                </nav>
                <div class="user-icon">👤</div>
            </header>

            <!-- Page Content -->
            <section class="content">
                <div class="page-header">
                    <h1>Training Module Management</h1>
                </div>
                <div class="page-description">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
                
                <div class="cards-container">
                    <div class="card">
                        <h3>Earthquake Drills</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor.</p>
                        <button class="btn-read-more">Read more →</button>
                    </div>
                    <div class="card">
                        <h3>Fire Drills</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor.</p>
                        <button class="btn-read-more">Read more →</button>
                    </div>
                    <div class="card">
                        <h3>Typhoon</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor.</p>
                        <button class="btn-read-more">Read more →</button>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/main.js"></script>
    
    <?php
    // Display welcome notification if user just logged in
    if (isset($_SESSION['first_login']) && $_SESSION['first_login']) {
        $admin_name = isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin';
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Welcome!',
                    text: 'Hello <?php echo $admin_name; ?>! Your account setup is complete and you have successfully logged in.',
                    icon: 'success',
                    confirmButtonColor: '#3a7675',
                    timer: 4000,
                    timerProgressBar: true,
                    didClose: function() {
                        // Optional: You can perform additional actions after the notification closes
                    }
                });
            });
        </script>
        <?php
        // Clear the flag so notification doesn't show again on refresh
        unset($_SESSION['first_login']);
    }
    ?>
</html>
