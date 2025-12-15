<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Issuance - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin-certificate-issuance.css">
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
                    <li><a href="admin-simulation-planning.php" class="menu-item">Simulation Planning</a></li>
                    <li><a href="admin-registration-attendance.php" class="menu-item">Registration & Attendance</a></li>
                    <li><a href="admin-evaluation-scoring.php" class="menu-item">Evaluation & Scoring</a></li>
                    <li><a href="admin-inventory.php" class="menu-item">Inventory</a></li>
                    <li><a href="admin-certificate-issuance.php" class="menu-item active">Certificate Issuance</a></li>
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
                    <h1>Certificate Issuance Management</h1>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="dashboard">Dashboard</button>
                    <button class="tab-btn" data-tab="templates">Template Management</button>
                    <button class="tab-btn" data-tab="generation">Generate Certificates</button>
                    <button class="tab-btn" data-tab="issuance">Certificate Issuance</button>
                    <button class="tab-btn" data-tab="history">History & Logs</button>
                    <button class="tab-btn" data-tab="validation">Validation</button>
                </div>

                <!-- Tab 1: Dashboard -->
                <div class="tab-content active" id="dashboard">
                    <div class="certificate-summary">
                        <div class="summary-card">
                            <h4>Total Issued</h4>
                            <p class="summary-value" id="totalIssued">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>Pending</h4>
                            <p class="summary-value" id="pendingCerts">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>Active Templates</h4>
                            <p class="summary-value" id="activeTemplates">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>This Month</h4>
                            <p class="summary-value" id="thisMonth">0</p>
                        </div>
                    </div>

                    <div class="recent-certificates">
                        <h3>Recent Certificates</h3>
                        <div class="recent-table-wrapper">
                            <table class="recent-table">
                                <thead>
                                    <tr>
                                        <th>Participant</th>
                                        <th>Training/Event</th>
                                        <th>Certificate ID</th>
                                        <th>Issued Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recentCertsBody">
                                    <!-- Populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Template Management -->
                <div class="tab-content" id="templates">
                    <div class="template-controls">
                        <button class="btn-secondary" id="uploadTemplateBtn" type="button">+ Upload Template</button>
                    </div>

                    <div class="templates-grid" id="templatesGrid">
                        <!-- Templates will be displayed here -->
                    </div>
                </div>

                <!-- Tab 3: Generate Certificates -->
                <div class="tab-content" id="generation">
                    <div class="generation-form">
                        <div class="form-group">
                            <label for="genEvent">Select Training/Event *</label>
                            <select id="genEvent" required>
                                <option value="">Choose Event</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="genTemplate">Select Template *</label>
                            <select id="genTemplate" required>
                                <option value="">Choose Template</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="genParticipants">Participants Eligible</label>
                            <div class="eligible-info">
                                <p>Total: <span id="eligibleCount">0</span> participants</p>
                                <p style="font-size: 12px; color: #666;">Only participants marked as "Completed" with valid scores will be included.</p>
                            </div>
                        </div>

                        <div class="preview-section" style="margin: 20px 0; padding: 20px; background: #f9f9f9; border-radius: 5px;">
                            <h4>Certificate Preview</h4>
                            <div id="certPreview" style="text-align: center; color: #999;">
                                Select an event and template to see preview
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-primary" id="previewCertBtn">Preview Certificate</button>
                            <button type="button" class="btn-secondary" id="generateBulkBtn">Generate for All Eligible</button>
                        </div>
                    </div>

                    <div class="generation-progress" id="generationProgress" style="display: none; margin-top: 20px;">
                        <h4>Generation Progress</h4>
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        <p id="progressText">0 / 0 generated</p>
                    </div>
                </div>

                <!-- Tab 4: Certificate Issuance -->
                <div class="tab-content" id="issuance">
                    <div class="filter-bar">
                        <select id="filterIssueEvent">
                            <option value="">All Events</option>
                        </select>
                        <select id="filterIssueStatus">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="issued">Issued</option>
                            <option value="revoked">Revoked</option>
                        </select>
                    </div>

                    <div class="issuance-table-wrapper">
                        <table class="issuance-table">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Training/Event</th>
                                    <th>Certificate ID</th>
                                    <th>Generated Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="issuanceTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 5: History & Logs -->
                <div class="tab-content" id="history">
                    <div class="filter-bar">
                        <input type="text" id="searchCertHistory" placeholder="Search by participant or cert ID...">
                        <input type="date" id="historyStartDate">
                        <input type="date" id="historyEndDate">
                        <button class="btn-primary" id="exportLogsBtn">Export Logs</button>
                    </div>

                    <div class="history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Training/Event</th>
                                    <th>Certificate ID</th>
                                    <th>Issuance Date</th>
                                    <th>Issued By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 6: Validation -->
                <div class="tab-content" id="validation">
                    <div class="validation-form">
                        <div class="form-group">
                            <label for="validateCertId">Certificate ID or QR Code *</label>
                            <input type="text" id="validateCertId" placeholder="Enter Certificate ID or scan QR code">
                        </div>

                        <button type="button" class="btn-primary" id="validateBtn">Validate Certificate</button>
                    </div>

                    <div id="validationResult" style="margin-top: 30px; display: none;">
                        <div class="validation-card" id="validationCard"></div>
                    </div>

                    <div class="validation-settings" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 5px;">
                        <h4>Validation Settings</h4>
                        <label>
                            <input type="checkbox" id="enableQRValidation" checked> Enable QR Code Validation
                        </label>
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">Participants can verify their certificates using QR codes</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Template Upload -->
    <div id="templateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Upload Certificate Template</h3>
            <form id="templateForm">
                <div class="form-group">
                    <label for="templateName">Template Name *</label>
                    <input type="text" id="templateName" name="name" required placeholder="e.g., Standard Completion Certificate">
                </div>

                <div class="form-group">
                    <label for="templateFile">Template File (PNG, JPG, PDF) *</label>
                    <div class="file-upload-area" id="templateUploadArea">
                        <input type="file" id="templateFile" name="file" accept=".png,.jpg,.jpeg,.pdf" required>
                        <p>Click or drag to upload</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="templateType">Training Type *</label>
                    <select id="templateType" name="type" required>
                        <option value="">Select Type</option>
                        <option value="training">Training Module</option>
                        <option value="simulation">Simulation Event</option>
                        <option value="both">Both</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="active" checked> Set as Active
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Upload Template</button>
                    <button type="button" class="btn-secondary" id="closeTemplateBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Issuing Certificates -->
    <div id="issueModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Issue Certificates</h3>
            <form id="issueForm">
                <div class="form-group">
                    <label for="issueMethod">Delivery Method *</label>
                    <select id="issueMethod" name="method" required>
                        <option value="">Select Method</option>
                        <option value="email">Email to Participants</option>
                        <option value="download">Download PDF</option>
                        <option value="batch">Batch Download All</option>
                    </select>
                </div>

                <div class="form-group" id="emailNote" style="display: none; padding: 15px; background: #e3f2fd; border-radius: 5px;">
                    <p style="margin: 0; font-size: 13px;">Certificates will be sent to registered email addresses of eligible participants.</p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Issue Certificates</button>
                    <button type="button" class="btn-secondary" id="closeIssueBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    <script src="js/admin-certificate-issuance.js"></script>
</body>
</html>
