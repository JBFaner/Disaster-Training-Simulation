<?php
session_start();

if (!isset($_SESSION['participant_id'])) {
    header("Location: login.php");
    exit;
}

$participant_name = isset($_SESSION['participant_name']) ? htmlspecialchars($_SESSION['participant_name']) : 'Participant';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/part-styles.css">
    <link rel="stylesheet" href="css/part-events.css">
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
                    <li><a href="part-index.php" class="menu-item">Dashboard</a></li>
                    <li><a href="part-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="part-my-events.php" class="menu-item active">My Events</a></li>
                    <li><a href="part-scenarios.php" class="menu-item">Scenarios</a></li>
                    <li><a href="part-evaluation-results.php" class="menu-item">Evaluation Results</a></li>
                    <li><a href="part-certificate.php" class="menu-item">Certificates</a></li>
                    <li><a href="part-profile.php" class="menu-item">Profile & History</a></li>
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
                    <a href="part-index.php">Home</a>
                    <a href="part-training-modules.php">Training</a>
                    <a href="part-certificate.php">Certificates</a>
                    <a href="#contact">Contact</a>
                </nav>
                <div class="user-menu">
                    <button class="btn-profile" id="profileBtn">👤</button>
                    <div class="profile-dropdown" id="profileDropdown" style="display: none;">
                        <a href="part-profile.php">View Profile</a>
                        <a href="part-profile.php#qr-code">Show QR Code</a>
                        <hr>
                        <a href="#" onclick="confirmLogout(); return false;">Logout</a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <section class="content">
                <div class="page-header">
                    <h1>My Events</h1>
                    <p class="subtitle">Register and manage your simulation event attendance</p>
                </div>

                <!-- Tab Navigation -->
                <div class="event-tabs">
                    <button class="tab-btn active" onclick="switchTab('available')">Available Drills</button>
                    <button class="tab-btn" onclick="switchTab('registered')">My Registered Events</button>
                    <button class="tab-btn" onclick="switchTab('completed')">Completed Events</button>
                </div>

                <!-- Available Drills Tab -->
                <div id="available-tab" class="tab-content active">
                    <div class="events-grid">
                        <div class="event-card-large">
                            <div class="event-header">
                                <div class="event-date-badge">
                                    <span class="date-month">DEC</span>
                                    <span class="date-day">20</span>
                                </div>
                                <span class="event-badge badge-open">OPEN FOR REGISTRATION</span>
                            </div>
                            <div class="event-body">
                                <h3>Earthquake Preparedness Drill</h3>
                                <div class="event-details">
                                    <p>📍 <strong>Location:</strong> City Convention Center, Main Hall</p>
                                    <p>⏰ <strong>Time:</strong> 9:00 AM - 12:00 PM (3 hours)</p>
                                    <p>👥 <strong>Capacity:</strong> 45 / 50 participants</p>
                                    <p>📝 <strong>Description:</strong> Learn and practice earthquake response procedures including drop, cover, and hold techniques. This hands-on drill will test your knowledge in a controlled environment.</p>
                                </div>
                                <div class="event-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>Comfortable clothing and shoes</li>
                                        <li>Completion of Earthquake Skills module</li>
                                        <li>Valid ID</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="event-footer">
                                <button class="btn-register" onclick="registerEvent('Earthquake Preparedness Drill')">Register Now →</button>
                            </div>
                        </div>

                        <div class="event-card-large">
                            <div class="event-header">
                                <div class="event-date-badge">
                                    <span class="date-month">DEC</span>
                                    <span class="date-day">27</span>
                                </div>
                                <span class="event-badge badge-open">OPEN FOR REGISTRATION</span>
                            </div>
                            <div class="event-body">
                                <h3>Flood Response Training</h3>
                                <div class="event-details">
                                    <p>📍 <strong>Location:</strong> Training Center, Room A & B</p>
                                    <p>⏰ <strong>Time:</strong> 2:00 PM - 5:00 PM (3 hours)</p>
                                    <p>👥 <strong>Capacity:</strong> 30 / 40 participants</p>
                                    <p>📝 <strong>Description:</strong> Comprehensive flood response training covering evacuation procedures, water safety, and emergency communication in flood situations.</p>
                                </div>
                                <div class="event-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>Waterproof shoes and clothing</li>
                                        <li>Completion of Flood Response module</li>
                                        <li>Valid ID and emergency contact info</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="event-footer">
                                <button class="btn-register" onclick="registerEvent('Flood Response Training')">Register Now →</button>
                            </div>
                        </div>

                        <div class="event-card-large">
                            <div class="event-header">
                                <div class="event-date-badge">
                                    <span class="date-month">JAN</span>
                                    <span class="date-day">10</span>
                                </div>
                                <span class="event-badge badge-open">OPEN FOR REGISTRATION</span>
                            </div>
                            <div class="event-body">
                                <h3>Fire Evacuation Drill</h3>
                                <div class="event-details">
                                    <p>📍 <strong>Location:</strong> City Building, All Floors</p>
                                    <p>⏰ <strong>Time:</strong> 10:00 AM - 11:30 AM (1.5 hours)</p>
                                    <p>👥 <strong>Capacity:</strong> 60 / 80 participants</p>
                                    <p>📝 <strong>Description:</strong> Full-scale fire evacuation simulation. Practice proper evacuation procedures and assembly point procedures.</p>
                                </div>
                                <div class="event-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>Suitable shoes (no heels)</li>
                                        <li>Completion of Fire Skills module</li>
                                        <li>Valid ID</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="event-footer">
                                <button class="btn-register" onclick="registerEvent('Fire Evacuation Drill')">Register Now →</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registered Events Tab -->
                <div id="registered-tab" class="tab-content" style="display: none;">
                    <div class="events-grid">
                        <div class="event-card-status registered">
                            <div class="event-status-header">
                                <span class="status-badge registered">✓ REGISTERED</span>
                            </div>
                            <div class="event-status-body">
                                <h3>Earthquake Preparedness Drill</h3>
                                <p>📅 <strong>Schedule:</strong> December 20, 2024 • 9:00 AM</p>
                                <p>📍 <strong>Location:</strong> City Convention Center, Main Hall</p>
                                <p>✓ <strong>Status:</strong> You are confirmed to attend</p>
                                <div class="event-actions">
                                    <button class="btn-action-secondary" onclick="viewQRCode()">Show QR Code</button>
                                    <button class="btn-action-secondary" onclick="unregisterEvent('Earthquake Preparedness Drill')">Unregister</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="empty-state" style="display: none;">
                        <div class="empty-icon">📅</div>
                        <h3>No Registered Events</h3>
                        <p>Register for upcoming drills to see them here</p>
                        <button class="btn-primary" onclick="switchTab('available')">View Available Drills</button>
                    </div>
                </div>

                <!-- Completed Events Tab -->
                <div id="completed-tab" class="tab-content" style="display: none;">
                    <div class="events-grid">
                        <div class="event-card-status completed">
                            <div class="event-status-header">
                                <span class="status-badge completed">✓ COMPLETED</span>
                            </div>
                            <div class="event-status-body">
                                <h3>Fire Evacuation Training</h3>
                                <p>📅 <strong>Attended:</strong> November 15, 2024</p>
                                <p>📍 <strong>Location:</strong> Training Center, Room A</p>
                                <p>⭐ <strong>Evaluation Score:</strong> 92%</p>
                                <div class="event-actions">
                                    <button class="btn-action-secondary" onclick="viewResults('Fire Evacuation Training')">View Results</button>
                                    <button class="btn-action-secondary" onclick="viewCertificate('Fire Evacuation Training')">Download Certificate</button>
                                </div>
                            </div>
                        </div>

                        <div class="event-card-status completed">
                            <div class="event-status-header">
                                <span class="status-badge completed">✓ COMPLETED</span>
                            </div>
                            <div class="event-status-body">
                                <h3>Earthquake Drill - Phase 1</h3>
                                <p>📅 <strong>Attended:</strong> October 28, 2024</p>
                                <p>📍 <strong>Location:</strong> City Convention Center</p>
                                <p>⭐ <strong>Evaluation Score:</strong> 88%</p>
                                <div class="event-actions">
                                    <button class="btn-action-secondary" onclick="viewResults('Earthquake Drill - Phase 1')">View Results</button>
                                    <button class="btn-action-secondary" onclick="viewCertificate('Earthquake Drill - Phase 1')">Download Certificate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/part-main.js"></script>
    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').style.display = 'block';
            event.target.classList.add('active');
        }

        function registerEvent(eventName) {
            Swal.fire({
                title: 'Register for Event',
                html: `<p>Are you sure you want to register for <strong>${eventName}</strong>?</p><p style="font-size: 12px; color: #666; margin-top: 10px;">You will receive confirmation and reminders via email.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3a7675',
                cancelButtonColor: '#999',
                confirmButtonText: 'Register',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Registered Successfully!',
                        text: `You have been registered for ${eventName}. Check your email for details and reminders.`,
                        icon: 'success',
                        confirmButtonColor: '#3a7675',
                        timer: 3000
                    });
                    setTimeout(() => switchTab('registered'), 1500);
                }
            });
        }

        function unregisterEvent(eventName) {
            Swal.fire({
                title: 'Cancel Registration',
                text: `Are you sure you want to unregister from ${eventName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c33',
                cancelButtonColor: '#999',
                confirmButtonText: 'Yes, Unregister',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Unregistered',
                        text: 'You have been removed from this event.',
                        icon: 'info',
                        confirmButtonColor: '#3a7675'
                    });
                }
            });
        }

        function viewQRCode() {
            Swal.fire({
                title: 'Your QR Code',
                html: '<div style="background: white; padding: 20px; border-radius: 8px;"><div style="width: 200px; height: 200px; margin: 0 auto; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><span style="color: #999;">QR Code</span></div><p style="margin-top: 15px; font-size: 12px; color: #666;">Scan this QR code at the event to check in</p></div>',
                icon: 'info',
                confirmButtonColor: '#3a7675',
                confirmButtonText: 'Close'
            });
        }

        function viewResults(eventName) {
            window.location.href = 'part-evaluation-results.php';
        }

        function viewCertificate(eventName) {
            window.location.href = 'part-certificate.php';
        }

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
                    window.location.href = 'part-index.php?logout=1';
                }
            });
        }

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
