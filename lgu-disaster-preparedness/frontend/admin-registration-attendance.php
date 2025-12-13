<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration & Attendance - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin-registration-attendance.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
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
                    <li><a href="index.php" class="menu-item">Dashboard</a></li>
                    <li><a href="admin-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="admin-scenario-design.php" class="menu-item">Scenario Design</a></li>
                    <li><a href="admin-simulation-planning.php" class="menu-item">Simulation Planning</a></li>
                    <li><a href="admin-registration-attendance.php" class="menu-item active">Registration & Attendance</a></li>
                    <li><a href="admin-evaluation-scoring.php" class="menu-item">Evaluation</a></li>
                    <li><a href="admin-inventory.php" class="menu-item">Inventory</a></li>
                    <li><a href="admin-certificate-issuance.php" class="menu-item">Certificate</a></li>
                </ul>
            </nav>
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
                <h2>Admin Panel</h2>
                <div class="user-icon">👤</div>
            </header>

            <!-- Page Content -->
            <section class="content admin-content">
                <div class="page-header">
                    <h1>Participant Registration & Attendance</h1>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="participants">Participants List</button>
                    <button class="tab-btn" data-tab="registrations">Event Registrations</button>
                    <button class="tab-btn" data-tab="attendance">Attendance Tracking</button>
                    <button class="tab-btn" data-tab="history">Activity History</button>
                    <button class="tab-btn" data-tab="analytics">Analytics</button>
                </div>

                <!-- Tab 1: Participants List -->
                <div class="tab-content active" id="participants">
                    <div class="filter-bar">
                        <input type="text" id="searchParticipants" placeholder="Search by name...">
                        <select id="filterRole">
                            <option value="">All Roles</option>
                            <option value="staff">Staff</option>
                            <option value="volunteer">Volunteer</option>
                            <option value="student">Student</option>
                            <option value="community">Community Member</option>
                        </select>
                        <select id="filterDepartment">
                            <option value="">All Departments</option>
                            <option value="hr">Human Resources</option>
                            <option value="ops">Operations</option>
                            <option value="logistics">Logistics</option>
                            <option value="medical">Medical</option>
                        </select>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="participants-table-wrapper">
                        <table class="participants-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Contact</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="participantsTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Event Registrations -->
                <div class="tab-content" id="registrations">
                    <div class="filter-bar">
                        <select id="filterEventRegistration">
                            <option value="">Select Event</option>
                        </select>
                        <select id="filterApprovalStatus">
                            <option value="">All Status</option>
                            <option value="pending">Pending Approval</option>
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                            <option value="auto-approved">Auto-Approved</option>
                        </select>
                    </div>

                    <div class="registrations-table-wrapper">
                        <table class="registrations-table">
                            <thead>
                                <tr>
                                    <th>Participant Name</th>
                                    <th>Event</th>
                                    <th>Registration Date</th>
                                    <th>Approval Status</th>
                                    <th>Registered Participants</th>
                                    <th>Available Slots</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="registrationsTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="form-section" style="margin-top: 30px;">
                        <h4>Auto-Approval Settings</h4>
                        <label>
                            <input type="checkbox" id="enableAutoApproval"> Enable auto-approval for all event registrations
                        </label>
                    </div>
                </div>

                <!-- Tab 3: Attendance Tracking -->
                <div class="tab-content" id="attendance">
                    <div class="attendance-controls">
                        <div class="filter-bar">
                            <select id="filterAttendanceEvent">
                                <option value="">Select Event</option>
                            </select>
                            <input type="date" id="attendanceDate">
                            <button class="btn-secondary" id="checkInQRBtn">📱 QR Code Check-in</button>
                            <button class="btn-secondary" id="checkInCodeBtn">🔐 Attendance Code</button>
                        </div>
                    </div>

                    <div class="attendance-cards">
                        <div class="attendance-card">
                            <h4>Present</h4>
                            <p class="attendance-count" id="presentCount">0</p>
                        </div>
                        <div class="attendance-card">
                            <h4>Late</h4>
                            <p class="attendance-count" id="lateCount">0</p>
                        </div>
                        <div class="attendance-card">
                            <h4>Absent</h4>
                            <p class="attendance-count" id="absentCount">0</p>
                        </div>
                        <div class="attendance-card">
                            <h4>Attendance Rate</h4>
                            <p class="attendance-count" id="attendanceRate">0%</p>
                        </div>
                    </div>

                    <div class="attendance-table-wrapper">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Participant Name</th>
                                    <th>Role</th>
                                    <th>Check-in Time</th>
                                    <th>Check-out Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 4: Activity History -->
                <div class="tab-content" id="history">
                    <div class="filter-bar">
                        <input type="text" id="searchParticipantHistory" placeholder="Search participant...">
                        <select id="filterEventHistory">
                            <option value="">All Events</option>
                        </select>
                    </div>

                    <div class="history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Participant Name</th>
                                    <th>Event Attended</th>
                                    <th>Date</th>
                                    <th>Attendance Status</th>
                                    <th>Completion Status</th>
                                    <th>Last Attended</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 5: Analytics -->
                <div class="tab-content" id="analytics">
                    <div class="analytics-container">
                        <div class="analytics-filters">
                            <select id="analyticsEventFilter">
                                <option value="">Select Event</option>
                            </select>
                            <input type="date" id="analyticsStartDate">
                            <input type="date" id="analyticsEndDate">
                            <button class="btn-primary" id="exportAnalyticsBtn">Export Report</button>
                        </div>

                        <div class="analytics-grid">
                            <div class="analytics-card">
                                <h4>Total Participants</h4>
                                <p class="analytics-value" id="totalParticipantsAnalytics">0</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Present</h4>
                                <p class="analytics-value" id="presentAnalytics">0</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Absent</h4>
                                <p class="analytics-value" id="absentAnalytics">0</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Completed Full Event</h4>
                                <p class="analytics-value" id="completedAnalytics">0</p>
                            </div>
                        </div>

                        <div class="analytics-breakdown">
                            <div class="breakdown-card">
                                <h4>Attendance by Role</h4>
                                <div id="attendanceByRole"></div>
                            </div>
                            <div class="breakdown-card">
                                <h4>Attendance by Department</h4>
                                <div id="attendanceByDepartment"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Edit Participant -->
    <div id="editParticipantModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Edit Participant Information</h3>
            <form id="editParticipantForm">
                <div class="form-group">
                    <label for="editParticipantName">Name</label>
                    <input type="text" id="editParticipantName" name="name" readonly>
                </div>
                <div class="form-group">
                    <label for="editParticipantRole">Role</label>
                    <select id="editParticipantRole" name="role" required>
                        <option value="staff">Staff</option>
                        <option value="volunteer">Volunteer</option>
                        <option value="student">Student</option>
                        <option value="community">Community Member</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editParticipantDept">Department</label>
                    <select id="editParticipantDept" name="department" required>
                        <option value="hr">Human Resources</option>
                        <option value="ops">Operations</option>
                        <option value="logistics">Logistics</option>
                        <option value="medical">Medical</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editParticipantContact">Contact Number</label>
                    <input type="tel" id="editParticipantContact" name="contact" required>
                </div>
                <div class="form-group">
                    <label for="editParticipantEmail">Email</label>
                    <input type="email" id="editParticipantEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="editParticipantActive" name="active"> Active Account
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="button" class="btn-secondary" id="closeEditBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for QR Code Check-in -->
    <div id="qrCheckInModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>QR Code Check-in</h3>
            <div id="qrReaderContainer"></div>
            <input type="text" id="qrInput" placeholder="Scan QR code here..." autofocus>
            <button type="button" class="btn-primary" id="processQRBtn" style="margin-top: 15px; width: 100%;">Process</button>
        </div>
    </div>

    <!-- Modal for Attendance Code Check-in -->
    <div id="codeCheckInModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Attendance Code Check-in</h3>
            <div class="form-group">
                <label for="attendanceCodeInput">Enter Attendance Code</label>
                <input type="text" id="attendanceCodeInput" placeholder="e.g., SAFE2024" autofocus>
            </div>
            <div class="form-group">
                <label for="participantNameInput">Participant Name</label>
                <input type="text" id="participantNameInput" placeholder="Auto-filled or enter name">
            </div>
            <button type="button" class="btn-primary" id="submitCodeBtn" style="width: 100%; margin-top: 15px;">Check In</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    <script src="js/admin-registration-attendance.js"></script>
</body>
</html>
