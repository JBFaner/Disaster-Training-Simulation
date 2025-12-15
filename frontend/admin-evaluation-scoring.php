<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation & Scoring - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/admin-evaluation-scoring.css">
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
                    <li><a href="admin-simulation-planning.php" class="menu-item">Simulation Planning</a></li>
                    <li><a href="admin-registration-attendance.php" class="menu-item">Registration & Attendance</a></li>
                    <li><a href="admin-evaluation-scoring.php" class="menu-item active">Evaluation & Scoring</a></li>
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
                    <h1>Evaluation & Scoring System</h1>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="dashboard">Evaluation Dashboard</button>
                    <button class="tab-btn" data-tab="criteria-setup">Criteria Setup</button>
                    <button class="tab-btn" data-tab="participant-scoring">Participant Scoring</button>
                    <button class="tab-btn" data-tab="performance">Performance Analysis</button>
                    <button class="tab-btn" data-tab="logs">Evaluation Logs</button>
                </div>

                <!-- Tab 1: Evaluation Dashboard -->
                <div class="tab-content active" id="dashboard">
                    <div class="filter-bar">
                        <select id="filterEvaluationEvent">
                            <option value="">Select Event</option>
                        </select>
                        <select id="filterEvaluationStatus">
                            <option value="">All Status</option>
                            <option value="pending">Pending Evaluation</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="finalized">Finalized</option>
                        </select>
                    </div>

                    <div class="evaluation-summary">
                        <div class="summary-card">
                            <h4>Total Participants</h4>
                            <p class="summary-value" id="totalParticipantsEval">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>Pending Evaluation</h4>
                            <p class="summary-value" id="pendingEvalCount">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>Average Score</h4>
                            <p class="summary-value" id="avgEventScore">0%</p>
                        </div>
                        <div class="summary-card">
                            <h4>Pass Rate</h4>
                            <p class="summary-value" id="eventPassRate">0%</p>
                        </div>
                    </div>

                    <div class="events-evaluation-wrapper">
                        <table class="events-evaluation-table">
                            <thead>
                                <tr>
                                    <th>Participant Name</th>
                                    <th>Role</th>
                                    <th>Attendance</th>
                                    <th>Evaluation Status</th>
                                    <th>Total Score</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="evaluationDashboardBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Criteria Setup -->
                <div class="tab-content" id="criteria-setup">
                    <div class="criteria-controls">
                        <button class="btn-secondary" id="createTemplateBtn" type="button">+ Create Evaluation Template</button>
                        <button class="btn-secondary" id="loadTemplateBtn" type="button">📋 Load Template</button>
                    </div>

                    <form id="criteriaForm" class="criteria-form">
                        <div class="form-section">
                            <h3>Evaluation Criteria Setup</h3>
                            
                            <div class="form-group">
                                <label for="templateName">Template Name (Optional)</label>
                                <input type="text" id="templateName" name="template_name" placeholder="e.g., Earthquake Drill Evaluation">
                            </div>

                            <div class="form-group">
                                <label for="scoringMethod">Scoring Method *</label>
                                <select id="scoringMethod" name="scoring_method" required>
                                    <option value="">Select Method</option>
                                    <option value="0-100">Numerical (0-100)</option>
                                    <option value="1-5">Rating Scale (1-5)</option>
                                    <option value="1-10">Rating Scale (1-10)</option>
                                    <option value="pass-fail">Pass/Fail</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="passingThreshold">Passing Threshold (%) *</label>
                                <input type="number" id="passingThreshold" name="passing_threshold" min="0" max="100" value="70" required>
                            </div>

                            <div class="form-group">
                                <label>Evaluation Criteria</label>
                                <button type="button" class="btn-secondary" id="addCriteriaBtn">+ Add Criterion</button>
                            </div>

                            <div id="criteriaContainer" class="criteria-container">
                                <!-- Criteria items will be added here -->
                            </div>

                            <div class="form-group">
                                <label for="evaluatorInstructions">Evaluator Instructions</label>
                                <textarea id="evaluatorInstructions" name="evaluator_instructions" placeholder="Guidelines for evaluators" rows="4"></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Save Criteria</button>
                                <button type="button" class="btn-secondary" id="saveAsTemplateCriteriaBtn">Save as Template</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Participant Scoring -->
                <div class="tab-content" id="participant-scoring">
                    <div class="filter-bar">
                        <select id="filterScoringEvent">
                            <option value="">Select Event</option>
                        </select>
                    </div>

                    <div class="participants-scoring-wrapper">
                        <table class="participants-scoring-table">
                            <thead>
                                <tr>
                                    <th>Participant Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="participantsScoringBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 4: Performance Analysis -->
                <div class="tab-content" id="performance">
                    <div class="filter-bar">
                        <select id="analyticsEventFilter">
                            <option value="">Select Event</option>
                        </select>
                        <button class="btn-primary" id="exportPerformanceBtn" type="button">Export Report</button>
                    </div>

                    <div class="performance-grid">
                        <div class="performance-card">
                            <h4>Average Score</h4>
                            <p class="performance-value" id="avgScoreAnalytics">0%</p>
                        </div>
                        <div class="performance-card">
                            <h4>Highest Score</h4>
                            <p class="performance-value" id="highestScoreAnalytics">0%</p>
                        </div>
                        <div class="performance-card">
                            <h4>Lowest Score</h4>
                            <p class="performance-value" id="lowestScoreAnalytics">0%</p>
                        </div>
                        <div class="performance-card">
                            <h4>Pass Rate</h4>
                            <p class="performance-value" id="passRateAnalytics">0%</p>
                        </div>
                    </div>

                    <div class="performance-breakdown">
                        <div class="breakdown-section">
                            <h4>Performance by Criterion</h4>
                            <div id="criterionBreakdown"></div>
                        </div>
                        <div class="breakdown-section">
                            <h4>Performance by Role</h4>
                            <div id="roleBreakdown"></div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Evaluation Logs -->
                <div class="tab-content" id="logs">
                    <div class="filter-bar">
                        <input type="text" id="searchEvaluationLogs" placeholder="Search by participant...">
                        <select id="filterEvaluatorLogs">
                            <option value="">All Evaluators</option>
                            <option value="admin">Admin</option>
                            <option value="trainer">Trainer</option>
                        </select>
                    </div>

                    <div class="logs-wrapper">
                        <table class="logs-table">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Event</th>
                                    <th>Evaluator</th>
                                    <th>Score</th>
                                    <th>Result</th>
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Scoring -->
    <div id="scoringModal" class="modal">
        <div class="modal-content large-modal">
            <span class="close">&times;</span>
            <h3>Evaluate Participant</h3>
            <form id="scoringForm">
                <div class="form-section">
                    <h4 id="participantNameHeader"></h4>
                    <div class="participant-info">
                        <p><strong>Role:</strong> <span id="participantRoleInfo"></span></p>
                        <p><strong>Event:</strong> <span id="eventNameInfo"></span></p>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Scoring</h4>
                    <div id="scoringCriteria">
                        <!-- Criteria scoring fields will be added here -->
                    </div>
                </div>

                <div class="form-section">
                    <h4>Feedback</h4>
                    <div class="form-group">
                        <label for="evaluatorFeedback">Evaluator Comments</label>
                        <textarea id="evaluatorFeedback" name="feedback" placeholder="Provide detailed feedback" rows="4"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Result</h4>
                    <div class="form-group">
                        <label for="evaluationResult">Mark as *</label>
                        <select id="evaluationResult" name="result" required>
                            <option value="">Select Result</option>
                            <option value="passed">Passed</option>
                            <option value="failed">Failed</option>
                            <option value="needs-improvement">Needs Improvement</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Evaluation</button>
                    <button type="button" class="btn-secondary" id="closeScoringBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Template Loading -->
    <div id="templateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Load Evaluation Template</h3>
            <div id="templateList" class="template-list">
                <!-- Templates will be listed here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    <script src="js/admin-evaluation-scoring.js"></script>
</body>
</html>
