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
    <title>Evaluation Results - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/part-styles.css">
    <link rel="stylesheet" href="css/part-results.css">
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
                    <li><a href="part-evaluation-results.php" class="menu-item active">Evaluation Results</a></li>
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
                    <h1>Evaluation Results</h1>
                    <p class="subtitle">Review your performance and evaluation scores from completed events</p>
                </div>

                <!-- Results Cards -->
                <div class="results-container">
                    <!-- Result Card 1 - Passed -->
                    <div class="result-card passed">
                        <div class="result-header">
                            <h3>Fire Evacuation Drill</h3>
                            <span class="result-status passed">✓ PASSED</span>
                        </div>
                        <div class="result-date">Evaluated: November 15, 2024</div>
                        
                        <div class="score-section">
                            <div class="score-circle">
                                <div class="score-number">92</div>
                                <div class="score-label">%</div>
                            </div>
                            <div class="score-breakdown">
                                <h4>Score Breakdown:</h4>
                                <div class="score-item">
                                    <span class="score-label">Evacuation Speed</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 90%; background: #4a9b8e;"></div>
                                    </div>
                                    <span class="score-value">90/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Safety Compliance</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 95%; background: #4a9b8e;"></div>
                                    </div>
                                    <span class="score-value">95/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Teamwork & Communication</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 88%; background: #4a9b8e;"></div>
                                    </div>
                                    <span class="score-value">88/100</span>
                                </div>
                            </div>
                        </div>

                        <div class="feedback-section">
                            <h4>Strengths:</h4>
                            <ul class="strengths-list">
                                <li>✓ Excellent evacuation time - faster than average</li>
                                <li>✓ Proper use of evacuation routes</li>
                                <li>✓ Good communication with team members</li>
                                <li>✓ Maintained calm demeanor throughout</li>
                            </ul>

                            <h4>Areas for Improvement:</h4>
                            <ul class="improvement-list">
                                <li>~ Could improve speed in closing doors behind you</li>
                                <li>~ Remember to assist slower participants if able</li>
                            </ul>

                            <h4>Trainer Comments:</h4>
                            <p class="trainer-comment">Excellent performance! You demonstrated strong understanding of fire safety procedures. Your calm demeanor and quick response were impressive. Focus on assisting teammates and you'll be a model participant.</p>
                        </div>

                        <div class="result-actions">
                            <button class="btn-action-primary" onclick="downloadResult('Fire Evacuation Drill')">Download PDF Report</button>
                            <button class="btn-action-secondary" onclick="shareFeedback('Fire Evacuation Drill')">Share Feedback</button>
                        </div>
                    </div>

                    <!-- Result Card 2 - Passed -->
                    <div class="result-card passed">
                        <div class="result-header">
                            <h3>Earthquake Drill - Phase 1</h3>
                            <span class="result-status passed">✓ PASSED</span>
                        </div>
                        <div class="result-date">Evaluated: October 28, 2024</div>
                        
                        <div class="score-section">
                            <div class="score-circle">
                                <div class="score-number">88</div>
                                <div class="score-label">%</div>
                            </div>
                            <div class="score-breakdown">
                                <h4>Score Breakdown:</h4>
                                <div class="score-item">
                                    <span class="score-label">DROP, COVER, HOLD Response</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 85%; background: #4a9b8e;"></div>
                                    </div>
                                    <span class="score-value">85/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Evacuation Procedure</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 90%; background: #4a9b8e;"></div>
                                    </div>
                                    <span class="score-value">90/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Assembly Point Accountability</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 88%; background: #4a9b8e;"></div>
                                    </div>
                                    <span class="score-value">88/100</span>
                                </div>
                            </div>
                        </div>

                        <div class="feedback-section">
                            <h4>Strengths:</h4>
                            <ul class="strengths-list">
                                <li>✓ Quick initial response to earthquake warning</li>
                                <li>✓ Proper protection of head and neck</li>
                                <li>✓ Followed evacuation procedures correctly</li>
                            </ul>

                            <h4>Areas for Improvement:</h4>
                            <ul class="improvement-list">
                                <li>~ Hold your position a bit longer during shaking</li>
                                <li>~ Check for aftershocks before moving</li>
                            </ul>

                            <h4>Trainer Comments:</h4>
                            <p class="trainer-comment">Good performance overall. You understood the key concepts well. With more practice on earthquake procedures, you'll be excellent.</p>
                        </div>

                        <div class="result-actions">
                            <button class="btn-action-primary" onclick="downloadResult('Earthquake Drill - Phase 1')">Download PDF Report</button>
                            <button class="btn-action-secondary" onclick="shareFeedback('Earthquake Drill - Phase 1')">Share Feedback</button>
                        </div>
                    </div>

                    <!-- Result Card 3 - Need Improvement -->
                    <div class="result-card needs-improvement">
                        <div class="result-header">
                            <h3>First Aid Assessment</h3>
                            <span class="result-status needs-improvement">⚠ NEEDS IMPROVEMENT</span>
                        </div>
                        <div class="result-date">Evaluated: September 20, 2024</div>
                        
                        <div class="score-section">
                            <div class="score-circle">
                                <div class="score-number">72</div>
                                <div class="score-label">%</div>
                            </div>
                            <div class="score-breakdown">
                                <h4>Score Breakdown:</h4>
                                <div class="score-item">
                                    <span class="score-label">CPR Technique</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 70%; background: #f39c12;"></div>
                                    </div>
                                    <span class="score-value">70/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Wound Management</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 75%; background: #f39c12;"></div>
                                    </div>
                                    <span class="score-value">75/100</span>
                                </div>
                                <div class="score-item">
                                    <span class="score-label">Patient Assessment</span>
                                    <div class="score-bar">
                                        <div class="score-bar-fill" style="width: 72%; background: #f39c12;"></div>
                                    </div>
                                    <span class="score-value">72/100</span>
                                </div>
                            </div>
                        </div>

                        <div class="feedback-section">
                            <h4>Strengths:</h4>
                            <ul class="strengths-list">
                                <li>✓ Good understanding of basic procedures</li>
                                <li>✓ Showed willingness to help</li>
                            </ul>

                            <h4>Areas for Improvement:</h4>
                            <ul class="improvement-list">
                                <li>⚠ Need to improve CPR hand positioning</li>
                                <li>⚠ Compression depth needs adjustment</li>
                                <li>⚠ Work on faster patient assessment</li>
                                <li>⚠ Recommend refresher training</li>
                            </ul>

                            <h4>Trainer Comments:</h4>
                            <p class="trainer-comment">You have the foundation, but you need to practice more on CPR techniques. I recommend attending our First Aid Refresher course next month.</p>
                        </div>

                        <div class="result-actions">
                            <button class="btn-action-primary" onclick="registerRefresherCourse()">Register for Refresher Course</button>
                            <button class="btn-action-secondary" onclick="downloadResult('First Aid Assessment')">Download PDF Report</button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/part-main.js"></script>
    <script>
        function downloadResult(resultName) {
            Swal.fire({
                title: 'Download Report',
                text: `Downloading evaluation report for ${resultName}...`,
                icon: 'info',
                confirmButtonColor: '#3a7675',
                didOpen: () => {
                    // Simulate download
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Downloaded!',
                            text: 'Your PDF report has been downloaded.',
                            icon: 'success',
                            confirmButtonColor: '#3a7675'
                        });
                    }, 1500);
                }
            });
        }

        function shareFeedback(resultName) {
            Swal.fire({
                title: 'Share Feedback',
                html: `Share your evaluation feedback for ${resultName}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3a7675',
                confirmButtonText: 'Share',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    // You can add form here if needed
                }
            });
        }

        function registerRefresherCourse() {
            Swal.fire({
                title: 'Register for Refresher Course',
                text: 'You have been added to the First Aid Refresher course scheduled for next month. You will receive a confirmation email shortly.',
                icon: 'success',
                confirmButtonColor: '#3a7675'
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
