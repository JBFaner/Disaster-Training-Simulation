<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    $_SESSION = array();
    header("Location: frontend/login.php");
    exit;
}

// Redirect if not logged in
if (!isset($_SESSION['participant_id'])) {
    header("Location: frontend/login.php");
    exit;
}

$participant_name = isset($_SESSION['participant_name']) ? htmlspecialchars($_SESSION['participant_name']) : 'Participant';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="frontend/images/favicon.ico">
    <link rel="stylesheet" href="frontend/css/part-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="frontend/images/logo.svg" alt="Logo" class="logo" style="width: 200px; height: 60px;">
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li><a href="index.php" class="menu-item active">Dashboard</a></li>
                    <li><a href="frontend/part-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="frontend/part-my-events.php" class="menu-item">My Events</a></li>
                    <li><a href="frontend/part-scenarios.php" class="menu-item">Scenarios</a></li>
                    <li><a href="frontend/part-evaluation-results.php" class="menu-item">Evaluation Results</a></li>
                    <li><a href="frontend/part-certificate.php" class="menu-item">Certificates</a></li>
                    <li><a href="frontend/part-profile.php" class="menu-item">Profile & History</a></li>
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
                    <a href="frontend/part-training-modules.php">Training</a>
                    <a href="frontend/part-certificate.php">Certificates</a>
                    <a href="#contact">Contact</a>
                </nav>
                <div class="user-menu">
                    <button class="btn-profile" id="profileBtn">👤</button>
                    <div class="profile-dropdown" id="profileDropdown" style="display: none;">
                        <a href="frontend/part-profile.php">View Profile</a>
                        <a href="frontend/part-profile.php#qr-code">Show QR Code</a>
                        <hr>
                        <a href="#" onclick="confirmLogout(); return false;">Logout</a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <section class="content">
                <div class="page-header">
                    <h1>Welcome back, <?php echo $participant_name; ?>! 👋</h1>
                    <p class="subtitle">Your disaster preparedness journey continues here</p>
                </div>

                <!-- Dashboard Stats -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">📚</div>
                        <div class="stat-content">
                            <h3>Trainings Completed</h3>
                            <p class="stat-number">3</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🎯</div>
                        <div class="stat-content">
                            <h3>Events Attended</h3>
                            <p class="stat-number">2</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📜</div>
                        <div class="stat-content">
                            <h3>Certificates Earned</h3>
                            <p class="stat-number">2</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⭐</div>
                        <div class="stat-content">
                            <h3>Average Score</h3>
                            <p class="stat-number">88%</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions-section">
                    <h2>Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="frontend/part-training-modules.php" class="quick-action-card">
                            <div class="action-icon">📖</div>
                            <h3>View Training Modules</h3>
                            <p>Learn disaster preparedness skills</p>
                            <span class="arrow">→</span>
                        </a>
                        <a href="frontend/part-my-events.php" class="quick-action-card">
                            <div class="action-icon">📅</div>
                            <h3>Register for Events</h3>
                            <p>Join upcoming simulation drills</p>
                            <span class="arrow">→</span>
                        </a>
                        <a href="frontend/part-evaluation-results.php" class="quick-action-card">
                            <div class="action-icon">📊</div>
                            <h3>View Results</h3>
                            <p>Check your evaluation scores</p>
                            <span class="arrow">→</span>
                        </a>
                        <a href="frontend/part-certificate.php" class="quick-action-card">
                            <div class="action-icon">🏆</div>
                            <h3>My Certificates</h3>
                            <p>Download earned certificates</p>
                            <span class="arrow">→</span>
                        </a>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="upcoming-section">
                    <h2>Upcoming Events</h2>
                    <div class="upcoming-events">
                        <div class="event-card">
                            <div class="event-date">
                                <span class="month">DEC</span>
                                <span class="day">20</span>
                            </div>
                            <div class="event-info">
                                <h3>Earthquake Preparedness Drill</h3>
                                <p class="event-location">📍 City Convention Center</p>
                                <p class="event-time">⏰ 9:00 AM - 12:00 PM</p>
                                <div class="event-badges">
                                    <span class="badge badge-open">OPEN</span>
                                    <span class="badge badge-spots">45/50 Spots</span>
                                </div>
                            </div>
                            <a href="frontend/part-my-events.php" class="btn-event-action">Register →</a>
                        </div>
                        <div class="event-card">
                            <div class="event-date">
                                <span class="month">DEC</span>
                                <span class="day">27</span>
                            </div>
                            <div class="event-info">
                                <h3>Flood Response Training</h3>
                                <p class="event-location">📍 Training Center Room A</p>
                                <p class="event-time">⏰ 2:00 PM - 5:00 PM</p>
                                <div class="event-badges">
                                    <span class="badge badge-open">OPEN</span>
                                    <span class="badge badge-spots">30/40 Spots</span>
                                </div>
                            </div>
                            <a href="frontend/part-my-events.php" class="btn-event-action">Register →</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="activity-section">
                    <h2>Recent Activity</h2>
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon">✓</div>
                            <div class="activity-content">
                                <h4>Completed Training: Fire Evacuation Procedures</h4>
                                <p class="activity-time">2 days ago</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">🎯</div>
                            <div class="activity-content">
                                <h4>Scored 92% on Earthquake Drill Evaluation</h4>
                                <p class="activity-time">5 days ago</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">🏆</div>
                            <div class="activity-content">
                                <h4>Earned Certificate: Fire Safety Expert</h4>
                                <p class="activity-time">1 week ago</p>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">📝</div>
                            <div class="activity-content">
                                <h4>Registered for Flood Response Training</h4>
                                <p class="activity-time">2 weeks ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="frontend/js/part-main.js"></script>
    
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Confirm Logout',
                text: 'Are you sure you want to logout?',
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

        // Profile dropdown toggle
        document.getElementById('profileBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', function() {
            document.getElementById('profileDropdown').style.display = 'none';
        });
    </script>
</body>
</html>
