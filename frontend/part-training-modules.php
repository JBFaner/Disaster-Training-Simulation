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
    <title>Training Modules - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/part-styles.css">
    <link rel="stylesheet" href="css/part-modules.css">
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
                    <li><a href="../part-index.php" class="menu-item">Dashboard</a></li>
                    <li><a href="part-training-modules.php" class="menu-item active">Training Modules</a></li>
                    <li><a href="part-my-events.php" class="menu-item">My Events</a></li>
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
                    <a href="../part-index.php">Home</a>
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
                    <h1>Training Modules</h1>
                    <p class="subtitle">Learn essential disaster preparedness and response skills</p>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-buttons">
                        <button class="filter-btn active" onclick="filterModules('all')">All Modules</button>
                        <button class="filter-btn" onclick="filterModules('completed')">Completed</button>
                        <button class="filter-btn" onclick="filterModules('in-progress')">In Progress</button>
                        <button class="filter-btn" onclick="filterModules('not-started')">Not Started</button>
                    </div>
                </div>

                <!-- Modules Grid -->
                <div class="cards-container">
                    <div class="card module-card" data-status="completed">
                        <div class="card-header">
                            <span class="status-badge completed">✓ Completed</span>
                        </div>
                        <h3>Earthquake Skills</h3>
                        <p>Learn essential earthquake preparedness and safety procedures. Understand drop, cover, and hold on techniques.</p>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-text">100%</span>
                        </div>
                        <button class="btn-action btn-completed" onclick="viewModuleDetails('Earthquake Skills')">View Details</button>
                    </div>

                    <div class="card module-card" data-status="in-progress">
                        <div class="card-header">
                            <span class="status-badge in-progress">⏳ In Progress</span>
                        </div>
                        <h3>Fire Skills</h3>
                        <p>Master fire safety procedures and evacuation techniques. Learn proper use of fire extinguishers and emergency exits.</p>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 65%"></div>
                            </div>
                            <span class="progress-text">65%</span>
                        </div>
                        <button class="btn-action btn-continue" onclick="viewModuleDetails('Fire Skills')">Continue Learning</button>
                    </div>

                    <div class="card module-card" data-status="not-started">
                        <div class="card-header">
                            <span class="status-badge not-started">◯ Not Started</span>
                        </div>
                        <h3>Typhoon</h3>
                        <p>Understand typhoon preparedness and response. Learn about evacuation procedures and typhoon safety measures.</p>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-text">0%</span>
                        </div>
                        <button class="btn-action btn-start" onclick="viewModuleDetails('Typhoon')">Start Learning</button>
                    </div>

                    <div class="card module-card" data-status="completed">
                        <div class="card-header">
                            <span class="status-badge completed">✓ Completed</span>
                        </div>
                        <h3>Flood Response Training</h3>
                        <p>Learn flood response techniques and evacuation routes. Understand early warning systems and emergency procedures.</p>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-text">100%</span>
                        </div>
                        <button class="btn-action btn-completed" onclick="viewModuleDetails('Flood Response Training')">View Details</button>
                    </div>

                    <div class="card module-card" data-status="not-started">
                        <div class="card-header">
                            <span class="status-badge not-started">◯ Not Started</span>
                        </div>
                        <h3>First Aid Basics</h3>
                        <p>Essential first aid techniques for emergency situations. Learn CPR, wound care, and emergency response procedures.</p>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-text">0%</span>
                        </div>
                        <button class="btn-action btn-start" onclick="viewModuleDetails('First Aid Basics')">Start Learning</button>
                    </div>

                    <div class="card module-card" data-status="in-progress">
                        <div class="card-header">
                            <span class="status-badge in-progress">⏳ In Progress</span>
                        </div>
                        <h3>Emergency Communication</h3>
                        <p>Learn effective communication during emergencies. Understand emergency notification systems and communication protocols.</p>
                        <div class="card-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 45%"></div>
                            </div>
                            <span class="progress-text">45%</span>
                        </div>
                        <button class="btn-action btn-continue" onclick="viewModuleDetails('Emergency Communication')">Continue Learning</button>
                    </div>
                </div>

                <!-- Module Detail Modal -->
                <div id="moduleModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <button class="modal-close" onclick="closeModal()">×</button>
                        <div class="modal-header">
                            <h2 id="modalTitle">Module Details</h2>
                            <span id="modalStatus" class="status-badge"></span>
                        </div>
                        <div class="modal-body">
                            <div class="module-details">
                                <div class="detail-section">
                                    <h3>Learning Objectives</h3>
                                    <ul id="objectives" class="objectives-list">
                                        <li>Understand key concepts</li>
                                        <li>Apply practical skills</li>
                                        <li>Pass assessment quiz</li>
                                    </ul>
                                </div>
                                <div class="detail-section">
                                    <h3>Course Content</h3>
                                    <div id="content" class="content-list">
                                        <p>Video tutorials, interactive slides, and quizzes</p>
                                    </div>
                                </div>
                                <div class="detail-section">
                                    <h3>Progress</h3>
                                    <div id="progressDetail" class="progress-detail">
                                        <div class="progress-bar large">
                                            <div class="progress-fill" style="width: 75%"></div>
                                        </div>
                                        <p id="progressPercent">75% Complete</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button onclick="closeModal()" class="btn-secondary">Close</button>
                            <button onclick="continueModule()" class="btn-primary">Continue Module</button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/part-main.js"></script>
    <script>
        function filterModules(status) {
            const cards = document.querySelectorAll('.module-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update active button
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter cards
            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function viewModuleDetails(moduleName) {
            const modal = document.getElementById('moduleModal');
            document.getElementById('modalTitle').textContent = moduleName;
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('moduleModal').style.display = 'none';
        }

        function continueModule() {
            Swal.fire({
                title: 'Launch Module',
                text: 'You will now open the training module in a new interface.',
                icon: 'info',
                confirmButtonColor: '#3a7675',
                confirmButtonText: 'Launch'
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('moduleModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
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
                    window.location.href = '../part-index.php?logout=1';
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
