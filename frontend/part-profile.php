<?php
session_start();

if (!isset($_SESSION['participant_id'])) {
    header("Location: login.php");
    exit;
}

$participant_name = isset($_SESSION['participant_name']) ? htmlspecialchars($_SESSION['participant_name']) : 'Participant';
$participant_email = isset($_SESSION['participant_email']) ? htmlspecialchars($_SESSION['participant_email']) : 'participant@email.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & History - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/part-styles.css">
    <link rel="stylesheet" href="css/part-profile.css">
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
                    <li><a href="part-my-events.php" class="menu-item">My Events</a></li>
                    <li><a href="part-scenarios.php" class="menu-item">Scenarios</a></li>
                    <li><a href="part-evaluation-results.php" class="menu-item">Evaluation Results</a></li>
                    <li><a href="part-certificate.php" class="menu-item">Certificates</a></li>
                    <li><a href="part-profile.php" class="menu-item active">Profile & History</a></li>
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
                <!-- Profile Section -->
                <div class="profile-section">
                    <div class="profile-header">
                        <h1>Your Profile</h1>
                    </div>

                    <div class="profile-card">
                        <div class="profile-info">
                            <div class="profile-avatar">👤</div>
                            <div class="profile-details">
                                <h2><?php echo $participant_name; ?></h2>
                                <p class="profile-email">📧 <?php echo $participant_email; ?></p>
                                <p class="profile-joined">Member since: October 2024</p>
                            </div>
                        </div>

                        <div class="profile-stats">
                            <div class="profile-stat">
                                <span class="stat-icon">📚</span>
                                <div class="stat-content">
                                    <span class="stat-number">6</span>
                                    <span class="stat-name">Modules Assigned</span>
                                </div>
                            </div>
                            <div class="profile-stat">
                                <span class="stat-icon">✅</span>
                                <div class="stat-content">
                                    <span class="stat-number">3</span>
                                    <span class="stat-name">Completed</span>
                                </div>
                            </div>
                            <div class="profile-stat">
                                <span class="stat-icon">🎯</span>
                                <div class="stat-content">
                                    <span class="stat-number">2</span>
                                    <span class="stat-name">Events Attended</span>
                                </div>
                            </div>
                            <div class="profile-stat">
                                <span class="stat-icon">🏆</span>
                                <div class="stat-content">
                                    <span class="stat-number">2</span>
                                    <span class="stat-name">Certificates</span>
                                </div>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button class="btn-edit-profile" onclick="editProfile()">✏️ Edit Profile</button>
                            <button class="btn-change-password" onclick="changePassword()">🔐 Change Password</button>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div id="qr-code" class="qr-section">
                    <div class="qr-header">
                        <h2>Your Check-in QR Code</h2>
                        <p>Show this QR code when you arrive at events for quick check-in</p>
                    </div>

                    <div class="qr-card">
                        <div class="qr-code-display">
                            <div class="qr-placeholder">
                                <div style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 2px solid #ddd;">
                                    <span style="color: #999; font-size: 14px;">QR Code</span>
                                </div>
                            </div>
                            <p class="qr-id">ID: <?php echo substr($participant_email, 0, 3) . '****' . substr($participant_email, -4); ?></p>
                        </div>
                        <div class="qr-actions">
                            <button class="btn-qr-download" onclick="downloadQRCode()">📥 Download QR Code</button>
                            <button class="btn-qr-print" onclick="printQRCode()">🖨️ Print QR Code</button>
                        </div>
                    </div>
                </div>

                <!-- Training Progress Section -->
                <div class="progress-section">
                    <h2>Training Progress</h2>
                    <div class="progress-grid">
                        <div class="progress-card">
                            <h4>Earthquake Skills</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-label">100% - Completed</span>
                        </div>
                        <div class="progress-card">
                            <h4>Fire Skills</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-label">100% - Completed</span>
                        </div>
                        <div class="progress-card">
                            <h4>Typhoon</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-label">0% - Not Started</span>
                        </div>
                        <div class="progress-card">
                            <h4>Flood Response Training</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-label">100% - Completed</span>
                        </div>
                        <div class="progress-card">
                            <h4>First Aid Basics</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-label">0% - Not Started</span>
                        </div>
                        <div class="progress-card">
                            <h4>Emergency Communication</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 45%"></div>
                            </div>
                            <span class="progress-label">45% - In Progress</span>
                        </div>
                    </div>
                </div>

                <!-- Event History Section -->
                <div class="history-section">
                    <h2>Event Attendance History</h2>
                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-marker">✓</div>
                            <div class="timeline-content">
                                <h4>Fire Evacuation Training</h4>
                                <p>November 15, 2024 • 9:00 AM - 12:00 PM</p>
                                <p class="event-location">📍 Training Center, Room A</p>
                                <p class="event-result">✓ Attended • Score: 92%</p>
                                <p class="event-cert">🏆 Certificate Earned: Fire Safety Expert</p>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-marker">✓</div>
                            <div class="timeline-content">
                                <h4>Earthquake Drill - Phase 1</h4>
                                <p>October 28, 2024 • 2:00 PM - 5:00 PM</p>
                                <p class="event-location">📍 City Convention Center</p>
                                <p class="event-result">✓ Attended • Score: 88%</p>
                                <p class="event-cert">🏆 Certificate Earned: Earthquake Preparedness</p>
                            </div>
                        </div>

                        <div class="timeline-item completed">
                            <div class="timeline-marker">✓</div>
                            <div class="timeline-content">
                                <h4>First Aid Assessment</h4>
                                <p>September 20, 2024 • 10:00 AM - 1:00 PM</p>
                                <p class="event-location">📍 Medical Training Center</p>
                                <p class="event-result">✓ Attended • Score: 72% (Needs Improvement)</p>
                                <p class="event-action">⏳ Pending refresher course enrollment</p>
                            </div>
                        </div>

                        <div class="timeline-item upcoming">
                            <div class="timeline-marker">📅</div>
                            <div class="timeline-content">
                                <h4>Earthquake Preparedness Drill</h4>
                                <p>December 20, 2024 • 9:00 AM - 12:00 PM</p>
                                <p class="event-location">📍 City Convention Center, Main Hall</p>
                                <p class="event-result">📝 Registered</p>
                                <p class="event-action">Awaiting event date</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings Section -->
                <div class="settings-section">
                    <h2>Account Settings</h2>
                    <div class="settings-card">
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Email Notifications</h4>
                                <p>Receive updates about training and events</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>SMS Reminders</h4>
                                <p>Receive reminders before upcoming events</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4>Two-Factor Authentication</h4>
                                <p>Extra security for your account</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-item danger-zone">
                            <div class="setting-info">
                                <h4>Delete Account</h4>
                                <p>Permanently delete your account and all data</p>
                            </div>
                            <button class="btn-danger" onclick="deleteAccount()">Delete</button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/part-main.js"></script>
    <script>
        function editProfile() {
            Swal.fire({
                title: 'Edit Profile',
                html: `<div style="text-align: left;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Full Name</label>
                        <input type="text" id="fullName" value="<?php echo $participant_name; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Email</label>
                        <input type="email" id="email" value="<?php echo $participant_email; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3a7675',
                confirmButtonText: 'Save Changes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Saved!',
                        text: 'Your profile has been updated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#3a7675'
                    });
                }
            });
        }

        function changePassword() {
            Swal.fire({
                title: 'Change Password',
                html: `<div style="text-align: left;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Current Password</label>
                        <input type="password" id="currentPass" placeholder="••••••••" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">New Password</label>
                        <input type="password" id="newPass" placeholder="••••••••" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Confirm Password</label>
                        <input type="password" id="confirmPass" placeholder="••••••••" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3a7675',
                confirmButtonText: 'Update Password',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Your password has been changed successfully.',
                        icon: 'success',
                        confirmButtonColor: '#3a7675'
                    });
                }
            });
        }

        function downloadQRCode() {
            Swal.fire({
                title: 'Download QR Code',
                text: 'Your QR code has been downloaded.',
                icon: 'success',
                confirmButtonColor: '#3a7675'
            });
        }

        function printQRCode() {
            window.print();
        }

        function deleteAccount() {
            Swal.fire({
                title: 'Delete Account',
                text: 'Are you sure you want to permanently delete your account? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c33',
                cancelButtonColor: '#999',
                confirmButtonText: 'Yes, Delete My Account',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Account Deleted',
                        text: 'Your account has been permanently deleted. You will be redirected to the login page.',
                        icon: 'info',
                        confirmButtonColor: '#3a7675',
                        didClose: () => {
                            window.location.href = 'login.php';
                        }
                    });
                }
            });
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
