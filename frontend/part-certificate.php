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
    <title>Certificates - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/part-styles.css">
    <link rel="stylesheet" href="css/part-certificates.css">
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
                    <li><a href="../index.php" class="menu-item">Dashboard</a></li>
                    <li><a href="part-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="part-my-events.php" class="menu-item">My Events</a></li>
                    <li><a href="part-scenarios.php" class="menu-item">Scenarios</a></li>
                    <li><a href="part-evaluation-results.php" class="menu-item">Evaluation Results</a></li>
                    <li><a href="part-certificate.php" class="menu-item active">Certificates</a></li>
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
                    <a href="../index.php">Home</a>
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
                    <h1>My Certificates</h1>
                    <p class="subtitle">Your earned certificates from completed training and events</p>
                </div>

                <div class="certificate-stats">
                    <div class="stat-item">
                        <span class="stat-number">2</span>
                        <span class="stat-label">Certificates Earned</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">5</span>
                        <span class="stat-label">Training Hours</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">90%</span>
                        <span class="stat-label">Average Score</span>
                    </div>
                </div>

                <!-- Certificates Grid -->
                <div class="certificates-grid">
                    <!-- Certificate 1 -->
                    <div class="certificate-card">
                        <div class="certificate-preview">
                            <div class="certificate-placeholder">
                                <div class="certificate-mockup">
                                    <div class="cert-header">Certificate of Completion</div>
                                    <div class="cert-title">Fire Safety Expert</div>
                                    <div class="cert-recipient">Presented to<br><?php echo $participant_name; ?></div>
                                    <div class="cert-date">November 15, 2024</div>
                                </div>
                            </div>
                        </div>
                        <div class="certificate-info">
                            <h3>Fire Safety Expert</h3>
                            <p class="cert-event">Fire Evacuation Drill</p>
                            <p class="cert-date-issued">📅 Issued: November 15, 2024</p>
                            <p class="cert-score">⭐ Score: 92%</p>
                            <p class="cert-validity">Valid for 2 years</p>
                            <div class="certificate-actions">
                                <button class="btn-download" onclick="downloadCertificate('Fire Safety Expert')">
                                    <span>📥 Download PDF</span>
                                </button>
                                <button class="btn-share" onclick="shareCertificate('Fire Safety Expert')">
                                    <span>📤 Share</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate 2 -->
                    <div class="certificate-card">
                        <div class="certificate-preview">
                            <div class="certificate-placeholder">
                                <div class="certificate-mockup">
                                    <div class="cert-header">Certificate of Completion</div>
                                    <div class="cert-title">Earthquake Preparedness</div>
                                    <div class="cert-recipient">Presented to<br><?php echo $participant_name; ?></div>
                                    <div class="cert-date">October 28, 2024</div>
                                </div>
                            </div>
                        </div>
                        <div class="certificate-info">
                            <h3>Earthquake Preparedness</h3>
                            <p class="cert-event">Earthquake Drill - Phase 1</p>
                            <p class="cert-date-issued">📅 Issued: October 28, 2024</p>
                            <p class="cert-score">⭐ Score: 88%</p>
                            <p class="cert-validity">Valid for 2 years</p>
                            <div class="certificate-actions">
                                <button class="btn-download" onclick="downloadCertificate('Earthquake Preparedness')">
                                    <span>📥 Download PDF</span>
                                </button>
                                <button class="btn-share" onclick="shareCertificate('Earthquake Preparedness')">
                                    <span>📤 Share</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State for Pending -->
                <div class="pending-section">
                    <h2>Pending Certificates</h2>
                    <div class="pending-info">
                        <p>✓ You have completed 1 event and are pending certificate issuance:</p>
                        <div class="pending-items">
                            <div class="pending-item">
                                <h4>First Aid Training</h4>
                                <p>Event Date: September 20, 2024</p>
                                <p class="pending-status">⏳ Currently pending evaluation</p>
                            </div>
                        </div>
                        <p class="pending-note">Certificates are typically issued within 5-7 business days after event completion. You will receive an email notification once your certificate is ready.</p>
                    </div>
                </div>

                <!-- Certificate Guidelines -->
                <div class="certificate-guidelines">
                    <h2>Certificate Guidelines</h2>
                    <div class="guidelines-content">
                        <div class="guideline-item">
                            <h4>📋 Eligibility</h4>
                            <p>You must pass (score ≥ 75%) the evaluation to receive a certificate.</p>
                        </div>
                        <div class="guideline-item">
                            <h4>📅 Validity Period</h4>
                            <p>Certificates are valid for 2 years from the date of issue. You will be notified before expiration.</p>
                        </div>
                        <div class="guideline-item">
                            <h4>📄 Usage</h4>
                            <p>You can download and print your certificates anytime. They are recognized by all partner organizations.</p>
                        </div>
                        <div class="guideline-item">
                            <h4>🔄 Renewal</h4>
                            <p>To renew your certificate, attend the training again before it expires.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/part-main.js"></script>
    <script>
        function downloadCertificate(certName) {
            Swal.fire({
                title: 'Download Certificate',
                text: `Downloading ${certName} certificate as PDF...`,
                icon: 'info',
                confirmButtonColor: '#3a7675',
                allowOutsideClick: false,
                didOpen: () => {
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Downloaded!',
                            text: `Your ${certName} certificate has been downloaded.`,
                            icon: 'success',
                            confirmButtonColor: '#3a7675'
                        });
                    }, 1500);
                }
            });
        }

        function shareCertificate(certName) {
            Swal.fire({
                title: 'Share Certificate',
                html: `<div style="text-align: left;">
                    <p>Share your ${certName} certificate with:</p>
                    <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center;">
                        <button style="padding: 8px 16px; background: #1da1f2; color: white; border: none; border-radius: 4px; cursor: pointer;">📤 LinkedIn</button>
                        <button style="padding: 8px 16px; background: #1877f2; color: white; border: none; border-radius: 4px; cursor: pointer;">📤 Facebook</button>
                        <button style="padding: 8px 16px; background: #25d366; color: white; border: none; border-radius: 4px; cursor: pointer;">📤 WhatsApp</button>
                    </div>
                </div>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3a7675',
                confirmButtonText: 'Copy Link',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Copied!',
                        text: 'Certificate share link copied to clipboard.',
                        icon: 'success',
                        confirmButtonColor: '#3a7675'
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
                    window.location.href = '../index.php?logout=1';
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
