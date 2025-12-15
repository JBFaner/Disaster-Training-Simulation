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
    <title>Scenarios - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/part-styles.css">
    <link rel="stylesheet" href="css/part-scenarios.css">
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
                    <li><a href="part-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="part-my-events.php" class="menu-item">My Events</a></li>
                    <li><a href="part-scenarios.php" class="menu-item active">Scenarios</a></li>
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
                    <h1>Simulation Scenarios</h1>
                    <p class="subtitle">Follow these scenarios during your simulation events to practice real-world disaster response</p>
                </div>

                <div class="cards-container">
                    <div class="scenario-card">
                        <div class="scenario-header">
                            <span class="scenario-type earthquake">🏢 Earthquake Scenario</span>
                        </div>
                        <h3>Magnitude 6.5 Earthquake Strike</h3>
                        <div class="scenario-timeline">
                            <div class="timeline-item">
                                <span class="timeline-marker">00:00</span>
                                <p><strong>Ground Movement Detected:</strong> Tremors begin immediately. Building starts swaying. Do NOT use elevators.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">00:15</span>
                                <p><strong>Initial Response:</strong> DROP, COVER, and HOLD ON under sturdy desks. Protect your head and neck.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">00:45</span>
                                <p><strong>Evacuation Order:</strong> Once movement stops, proceed to assembly points. Use stairs only, not elevators.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:00</span>
                                <p><strong>Accountability Check:</strong> Report to team leaders. Account for all team members.</p>
                            </div>
                        </div>
                        <button class="btn-read-more" onclick="viewScenarioDetails('Earthquake')">Read Full Scenario →</button>
                    </div>

                    <div class="scenario-card">
                        <div class="scenario-header">
                            <span class="scenario-type fire">🔥 Fire Scenario</span>
                        </div>
                        <h3>Multi-Floor Fire Outbreak</h3>
                        <div class="scenario-timeline">
                            <div class="timeline-item">
                                <span class="timeline-marker">00:00</span>
                                <p><strong>Fire Alarm Activation:</strong> Alarms sound throughout the building. Implement immediate response procedures.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">00:30</span>
                                <p><strong>Floor Evacuation:</strong> Proceed to nearest exit calmly. Feel doors before opening. Use stairs, never elevators.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:00</span>
                                <p><strong>Assembly Point:</strong> Gather at designated assembly points outside the building.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:15</span>
                                <p><strong>Headcount & Report:</strong> Team leaders report missing persons to incident commander.</p>
                            </div>
                        </div>
                        <button class="btn-read-more" onclick="viewScenarioDetails('Fire')">Read Full Scenario →</button>
                    </div>

                    <div class="scenario-card">
                        <div class="scenario-header">
                            <span class="scenario-type flood">🌊 Flood Scenario</span>
                        </div>
                        <h3>Flash Flood Warning & Evacuation</h3>
                        <div class="scenario-timeline">
                            <div class="timeline-item">
                                <span class="timeline-marker">00:00</span>
                                <p><strong>Flood Warning Issued:</strong> Alert received of imminent flooding in low-lying areas.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">00:30</span>
                                <p><strong>Evacuation Signal:</strong> Move to higher ground immediately. Take essential documents and supplies.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:00</span>
                                <p><strong>Safe Zone Assembly:</strong> Gather at designated higher elevation assembly points.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:30</span>
                                <p><strong>Communication Check:</strong> Report status to incident command. Provide updates on trapped individuals if any.</p>
                            </div>
                        </div>
                        <button class="btn-read-more" onclick="viewScenarioDetails('Flood')">Read Full Scenario →</button>
                    </div>

                    <div class="scenario-card">
                        <div class="scenario-header">
                            <span class="scenario-type typhoon">🌪️ Typhoon Scenario</span>
                        </div>
                        <h3>Typhoon Preparedness & Shelter In Place</h3>
                        <div class="scenario-timeline">
                            <div class="timeline-item">
                                <span class="timeline-marker">00:00</span>
                                <p><strong>Typhoon Alert Issued:</strong> Category 3 Typhoon approaching. Implement preparedness protocols.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:00</span>
                                <p><strong>Shelter Preparation:</strong> Move to designated shelter areas away from windows and exterior walls.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">02:00</span>
                                <p><strong>Resource Distribution:</strong> Distribute food, water, and medical supplies to sheltered participants.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">04:00</span>
                                <p><strong>Welfare Check:</strong> Conduct welfare checks. Monitor health and morale of all individuals.</p>
                            </div>
                        </div>
                        <button class="btn-read-more" onclick="viewScenarioDetails('Typhoon')">Read Full Scenario →</button>
                    </div>

                    <div class="scenario-card">
                        <div class="scenario-header">
                            <span class="scenario-type medical">🏥 Medical Emergency Scenario</span>
                        </div>
                        <h3>Mass Casualty Management</h3>
                        <div class="scenario-timeline">
                            <div class="timeline-item">
                                <span class="timeline-marker">00:00</span>
                                <p><strong>Incident Report:</strong> Multiple casualties reported. Activate medical response protocols.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">00:15</span>
                                <p><strong>Triage Setup:</strong> Establish triage areas. Categorize casualties by severity (Red, Yellow, Green).</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">00:45</span>
                                <p><strong>First Aid Response:</strong> Provide first aid to stable patients. Prepare critical patients for evacuation.</p>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-marker">01:30</span>
                                <p><strong>Transportation:</strong> Coordinate transport to medical facilities. Maintain communication with hospitals.</p>
                            </div>
                        </div>
                        <button class="btn-read-more" onclick="viewScenarioDetails('Medical')">Read Full Scenario →</button>
                    </div>
                </div>

                <!-- Scenario Detail Modal -->
                <div id="scenarioModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <button class="modal-close" onclick="closeScenarioModal()">×</button>
                        <div id="scenarioModalBody" class="scenario-modal-body">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="js/part-main.js"></script>
    <script>
        const scenarioDetails = {
            'Earthquake': {
                title: 'Magnitude 6.5 Earthquake Strike',
                description: 'A 6.5 magnitude earthquake strikes the area. Participants must follow proper earthquake response procedures.',
                fullContent: `
                    <h2>Full Earthquake Scenario</h2>
                    <h3>Objective:</h3>
                    <p>Practice DROP, COVER, and HOLD ON techniques followed by safe evacuation procedures.</p>
                    <h3>Timeline & Actions:</h3>
                    <ul>
                        <li><strong>T+0:00 - Ground Movement Detected:</strong> Tremors begin. Buildings sway. Immediately DROP, COVER, and HOLD ON under sturdy tables or desks.</li>
                        <li><strong>T+0:15 - Sustained Movement:</strong> Continue holding position. Protect head and neck. Do not run outside while shaking.</li>
                        <li><strong>T+0:45 - Shaking Stops:</strong> Carefully check surroundings. Proceed to nearest safe exit using stairs only.</li>
                        <li><strong>T+1:00 - Evacuation Complete:</strong> All participants assemble at designated outdoor assembly points.</li>
                        <li><strong>T+1:30 - Accountability Check:</strong> Team leaders conduct headcount and report status to incident commander.</li>
                    </ul>
                    <h3>Key Points:</h3>
                    <ul>
                        <li>Never use elevators during or immediately after an earthquake</li>
                        <li>Protect your head and neck with your arms</li>
                        <li>Stay away from windows and unsecured furniture</li>
                        <li>Expect aftershocks</li>
                    </ul>
                `
            },
            'Fire': {
                title: 'Multi-Floor Fire Outbreak',
                description: 'A fire breaks out in the building. Participants must evacuate safely and assemble at designated points.',
                fullContent: `
                    <h2>Full Fire Scenario</h2>
                    <h3>Objective:</h3>
                    <p>Execute controlled evacuation procedures and account for all personnel at assembly points.</p>
                    <h3>Timeline & Actions:</h3>
                    <ul>
                        <li><strong>T+0:00 - Alarm Activation:</strong> Fire alarms sound throughout building. Begin immediate evacuation.</li>
                        <li><strong>T+0:15 - Evacuation Procedures:</strong> Feel doors before opening. Crouch low to avoid smoke. Use nearest stairwell.</li>
                        <li><strong>T+0:45 - Ground Level Reached:</strong> Continue to nearest exit. Do not use elevators under any circumstances.</li>
                        <li><strong>T+1:00 - Assembly Point:</strong> Gather at designated outdoor assembly point away from building.</li>
                        <li><strong>T+1:30 - Accountability Report:</strong> Team leaders report all personnel accounted for.</li>
                    </ul>
                    <h3>Critical Actions:</h3>
                    <ul>
                        <li>Always use stairs, never elevators</li>
                        <li>Close doors behind you to slow fire spread</li>
                        <li>Crouch low when exiting to avoid smoke</li>
                        <li>Account for all team members at assembly point</li>
                    </ul>
                `
            },
            'Flood': {
                title: 'Flash Flood Warning & Evacuation',
                description: 'A flash flood warning is issued for the area. Participants must evacuate to higher ground.',
                fullContent: `
                    <h2>Full Flood Scenario</h2>
                    <h3>Objective:</h3>
                    <p>Execute rapid evacuation to higher ground and manage resources for stranded individuals.</p>
                    <h3>Timeline & Actions:</h3>
                    <ul>
                        <li><strong>T+0:00 - Flood Warning:</strong> Flash flood alert issued. Immediately begin evacuation preparation.</li>
                        <li><strong>T+0:15 - Gather Essentials:</strong> Collect important documents, medications, and emergency supplies.</li>
                        <li><strong>T+0:30 - Evacuation Begins:</strong> Move to designated higher elevation assembly point using planned evacuation routes.</li>
                        <li><strong>T+1:00 - Safe Zone Assembly:</strong> All participants gathered at high-elevation assembly point.</li>
                        <li><strong>T+2:00 - Resource Management:</strong> Distribute food, water, and first aid supplies. Monitor for injuries.</li>
                    </ul>
                    <h3>Important Notes:</h3>
                    <ul>
                        <li>Do not attempt to drive through flooded areas</li>
                        <li>Move to higher ground immediately upon warning</li>
                        <li>Bring essential items and medications</li>
                        <li>Report any missing persons immediately</li>
                    </ul>
                `
            },
            'Typhoon': {
                title: 'Typhoon Preparedness & Shelter In Place',
                description: 'A Category 3 Typhoon is approaching. Participants must shelter in place and manage resources.',
                fullContent: `
                    <h2>Full Typhoon Scenario</h2>
                    <h3>Objective:</h3>
                    <p>Prepare shelter facilities and manage resources for extended sheltering during typhoon passage.</p>
                    <h3>Timeline & Actions:</h3>
                    <ul>
                        <li><strong>T+0:00 - Typhoon Alert:</strong> Category 3 Typhoon approaching. Activate shelter operations.</li>
                        <li><strong>T+1:00 - Shelter Preparation:</strong> Move to designated shelter areas away from windows and doors.</li>
                        <li><strong>T+2:00 - Resource Distribution:</strong> Distribute food, water, medicine, and blankets. Set up first aid station.</li>
                        <li><strong>T+4:00 - Peak Impact:</strong> Monitor weather updates. Conduct welfare checks on all sheltered persons.</li>
                        <li><strong>T+8:00 - All Clear:</strong> Once wind speeds decrease, conduct post-impact assessment and clean-up.</li>
                    </ul>
                    <h3>Shelter Management:</h3>
                    <ul>
                        <li>Move away from windows and exterior walls</li>
                        <li>Keep shelter doors and windows secured</li>
                        <li>Maintain adequate ventilation</li>
                        <li>Monitor health and psychological well-being of sheltered persons</li>
                    </ul>
                `
            },
            'Medical': {
                title: 'Mass Casualty Management',
                description: 'Multiple casualties reported. Set up triage and provide emergency medical response.',
                fullContent: `
                    <h2>Full Medical Emergency Scenario</h2>
                    <h3>Objective:</h3>
                    <p>Establish triage, provide emergency first aid, and coordinate evacuation of critical patients.</p>
                    <h3>Timeline & Actions:</h3>
                    <ul>
                        <li><strong>T+0:00 - Incident Report:</strong> Multiple casualties reported. Activate emergency medical response.</li>
                        <li><strong>T+0:10 - Triage Setup:</strong> Establish triage area. Categorize patients: RED (Critical), YELLOW (Urgent), GREEN (Stable).</li>
                        <li><strong>T+0:30 - First Aid Response:</strong> Provide basic first aid. Control bleeding, manage airways, position stably.</li>
                        <li><strong>T+1:00 - Transportation Coordination:</strong> Arrange transport for RED and YELLOW patients to medical facilities.</li>
                        <li><strong>T+2:00 - Ongoing Care:</strong> Continue monitoring GREEN patients. Document all treatments and patient status.</li>
                    </ul>
                    <h3>Triage Categories:</h3>
                    <ul>
                        <li><strong>RED (Critical):</strong> Severe injuries, difficulty breathing, severe bleeding - transport immediately</li>
                        <li><strong>YELLOW (Urgent):</strong> Moderate injuries, conscious - transport within 30 minutes</li>
                        <li><strong>GREEN (Stable):</strong> Minor injuries, alert and oriented - can wait for transport</li>
                    </ul>
                `
            }
        };

        function viewScenarioDetails(scenarioType) {
            const scenario = scenarioDetails[scenarioType];
            const modal = document.getElementById('scenarioModal');
            const body = document.getElementById('scenarioModalBody');
            
            body.innerHTML = scenario.fullContent;
            modal.style.display = 'block';
        }

        function closeScenarioModal() {
            document.getElementById('scenarioModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('scenarioModal');
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
