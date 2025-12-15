<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource & Equipment Inventory - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin-inventory.css">
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
                    <li><a href="admin-inventory.php" class="menu-item active">Inventory</a></li>
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
                    <h1>Resource & Equipment Inventory</h1>
                    <button class="btn-primary" id="addResourceBtn">+ Add New Resource</button>
                </div>

                <!-- Tabs Navigation -->
                <div class="admin-tabs">
                    <button class="tab-btn active" data-tab="inventory-dashboard">Inventory Dashboard</button>
                    <button class="tab-btn" data-tab="add-resource">Add/Edit Resource</button>
                    <button class="tab-btn" data-tab="assignments">Resource Assignments</button>
                    <button class="tab-btn" data-tab="maintenance">Maintenance Tracking</button>
                    <button class="tab-btn" data-tab="reports">Reports & Analytics</button>
                    <button class="tab-btn" data-tab="categories">Categories</button>
                </div>

                <!-- Tab 1: Inventory Dashboard -->
                <div class="tab-content active" id="inventory-dashboard">
                    <div class="inventory-summary">
                        <div class="summary-card">
                            <h4>Total Resources</h4>
                            <p class="summary-value" id="totalResources">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>Available</h4>
                            <p class="summary-value" id="availableResources">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>In Use</h4>
                            <p class="summary-value" id="inUseResources">0</p>
                        </div>
                        <div class="summary-card">
                            <h4>Needs Repair</h4>
                            <p class="summary-value" id="needsRepairResources">0</p>
                        </div>
                    </div>

                    <div class="filter-bar">
                        <input type="text" id="searchResources" placeholder="Search by name or ID...">
                        <select id="filterCategory">
                            <option value="">All Categories</option>
                        </select>
                        <select id="filterStatus">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="in-use">In Use</option>
                            <option value="maintenance">Under Maintenance</option>
                            <option value="damaged">Damaged</option>
                            <option value="missing">Missing</option>
                        </select>
                        <select id="filterCondition">
                            <option value="">All Conditions</option>
                            <option value="new">New</option>
                            <option value="good">Good</option>
                            <option value="needs-repair">Needs Repair</option>
                            <option value="damaged">Damaged</option>
                        </select>
                        <select id="filterLocation">
                            <option value="">All Locations</option>
                            <option value="warehouse">Warehouse</option>
                            <option value="depot">Depot</option>
                            <option value="office">Office</option>
                            <option value="vehicle">Vehicle Storage</option>
                        </select>
                    </div>

                    <div class="inventory-table-wrapper">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Resource Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Serial #</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Add/Edit Resource -->
                <div class="tab-content" id="add-resource">
                    <form id="resourceForm" class="resource-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="resourceName">Resource Name *</label>
                                <input type="text" id="resourceName" name="name" required placeholder="e.g., Fire Extinguisher">
                            </div>
                            <div class="form-group">
                                <label for="resourceCategory">Category *</label>
                                <select id="resourceCategory" name="category" required>
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="resourceDescription">Description</label>
                            <textarea id="resourceDescription" name="description" placeholder="Resource details" rows="3"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="quantity">Quantity *</label>
                                <input type="number" id="quantity" name="quantity" min="1" required value="1">
                            </div>
                            <div class="form-group">
                                <label for="serialNumber">Serial/Tag Number</label>
                                <input type="text" id="serialNumber" name="serial_number" placeholder="e.g., SN-001">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="condition">Condition *</label>
                                <select id="condition" name="condition" required>
                                    <option value="new">New</option>
                                    <option value="good">Good</option>
                                    <option value="needs-repair">Needs Repair</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select id="status" name="status" required>
                                    <option value="available">Available</option>
                                    <option value="in-use">In Use</option>
                                    <option value="maintenance">Under Maintenance</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="missing">Missing</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location">Storage Location *</label>
                            <select id="location" name="location" required>
                                <option value="warehouse">Warehouse</option>
                                <option value="depot">Depot</option>
                                <option value="office">Office</option>
                                <option value="vehicle">Vehicle Storage</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="resourceImage">Upload Resource Photo</label>
                            <div class="file-upload-area" id="imageUploadArea">
                                <input type="file" id="resourceImage" name="image" accept="image/*">
                                <p>Click or drag to upload photo</p>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Save Resource</button>
                            <button type="reset" class="btn-secondary">Reset</button>
                            <button type="button" class="btn-danger" id="cancelResourceBtn">Cancel</button>
                        </div>
                    </form>
                </div>

                <!-- Tab 3: Resource Assignments -->
                <div class="tab-content" id="assignments">
                    <div class="filter-bar">
                        <select id="filterAssignmentEvent">
                            <option value="">Select Event</option>
                        </select>
                        <button class="btn-secondary" id="assignResourceBtn" type="button">+ Assign Resource</button>
                    </div>

                    <div class="assignments-table-wrapper">
                        <table class="assignments-table">
                            <thead>
                                <tr>
                                    <th>Resource Name</th>
                                    <th>Event</th>
                                    <th>Quantity Assigned</th>
                                    <th>Handler</th>
                                    <th>Assignment Date</th>
                                    <th>Return Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="assignmentsTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 4: Maintenance Tracking -->
                <div class="tab-content" id="maintenance">
                    <div class="filter-bar">
                        <input type="text" id="searchMaintenance" placeholder="Search resource...">
                        <button class="btn-secondary" id="scheduleMaintenance" type="button">+ Schedule Maintenance</button>
                    </div>

                    <div class="maintenance-table-wrapper">
                        <table class="maintenance-table">
                            <thead>
                                <tr>
                                    <th>Resource Name</th>
                                    <th>Last Maintenance</th>
                                    <th>Next Scheduled</th>
                                    <th>Technician</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="maintenanceTableBody">
                                <!-- Populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 5: Reports & Analytics -->
                <div class="tab-content" id="reports">
                    <div class="reports-filters">
                        <select id="reportCategory">
                            <option value="">All Categories</option>
                        </select>
                        <input type="date" id="reportStartDate">
                        <input type="date" id="reportEndDate">
                        <button class="btn-primary" id="exportReportBtn">Export Report</button>
                    </div>

                    <div class="analytics-grid">
                        <div class="analytics-card">
                            <h4>Total Inventory Value</h4>
                            <p class="analytics-value" id="totalValue">₱0</p>
                        </div>
                        <div class="analytics-card">
                            <h4>Damaged Items</h4>
                            <p class="analytics-value" id="damagedCount">0</p>
                        </div>
                        <div class="analytics-card">
                            <h4>Most Used Resource</h4>
                            <p class="analytics-value" id="mostUsed">N/A</p>
                        </div>
                        <div class="analytics-card">
                            <h4>Rarely Used</h4>
                            <p class="analytics-value" id="rarelyUsed">N/A</p>
                        </div>
                    </div>

                    <div class="report-breakdown">
                        <div class="breakdown-section">
                            <h4>Resource Usage by Event</h4>
                            <div id="usageByEvent"></div>
                        </div>
                        <div class="breakdown-section">
                            <h4>Resource Status Distribution</h4>
                            <div id="statusDistribution"></div>
                        </div>
                    </div>
                </div>

                <!-- Tab 6: Categories -->
                <div class="tab-content" id="categories">
                    <div class="categories-controls">
                        <button class="btn-secondary" id="addCategoryBtn" type="button">+ Add New Category</button>
                    </div>

                    <div class="categories-list">
                        <div id="categoriesList">
                            <!-- Categories will be listed here -->
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal for Resource Assignment -->
    <div id="assignmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Assign Resource to Event</h3>
            <form id="assignmentForm">
                <div class="form-group">
                    <label for="assignResource">Select Resource *</label>
                    <select id="assignResource" name="resource_id" required>
                        <option value="">Choose Resource</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="assignEvent">Select Event *</label>
                    <select id="assignEvent" name="event_id" required>
                        <option value="">Choose Event</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="assignQuantity">Quantity Needed *</label>
                    <input type="number" id="assignQuantity" name="quantity" min="1" required value="1">
                </div>
                <div class="form-group">
                    <label for="assignHandler">Resource Handler *</label>
                    <input type="text" id="assignHandler" name="handler" required placeholder="Staff name">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Assign Resource</button>
                    <button type="button" class="btn-secondary" id="closeAssignmentBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Maintenance Scheduling -->
    <div id="maintenanceModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Schedule Maintenance</h3>
            <form id="maintenanceForm">
                <div class="form-group">
                    <label for="maintResource">Resource *</label>
                    <select id="maintResource" name="resource_id" required>
                        <option value="">Select Resource</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="maintDate">Maintenance Date *</label>
                    <input type="date" id="maintDate" name="date" required>
                </div>
                <div class="form-group">
                    <label for="maintTechnician">Technician Name *</label>
                    <input type="text" id="maintTechnician" name="technician" required>
                </div>
                <div class="form-group">
                    <label for="maintRemarks">Remarks</label>
                    <textarea id="maintRemarks" name="remarks" placeholder="Maintenance notes" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Schedule</button>
                    <button type="button" class="btn-secondary" id="closeMaintenanceBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Category Management -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add/Edit Category</h3>
            <form id="categoryForm">
                <div class="form-group">
                    <label for="categoryName">Category Name *</label>
                    <input type="text" id="categoryName" name="name" required placeholder="e.g., Personal Protective Equipment">
                </div>
                <div class="form-group">
                    <label for="categoryDescription">Description</label>
                    <textarea id="categoryDescription" name="description" placeholder="Category description" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Category</button>
                    <button type="button" class="btn-secondary" id="closeCategoryBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js"></script>
    <script src="js/admin-inventory.js"></script>
</body>
</html>
