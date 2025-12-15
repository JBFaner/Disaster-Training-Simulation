<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation Event Planning - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin-simulation-planning.css">
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
                    <li><a href="../index.php" class="menu-item">Dashboard</a></li>
                    <li><a href="admin-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="admin-scenario-design.php" class="menu-item">Scenario Design</a></li>
                    <li><a href="admin-simulation-planning.php" class="menu-item active">Simulation Planning</a></li>
                    <li><a href="admin-registration-attendance.php" class="menu-item">Registration</a></li>
                    <li><a href="admin-evaluation-scoring.php" class="menu-item">Evaluation</a></li>
                    <li><a href="admin-inventory.php" class="menu-item">Inventory</a></li>
                    <li><a href="admin-certificate-issuance.php" class="menu-item">Certificate</a></li>
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
                        window.location.href = '../index.php?logout=1';
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
                <h2>Admin Panel</h2>
                <div class="user-icon">👤</div>
            </header>

            <!-- Page Content -->
            <section class="content admin-content">
                <div class="page-header">
                    <h1>Simulation Event Planning</h1>
                    <button class="btn-primary" id="createEventBtn">+ Create New Event</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="events-list">Events List</button>
                    <button class="tab-btn" data-tab="create-edit">Create/Edit Event</button>
                    <button class="tab-btn" data-tab="workflow">Event Workflow</button>
                    <button class="tab-btn" data-tab="resources">Resources</button>
                    <button class="tab-btn" data-tab="reports">Reports</button>
                </div>

                <!-- Tab 1: Events List -->
                <div class="tab-content active" id="events-list">
                    <div class="filter-bar">
                        <input type="text" id="searchEvents" placeholder="Search events...">
                        <select id="filterDisasterType">
                            <option value="">All Disaster Types</option>
                            <option value="earthquake">Earthquake</option>
                            <option value="fire">Fire</option>
                            <option value="flood">Flood</option>
                            <option value="typhoon">Typhoon</option>
                            <option value="landslide">Landslide</option>
                            <option value="chemical">Chemical Accident</option>
                        </select>
                        <select id="filterCategory">
                            <option value="">All Categories</option>
                            <option value="drill">Drill</option>
                            <option value="full-scale">Full-scale Exercise</option>
                            <option value="tabletop">Tabletop Exercise</option>
                            <option value="training">Training Session</option>
                        </select>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="events-table-wrapper">
                        <table class="events-table">
                            <thead>
                                <tr>
                                    <th>Event Title</th>
                                    <th>Disaster Type</th>
                                    <th>Category</th>
                                    <th>Date & Time</th>
                                    <th>Location</th>
                                    <th>Participants</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="eventsTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Create/Edit Event -->
                <div class="tab-content" id="create-edit">
                    <form id="eventForm" class="event-form">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h3>Basic Information</h3>
                            <div class="form-group">
                                <label for="eventTitle">Event Title *</label>
                                <input type="text" id="eventTitle" name="title" required placeholder="e.g., Earthquake Evacuation Drill">
                            </div>

                            <div class="form-group">
                                <label for="eventDescription">Event Description / Learning Objective *</label>
                                <textarea id="eventDescription" name="description" required placeholder="Describe the event and its objectives" rows="4"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="disasterType">Disaster Type *</label>
                                    <select id="disasterType" name="disaster_type" required>
                                        <option value="">Select Type</option>
                                        <option value="earthquake">Earthquake</option>
                                        <option value="fire">Fire</option>
                                        <option value="flood">Flood</option>
                                        <option value="typhoon">Typhoon</option>
                                        <option value="landslide">Landslide</option>
                                        <option value="chemical">Chemical Accident</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="eventCategory">Event Category *</label>
                                    <select id="eventCategory" name="event_category" required>
                                        <option value="">Select Category</option>
                                        <option value="drill">Drill</option>
                                        <option value="full-scale">Full-scale Exercise</option>
                                        <option value="tabletop">Tabletop Exercise</option>
                                        <option value="training">Training Session</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Event Schedule -->
                        <div class="form-section">
                            <h3>Event Schedule</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="eventDate">Event Date *</label>
                                    <input type="date" id="eventDate" name="event_date" required>
                                </div>

                                <div class="form-group">
                                    <label for="startTime">Start Time *</label>
                                    <input type="time" id="startTime" name="start_time" required>
                                </div>

                                <div class="form-group">
                                    <label for="endTime">End Time *</label>
                                    <input type="time" id="endTime" name="end_time" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="eventDuration">Event Duration (auto-calculated)</label>
                                <input type="text" id="eventDuration" name="duration" readonly placeholder="Duration will be calculated">
                            </div>

                            <div class="form-group">
                                <label>Event Recurrence (Optional)</label>
                                <div class="checkboxes-group">
                                    <label><input type="radio" name="recurrence" value="none" checked> None</label>
                                    <label><input type="radio" name="recurrence" value="weekly"> Weekly</label>
                                    <label><input type="radio" name="recurrence" value="monthly"> Monthly</label>
                                    <label><input type="radio" name="recurrence" value="yearly"> Yearly</label>
                                </div>
                            </div>
                        </div>

                        <!-- Event Location -->
                        <div class="form-section">
                            <h3>Event Location</h3>
                            <div class="form-group">
                                <label for="eventLocation">Location/Building/Area *</label>
                                <input type="text" id="eventLocation" name="location" required placeholder="e.g., City Hall Building">
                            </div>

                            <div class="form-group">
                                <label for="roomNumber">Room Number / Section (Optional)</label>
                                <input type="text" id="roomNumber" name="room_number" placeholder="e.g., 3rd Floor, Room 301">
                            </div>

                            <div class="form-group">
                                <label for="accessibilityNotes">Accessibility Notes (exits, hazards, etc.)</label>
                                <textarea id="accessibilityNotes" name="accessibility_notes" placeholder="Document exits, fire extinguishers, hazard zones" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="siteMap">Upload Site Map / Building Layout (Optional)</label>
                                <div class="file-upload-area" id="mapUpload">
                                    <input type="file" id="siteMap" name="site_map" accept="image/*,.pdf">
                                    <p>Upload site map or building layout</p>
                                </div>
                            </div>
                        </div>

                        <!-- Assign Scenario -->
                        <div class="form-section">
                            <h3>Assign Scenario</h3>
                            <div class="form-group">
                                <label for="assignedScenario">Choose Scenario *</label>
                                <select id="assignedScenario" name="assigned_scenario" required>
                                    <option value="">Select Scenario</option>
                                </select>
                            </div>

                            <div id="scenarioSummary" style="background: #f0f7ff; padding: 15px; border-radius: 5px; margin-top: 15px; display: none;">
                                <h4 style="margin-top: 0;">Scenario Summary</h4>
                                <p id="scenarioSummaryText"></p>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="scenario_required"> Mark scenario as "Required" for participants
                                </label>
                            </div>
                        </div>

                        <!-- Assign Trainers/Facilitators -->
                        <div class="form-section">
                            <h3>Assign Trainers / Facilitators</h3>
                            <button type="button" class="btn-secondary" id="addTrainerBtn">+ Add Trainer</button>
                            <div id="trainersContainer" style="margin-top: 15px;">
                                <!-- Trainers will be added here -->
                            </div>
                        </div>

                        <!-- Participant Management -->
                        <div class="form-section">
                            <h3>Participant Management</h3>
                            <div class="form-group">
                                <label>Participant Groups *</label>
                                <div class="checkboxes-group">
                                    <label><input type="checkbox" name="participant_groups" value="staff"> Staff</label>
                                    <label><input type="checkbox" name="participant_groups" value="volunteers"> Volunteers</label>
                                    <label><input type="checkbox" name="participant_groups" value="students"> Students</label>
                                    <label><input type="checkbox" name="participant_groups" value="responders"> Emergency Responders</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="maxCapacity">Maximum Participant Capacity *</label>
                                    <input type="number" id="maxCapacity" name="max_capacity" min="1" required value="50">
                                </div>

                                <div class="form-group">
                                    <label for="minRequired">Minimum Required</label>
                                    <input type="number" id="minRequired" name="min_required" min="0" value="10">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="public_registration" checked> Enable Public Registration
                                </label>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="attendance_code"> Enable Attendance Code/QR System
                                </label>
                            </div>
                        </div>

                        <!-- Safety & Compliance -->
                        <div class="form-section">
                            <h3>Safety & Compliance Settings</h3>
                            <div class="form-group">
                                <label for="safetyGuidelines">Safety Guidelines *</label>
                                <textarea id="safetyGuidelines" name="safety_guidelines" required placeholder="List all safety guidelines for this event" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="hazardWarnings">Hazard Warnings</label>
                                <textarea id="hazardWarnings" name="hazard_warnings" placeholder="Document any hazards participants should be aware of" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Required PPE</label>
                                <div class="checkboxes-group">
                                    <label><input type="checkbox" name="required_ppe" value="helmet"> Helmet</label>
                                    <label><input type="checkbox" name="required_ppe" value="vest"> Safety Vest</label>
                                    <label><input type="checkbox" name="required_ppe" value="gloves"> Gloves</label>
                                    <label><input type="checkbox" name="required_ppe" value="mask"> Mask</label>
                                    <label><input type="checkbox" name="required_ppe" value="boots"> Steel-toed Boots</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="safetyDocuments">Upload Safety Documents (PDF/Image)</label>
                                <div class="file-upload-area" id="safetyUpload">
                                    <input type="file" id="safetyDocuments" name="safety_documents" multiple accept=".pdf,image/*">
                                    <p>Upload safety documents, maps, guides</p>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Status -->
                        <div class="form-section">
                            <h3>Publish Status</h3>
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="publish_status" value="published"> Publish Event
                                </label>
                                <label>
                                    <input type="radio" name="publish_status" value="draft" checked> Save as Draft
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Event</button>
                            <button type="reset" class="btn-secondary">Reset</button>
                            <button type="button" class="btn-danger" id="cancelEventBtn">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Event Workflow -->
                <div class="tab-content" id="workflow">
                    <div class="workflow-builder">
                        <div class="workflow-controls">
                            <button class="btn-secondary" id="addPhaseBtn">+ Add Event Phase</button>
                        </div>

                        <div id="workflowPhases" class="workflow-phases">
                            <!-- Phases will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Resources -->
                <div class="tab-content" id="resources">
                    <div class="resources-management">
                        <div class="resources-controls">
                            <button class="btn-secondary" id="addResourceBtn">+ Add Resource</button>
                        </div>

                        <div id="resourcesContainer" class="resources-container">
                            <!-- Resources will be added here -->
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Reports -->
                <div class="tab-content" id="reports">
                    <div class="reports-container">
                        <div class="reports-filters">
                            <select id="reportEventFilter">
                                <option value="">Select Event</option>
                            </select>
                            <button class="btn-primary" id="generateReportBtn">Generate Report</button>
                            <button class="btn-primary" id="exportReportBtn">Export Report</button>
                        </div>

                        <div class="reports-grid">
                            <div class="report-card">
                                <h4>Registered Participants</h4>
                                <p class="report-value" id="registeredParticipants">0</p>
                            </div>
                            <div class="report-card">
                                <h4>Checked-in</h4>
                                <p class="report-value" id="checkedInCount">0</p>
                            </div>
                            <div class="report-card">
                                <h4>No-shows</h4>
                                <p class="report-value" id="noShowCount">0</p>
                            </div>
                            <div class="report-card">
                                <h4>Attendance Rate</h4>
                                <p class="report-value" id="attendanceRate">0%</p>
                            </div>
                        </div>

                        <div class="event-logs">
                            <h4>Event Logs & Attendance</h4>
                            <div id="eventLogsTable"></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Trainer -->
    <div id="trainerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add/Edit Trainer</h3>
            <form id="trainerForm">
                <div class="form-group">
                    <label for="trainerName">Trainer Name *</label>
                    <input type="text" id="trainerName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="trainerRole">Role *</label>
                    <select id="trainerRole" name="role" required>
                        <option value="">Select Role</option>
                        <option value="lead">Lead Trainer</option>
                        <option value="safety">Safety Officer</option>
                        <option value="observer">Observer</option>
                        <option value="evaluator">Evaluator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="trainerContact">Contact Number *</label>
                    <input type="tel" id="trainerContact" name="contact" required>
                </div>
                <div class="form-group">
                    <label for="trainerEmail">Email Address *</label>
                    <input type="email" id="trainerEmail" name="email" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Trainer</button>
                    <button type="button" class="btn-secondary" id="closeTrainerBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Phase -->
    <div id="phaseModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add/Edit Event Phase</h3>
            <form id="phaseForm">
                <div class="form-group">
                    <label for="phaseName">Phase Name *</label>
                    <input type="text" id="phaseName" name="name" required placeholder="e.g., Briefing">
                </div>
                <div class="form-group">
                    <label for="phaseDuration">Duration (minutes) *</label>
                    <input type="number" id="phaseDuration" name="duration" min="1" required>
                </div>
                <div class="form-group">
                    <label for="phaseDescription">Description *</label>
                    <textarea id="phaseDescription" name="description" required rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="facilitatorInstructions">Facilitator Instructions (Optional)</label>
                    <textarea id="facilitatorInstructions" name="instructions" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Phase</button>
                    <button type="button" class="btn-secondary" id="closePhaseBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    <script src="js/admin-simulation-planning.js"></script>
</body>
</html>
