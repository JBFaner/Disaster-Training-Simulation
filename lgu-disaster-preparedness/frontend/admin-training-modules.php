<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Module Management - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin-training-modules.css">
    <!-- SweetAlert2 CDN -->
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
                    <li><a href="admin-training-modules.php" class="menu-item active">Training Modules</a></li>
                    <li><a href="admin-scenario-design.php" class="menu-item">Scenario Design</a></li>
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
                    <h1>Training Module Management</h1>
                    <button class="btn-primary" id="createModuleBtn">+ Create New Module</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="modules-list">Modules List</button>
                    <button class="tab-btn" data-tab="create-edit">Create/Edit Module</button>
                    <button class="tab-btn" data-tab="analytics">Analytics</button>
                    <button class="tab-btn" data-tab="archived">Archived Modules</button>
                </div>

                <!-- Tab 1: Modules List -->
                <div class="tab-content active" id="modules-list">
                    <div class="filter-bar">
                        <input type="text" id="searchModules" placeholder="Search modules...">
                        <select id="filterCategory">
                            <option value="">All Categories</option>
                            <option value="earthquake">Earthquake</option>
                            <option value="fire">Fire</option>
                            <option value="flood">Flood</option>
                            <option value="multi-hazard">Multi-hazard</option>
                        </select>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <div class="modules-table-wrapper">
                        <table class="modules-table">
                            <thead>
                                <tr>
                                    <th>Module Title</th>
                                    <th>Category</th>
                                    <th>Difficulty</th>
                                    <th>Status</th>
                                    <th>Participants</th>
                                    <th>Completion Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="modulesTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Create/Edit Module -->
                <div class="tab-content" id="create-edit">
                    <form id="moduleForm" class="module-form">
                        <div class="form-section">
                            <h3>Basic Information</h3>
                            <div class="form-group">
                                <label for="moduleTitle">Module Title *</label>
                                <input type="text" id="moduleTitle" name="title" required placeholder="e.g., Earthquake Response Drills">
                            </div>

                            <div class="form-group">
                                <label for="moduleDescription">Description *</label>
                                <textarea id="moduleDescription" name="description" required placeholder="Enter module description" rows="5"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="moduleDifficulty">Difficulty Level *</label>
                                    <select id="moduleDifficulty" name="difficulty" required>
                                        <option value="">Select Level</option>
                                        <option value="beginner">Beginner</option>
                                        <option value="intermediate">Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="moduleCategory">Category *</label>
                                    <select id="moduleCategory" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="earthquake">Earthquake</option>
                                        <option value="fire">Fire</option>
                                        <option value="flood">Flood</option>
                                        <option value="multi-hazard">Multi-hazard</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Lessons Management -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3>Lessons</h3>
                                <button type="button" class="btn-secondary" id="addLessonBtn">+ Add Lesson</button>
                            </div>

                            <div id="lessonsContainer">
                                <!-- Lessons will be added dynamically -->
                            </div>
                        </div>

                        <!-- Scenario Linking -->
                        <div class="form-section">
                            <h3>Link Scenarios</h3>
                            <div class="scenario-selector">
                                <label>Available Scenarios:</label>
                                <div id="scenariosCheckboxes" class="checkboxes-group">
                                    <!-- Populated by JavaScript -->
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="defaultScenario">Default Scenario for Drills</label>
                                <select id="defaultScenario" name="default_scenario">
                                    <option value="">None</option>
                                </select>
                            </div>
                        </div>

                        <!-- Resource Attachments -->
                        <div class="form-section">
                            <h3>Resource Attachments</h3>
                            <div class="form-group">
                                <label for="instructionalMaterials">Instructional Materials (PDF, Images, Videos)</label>
                                <div class="file-upload-area" id="materialUpload">
                                    <input type="file" id="instructionalMaterials" name="materials" multiple accept=".pdf,.jpg,.png,.mp4,.pptx">
                                    <p>Drag & drop files or click to upload</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="trainerMaterials">Trainer Materials</label>
                                <input type="file" id="trainerMaterials" name="trainer_materials" multiple>
                            </div>

                            <div class="form-group">
                                <label for="safetyChecklist">Safety Checklist (PDF)</label>
                                <input type="file" id="safetyChecklist" name="safety_checklist" accept=".pdf">
                            </div>
                        </div>

                        <!-- Access Control -->
                        <div class="form-section">
                            <h3>Access Control</h3>
                            <div class="form-group">
                                <label for="editAccess">Who Can Edit</label>
                                <select id="editAccess" name="edit_access">
                                    <option value="admin">Admin Only</option>
                                    <option value="trainer">Admin & Trainer</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="visibility">Module Visibility</label>
                                <select id="visibility" name="visibility">
                                    <option value="all">All Participants</option>
                                    <option value="specific">Specific Groups</option>
                                    <option value="staff">Staff Only</option>
                                </select>
                            </div>

                            <div id="specificGroupsSelect" style="display:none;" class="form-group">
                                <label>Select Groups:</label>
                                <div class="checkboxes-group">
                                    <label><input type="checkbox" name="groups" value="group1"> Group 1</label>
                                    <label><input type="checkbox" name="groups" value="group2"> Group 2</label>
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
                            <button type="submit" class="btn-primary">Save Module</button>
                            <button type="reset" class="btn-secondary">Reset</button>
                            <button type="button" class="btn-danger" id="cancelBtn">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Analytics -->
                <div class="tab-content" id="analytics">
                    <div class="analytics-container">
                        <div class="analytics-filters">
                            <select id="analyticsModuleFilter">
                                <option value="">Select Module</option>
                            </select>
                            <input type="date" id="analyticsStartDate">
                            <input type="date" id="analyticsEndDate">
                            <button class="btn-primary" id="exportAnalyticsBtn">Export (CSV/PDF)</button>
                        </div>

                        <div class="analytics-grid">
                            <div class="analytics-card">
                                <h4>Total Participants</h4>
                                <p class="analytics-value" id="totalParticipants">0</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Completion Rate</h4>
                                <p class="analytics-value" id="completionRate">0%</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Average Quiz Score</h4>
                                <p class="analytics-value" id="avgQuizScore">0%</p>
                            </div>
                            <div class="analytics-card">
                                <h4>Most Completed Lesson</h4>
                                <p class="analytics-value" id="mostCompleted">-</p>
                            </div>
                        </div>

                        <div class="analytics-chart">
                            <h4>Completion by Lesson</h4>
                            <div id="lessonCompletionChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Archived Modules -->
                <div class="tab-content" id="archived">
                    <div class="modules-table-wrapper">
                        <table class="modules-table">
                            <thead>
                                <tr>
                                    <th>Module Title</th>
                                    <th>Category</th>
                                    <th>Archived Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="archivedTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Lesson Management -->
    <div id="lessonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add/Edit Lesson</h3>
            <form id="lessonForm">
                <div class="form-group">
                    <label for="lessonTitle">Lesson Title *</label>
                    <input type="text" id="lessonTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="lessonDescription">Description *</label>
                    <textarea id="lessonDescription" name="description" required rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="lessonFiles">Lesson Files (PDF, MP4, PowerPoint, etc.)</label>
                    <input type="file" id="lessonFiles" name="files" multiple>
                </div>
                <div class="form-group">
                    <label for="lessonQuiz">Attach Quiz/Assessment (Optional)</label>
                    <select id="lessonQuiz" name="quiz">
                        <option value="">None</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Lesson</button>
                    <button type="button" class="btn-secondary" id="closeLesson">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Version Control -->
    <div id="versionModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Version History</h3>
            <div id="versionList" class="version-list">
                <!-- Version history populated by JavaScript -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/admin-training-modules.js"></script>
</body>
</html>
