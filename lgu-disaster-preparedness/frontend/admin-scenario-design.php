<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario Design - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin-scenario-design.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="../images/logo.svg" alt="Logo" class="logo">
                <span class="brand-name">DisasterQC</span>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li><a href="index.php" class="menu-item">Dashboard</a></li>
                    <li><a href="admin-training-modules.php" class="menu-item">Training Modules</a></li>
                    <li><a href="admin-scenario-design.php" class="menu-item active">Scenario Design</a></li>
                    <li><a href="admin-simulation-planning.php" class="menu-item">Simulation Planning</a></li>
                    <li><a href="admin-registration-attendance.php" class="menu-item">Registration</a></li>
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
                    <h1>Scenario-Based Exercise Design</h1>
                    <button class="btn-primary" id="createScenarioBtn">+ Create New Scenario</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="scenarios-list">Scenarios List</button>
                    <button class="tab-btn" data-tab="create-edit">Create/Edit Scenario</button>
                    <button class="tab-btn" data-tab="timeline">Timeline Builder</button>
                    <button class="tab-btn" data-tab="evaluation">Evaluation Setup</button>
                    <button class="tab-btn" data-tab="analytics">Analytics</button>
                </div>

                <!-- Tab 1: Scenarios List -->
                <div class="tab-content active" id="scenarios-list">
                    <div class="filter-bar">
                        <input type="text" id="searchScenarios" placeholder="Search scenarios...">
                        <select id="filterDisasterType">
                            <option value="">All Disaster Types</option>
                            <option value="earthquake">Earthquake</option>
                            <option value="fire">Fire</option>
                            <option value="flood">Flood</option>
                            <option value="typhoon">Typhoon</option>
                            <option value="landslide">Landslide</option>
                            <option value="chemical">Chemical Spill</option>
                            <option value="multi-hazard">Multi-hazard</option>
                        </select>
                        <select id="filterDifficulty">
                            <option value="">All Difficulty Levels</option>
                            <option value="basic">Basic</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <div class="scenarios-table-wrapper">
                        <table class="scenarios-table">
                            <thead>
                                <tr>
                                    <th>Scenario Title</th>
                                    <th>Disaster Type</th>
                                    <th>Difficulty</th>
                                    <th>Status</th>
                                    <th>Events Used</th>
                                    <th>Avg Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="scenariosTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Create/Edit Scenario -->
                <div class="tab-content" id="create-edit">
                    <form id="scenarioForm" class="scenario-form">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h3>Basic Information</h3>
                            <div class="form-group">
                                <label for="scenarioTitle">Scenario Title *</label>
                                <input type="text" id="scenarioTitle" name="title" required placeholder="e.g., Downtown Building Earthquake">
                            </div>

                            <div class="form-group">
                                <label for="scenarioDescription">Short Description *</label>
                                <textarea id="scenarioDescription" name="description" required placeholder="Brief description of the scenario" rows="3"></textarea>
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
                                        <option value="chemical">Chemical Spill</option>
                                        <option value="multi-hazard">Multi-hazard</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="difficultyLevel">Difficulty Level *</label>
                                    <select id="difficultyLevel" name="difficulty" required>
                                        <option value="">Select Level</option>
                                        <option value="basic">Basic</option>
                                        <option value="intermediate">Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="intendedParticipants">Intended Participants *</label>
                                <div class="checkboxes-group">
                                    <label><input type="checkbox" name="participants" value="students"> Students</label>
                                    <label><input type="checkbox" name="participants" value="staff"> Staff</label>
                                    <label><input type="checkbox" name="participants" value="volunteers"> Volunteers</label>
                                    <label><input type="checkbox" name="participants" value="responders"> Emergency Responders</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="safetyNotes">Safety Notes/Warnings (Optional)</label>
                                <textarea id="safetyNotes" name="safety_notes" placeholder="Any safety warnings for participants" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Scenario Conditions -->
                        <div class="form-section">
                            <h3>Scenario Conditions</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="incidentTime">Incident Time *</label>
                                    <select id="incidentTime" name="incident_time" required>
                                        <option value="day">Day</option>
                                        <option value="night">Night</option>
                                        <option value="dawn">Dawn</option>
                                        <option value="dusk">Dusk</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="weatherCondition">Weather Condition *</label>
                                    <select id="weatherCondition" name="weather_condition" required>
                                        <option value="sunny">Sunny</option>
                                        <option value="rainy">Rainy</option>
                                        <option value="stormy">Stormy</option>
                                        <option value="windy">Windy</option>
                                        <option value="foggy">Foggy</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="locationType">Location Type *</label>
                                <select id="locationType" name="location_type" required>
                                    <option value="building">Building</option>
                                    <option value="open-field">Open Field</option>
                                    <option value="classroom">Classroom</option>
                                    <option value="street">Street</option>
                                    <option value="office">Office</option>
                                    <option value="hospital">Hospital</option>
                                    <option value="transit">Transit Station</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Hazard Variables</label>
                                <div class="checkboxes-group">
                                    <label><input type="checkbox" name="hazard_variables" value="aftershocks"> Aftershocks</label>
                                    <label><input type="checkbox" name="hazard_variables" value="fire-spread"> Fire Spread</label>
                                    <label><input type="checkbox" name="hazard_variables" value="rising-water"> Rising Flood Water</label>
                                    <label><input type="checkbox" name="hazard_variables" value="blocked-exits"> Blocked Exits</label>
                                    <label><input type="checkbox" name="hazard_variables" value="power-outage"> Power Outage</label>
                                    <label><input type="checkbox" name="hazard_variables" value="gas-leak"> Gas Leak</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="casualtyCount">Casualty Count *</label>
                                    <input type="number" id="casualtyCount" name="casualty_count" min="0" required value="0">
                                </div>

                                <div class="form-group">
                                    <label for="victimCondition">Victim Condition Presets</label>
                                    <select id="victimCondition" name="victim_condition" multiple size="4">
                                        <option value="minor-injury">Minor Injury</option>
                                        <option value="major-injury">Major Injury</option>
                                        <option value="unconscious">Unconscious</option>
                                        <option value="trapped">Trapped</option>
                                        <option value="psychological-trauma">Psychological Trauma</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Link to Training Module -->
                        <div class="form-section">
                            <h3>Link to Training Module</h3>
                            <div class="form-group">
                                <label for="trainingModule">Training Module *</label>
                                <select id="trainingModule" name="training_module" required>
                                    <option value="">Select Module</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_required"> Required Scenario
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="learningObjectives">Learning Objectives</label>
                                <textarea id="learningObjectives" name="learning_objectives" placeholder="List the learning objectives" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Target Competencies</label>
                                <div class="checkboxes-group">
                                    <label><input type="checkbox" name="competencies" value="evacuation"> Evacuation</label>
                                    <label><input type="checkbox" name="competencies" value="communication"> Communication</label>
                                    <label><input type="checkbox" name="competencies" value="first-aid"> First Aid</label>
                                    <label><input type="checkbox" name="competencies" value="hazard-identification"> Hazard Identification</label>
                                    <label><input type="checkbox" name="competencies" value="coordination"> Coordination</label>
                                    <label><input type="checkbox" name="competencies" value="decision-making"> Decision Making</label>
                                </div>
                            </div>
                        </div>

                        <!-- Multimedia Elements -->
                        <div class="form-section">
                            <h3>Multimedia Elements</h3>
                            <div class="form-group">
                                <label for="scenarioImages">Images</label>
                                <div class="file-upload-area" id="imageUpload">
                                    <input type="file" id="scenarioImages" name="images" multiple accept="image/*">
                                    <p>Upload images (JPG, PNG)</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="scenarioAudio">Audio Files (Alarms, Sirens, etc.)</label>
                                <div class="file-upload-area" id="audioUpload">
                                    <input type="file" id="scenarioAudio" name="audio" multiple accept="audio/*">
                                    <p>Upload audio files (MP3, WAV)</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="scenarioVideos">Videos</label>
                                <div class="file-upload-area" id="videoUpload">
                                    <input type="file" id="scenarioVideos" name="videos" multiple accept="video/*">
                                    <p>Upload videos (MP4, WebM)</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="scenarioDocuments">Documents (Maps, Layouts, etc.)</label>
                                <div class="file-upload-area" id="documentUpload">
                                    <input type="file" id="scenarioDocuments" name="documents" multiple accept=".pdf,.doc,.docx">
                                    <p>Upload documents (PDF, DOC)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Status -->
                        <div class="form-section">
                            <h3>Publish Status</h3>
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="publish_status" value="published"> Published
                                </label>
                                <label>
                                    <input type="radio" name="publish_status" value="draft" checked> Save as Draft
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Scenario</button>
                            <button type="reset" class="btn-secondary">Reset</button>
                            <button type="button" class="btn-danger" id="cancelScenarioBtn">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Timeline Builder -->
                <div class="tab-content" id="timeline">
                    <div class="timeline-builder">
                        <div class="timeline-controls">
                            <button class="btn-secondary" id="addEventBtn">+ Add Event Step</button>
                        </div>

                        <div id="timelineEvents" class="timeline-events">
                            <!-- Events will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Evaluation Setup -->
                <div class="tab-content" id="evaluation">
                    <div class="evaluation-setup">
                        <form id="evaluationForm">
                            <h3>Evaluation Criteria</h3>
                            
                            <div class="evaluation-metrics">
                                <button type="button" class="btn-secondary" id="addMetricBtn">+ Add Metric</button>
                                <div id="metricsContainer">
                                    <!-- Metrics will be added here -->
                                </div>
                            </div>

                            <div class="form-section">
                                <h4>Checklist Items for Evaluators</h4>
                                <button type="button" class="btn-secondary" id="addChecklistBtn">+ Add Checklist Item</button>
                                <div id="checklistContainer">
                                    <!-- Checklist items will be added here -->
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-group">
                                    <label for="passingScore">Pass/Fail Threshold (%) *</label>
                                    <input type="number" id="passingScore" name="passing_score" min="0" max="100" value="70" required>
                                </div>

                                <label>
                                    <input type="checkbox" name="auto_scoring" id="autoScoring"> Enable Automatic Scoring
                                </label>
                            </div>

                            <button type="submit" class="btn-primary">Save Evaluation Setup</button>
                        </form>
                    </div>
                </div>

                <!-- Tab 5: Analytics -->
                <div class="tab-content" id="analytics">
                    <div class="analytics-container">
                        <div class="analytics-filters">
                            <select id="analyticsScenarioFilter">
                                <option value="">Select Scenario</option>
                            </select>
                            <input type="date" id="analyticsStartDate">
                            <input type="date" id="analyticsEndDate">
                            <button class="btn-primary" id="exportScenarioAnalyticsBtn">Export (CSV/PDF)</button>
                        </div>

                        <div class="analytics-grid">
                            <div class="analytics-card">
                                <h4>Events Used</h4>
                                <p class="analytics-value" id="eventsUsed">0</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Average Score</h4>
                                <p class="analytics-value" id="avgScenarioScore">0%</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Pass Rate</h4>
                                <p class="analytics-value" id="passRate">0%</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Total Participants</h4>
                                <p class="analytics-value" id="totalParticipantsScenario">0</p>
                            </div>
                        </div>

                        <div class="analytics-chart">
                            <h4>Most Common Failed Actions</h4>
                            <div id="failedActionsChart"></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Event Timeline -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add/Edit Timeline Event</h3>
            <form id="eventForm">
                <div class="form-group">
                    <label for="eventTitle">Event Title/Description *</label>
                    <input type="text" id="eventTitle" name="title" required placeholder="e.g., Initial earthquake strike">
                </div>
                <div class="form-group">
                    <label for="triggerTime">Trigger Time (seconds) *</label>
                    <input type="number" id="triggerTime" name="trigger_time" min="0" required placeholder="0">
                </div>
                <div class="form-group">
                    <label for="instructorPrompt">Instructor Prompt (Optional)</label>
                    <textarea id="instructorPrompt" name="instructor_prompt" placeholder="Guidance for the instructor" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="expectedAction">Expected Participant Action *</label>
                    <textarea id="expectedAction" name="expected_action" required placeholder="What should participants do?" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_escalation"> Escalation Event
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_end_scenario"> End of Scenario
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Event</button>
                    <button type="button" class="btn-secondary" id="closeEventBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/admin-scenario-design.js"></script>
</body>
</html>
