class InventoryManager {
    constructor() {
        this.resources = [];
        this.categories = [];
        this.assignments = [];
        this.maintenance = [];
        this.events = [];
        this.currentResourceId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadData();
    }

    setupEventListeners() {
        // Tab Navigation
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchTab(e.target.dataset.tab);
            });
        });

        // Inventory Dashboard
        document.getElementById('searchResources').addEventListener('input', () => this.filterResources());
        document.getElementById('filterCategory').addEventListener('change', () => this.filterResources());
        document.getElementById('filterStatus').addEventListener('change', () => this.filterResources());
        document.getElementById('filterCondition').addEventListener('change', () => this.filterResources());
        document.getElementById('filterLocation').addEventListener('change', () => this.filterResources());

        // Add Resource
        document.getElementById('addResourceBtn').addEventListener('click', () => {
            this.resetResourceForm();
            this.switchTab('add-resource');
        });
        document.getElementById('resourceForm').addEventListener('submit', (e) => this.saveResource(e));
        document.getElementById('cancelResourceBtn').addEventListener('click', () => this.switchTab('inventory-dashboard'));

        // Resource Assignments
        document.getElementById('filterAssignmentEvent').addEventListener('change', () => this.loadAssignments());
        document.getElementById('assignResourceBtn').addEventListener('click', () => this.openAssignmentModal());

        // Maintenance
        document.getElementById('searchMaintenance').addEventListener('input', () => this.filterMaintenance());
        document.getElementById('scheduleMaintenance').addEventListener('click', () => this.openMaintenanceModal());

        // Reports
        document.getElementById('reportCategory').addEventListener('change', () => this.loadReports());
        document.getElementById('exportReportBtn').addEventListener('click', () => this.exportReport());

        // Categories
        document.getElementById('addCategoryBtn').addEventListener('click', () => this.openCategoryModal());

        // File Upload
        const imageUploadArea = document.getElementById('imageUploadArea');
        if (imageUploadArea) {
            imageUploadArea.addEventListener('click', () => {
                imageUploadArea.querySelector('input').click();
            });
            imageUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageUploadArea.style.background = '#e3f2fd';
            });
            imageUploadArea.addEventListener('dragleave', () => {
                imageUploadArea.style.background = '';
            });
            imageUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                imageUploadArea.style.background = '';
            });
        }

        // Modal Close
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const modal = e.target.closest('.modal');
                if (modal) modal.style.display = 'none';
            });
        });

        // Modal Forms
        document.getElementById('assignmentForm').addEventListener('submit', (e) => this.saveAssignment(e));
        document.getElementById('closeAssignmentBtn').addEventListener('click', () => this.closeAssignmentModal());
        document.getElementById('maintenanceForm').addEventListener('submit', (e) => this.saveMaintenance(e));
        document.getElementById('closeMaintenanceBtn').addEventListener('click', () => this.closeMaintenanceModal());
        document.getElementById('categoryForm').addEventListener('submit', (e) => this.saveCategory(e));
        document.getElementById('closeCategoryBtn').addEventListener('click', () => this.closeCategoryModal());
    }

    switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

        const tabElement = document.getElementById(tabName);
        if (tabElement) {
            tabElement.classList.add('active');
        }

        const btnElement = document.querySelector(`[data-tab="${tabName}"]`);
        if (btnElement) {
            btnElement.classList.add('active');
        }

        if (tabName === 'inventory-dashboard') {
            this.loadDashboard();
        } else if (tabName === 'assignments') {
            this.loadAssignments();
        } else if (tabName === 'maintenance') {
            this.loadMaintenance();
        } else if (tabName === 'reports') {
            this.loadReports();
        } else if (tabName === 'categories') {
            this.loadCategories();
        }
    }

    loadData() {
        this.categories = [
            { id: 1, name: 'PPE', description: 'Personal Protective Equipment' },
            { id: 2, name: 'Fire Equipment', description: 'Fire Safety Tools' },
            { id: 3, name: 'Medical', description: 'Medical Kits & Supplies' },
            { id: 4, name: 'Communication', description: 'Communication Devices' },
            { id: 5, name: 'Vehicles', description: 'Emergency Vehicles' }
        ];

        this.resources = [
            { id: 1, name: 'Fire Extinguisher', category: 1, quantity: 15, serial: 'FE-001', condition: 'good', status: 'available', location: 'warehouse' },
            { id: 2, name: 'Safety Helmet', category: 1, quantity: 50, serial: 'SH-001', condition: 'good', status: 'available', location: 'warehouse' },
            { id: 3, name: 'First Aid Kit', category: 3, quantity: 8, serial: 'FK-001', condition: 'needs-repair', status: 'maintenance', location: 'office' },
            { id: 4, name: 'Ambulance', category: 5, quantity: 2, serial: 'AMB-001', condition: 'good', status: 'in-use', location: 'vehicle' }
        ];

        this.events = [
            { id: 1, name: 'Earthquake Evacuation Drill' },
            { id: 2, name: 'Fire Safety Exercise' }
        ];

        this.loadCategorySelects();
        this.loadDashboard();
    }

    loadCategorySelects() {
        const selects = ['resourceCategory', 'filterCategory', 'reportCategory', 'maintResource', 'assignResource'];
        
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select && selectId !== 'maintResource' && selectId !== 'assignResource') {
                select.innerHTML = '<option value="">Select Category</option>';
                this.categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    select.appendChild(option);
                });
            }
        });

        const assignResource = document.getElementById('assignResource');
        if (assignResource) {
            assignResource.innerHTML = '<option value="">Select Resource</option>';
            this.resources.forEach(res => {
                const option = document.createElement('option');
                option.value = res.id;
                option.textContent = `${res.name} (${res.quantity} available)`;
                assignResource.appendChild(option);
            });
        }

        const maintResource = document.getElementById('maintResource');
        if (maintResource) {
            maintResource.innerHTML = '<option value="">Select Resource</option>';
            this.resources.forEach(res => {
                const option = document.createElement('option');
                option.value = res.id;
                option.textContent = res.name;
                maintResource.appendChild(option);
            });
        }

        const assignEvent = document.getElementById('assignEvent');
        if (assignEvent) {
            assignEvent.innerHTML = '<option value="">Select Event</option>';
            this.events.forEach(evt => {
                const option = document.createElement('option');
                option.value = evt.id;
                option.textContent = evt.name;
                assignEvent.appendChild(option);
            });
        }

        const filterEvent = document.getElementById('filterAssignmentEvent');
        if (filterEvent) {
            filterEvent.innerHTML = '<option value="">Select Event</option>';
            this.events.forEach(evt => {
                const option = document.createElement('option');
                option.value = evt.id;
                option.textContent = evt.name;
                filterEvent.appendChild(option);
            });
        }
    }

    loadDashboard() {
        const totalResources = this.resources.length;
        const available = this.resources.filter(r => r.status === 'available').length;
        const inUse = this.resources.filter(r => r.status === 'in-use').length;
        const needsRepair = this.resources.filter(r => r.condition === 'needs-repair').length;

        document.getElementById('totalResources').textContent = totalResources;
        document.getElementById('availableResources').textContent = available;
        document.getElementById('inUseResources').textContent = inUse;
        document.getElementById('needsRepairResources').textContent = needsRepair;

        this.displayInventory(this.resources);
    }

    displayInventory(resources) {
        const tbody = document.getElementById('inventoryTableBody');
        tbody.innerHTML = '';

        if (resources.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No resources found.</td></tr>';
            return;
        }

        resources.forEach(resource => {
            const category = this.categories.find(c => c.id === resource.category)?.name || 'N/A';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${resource.name}</strong></td>
                <td>${category}</td>
                <td>${resource.quantity}</td>
                <td>${resource.serial || 'N/A'}</td>
                <td><span class="status-badge condition-${resource.condition}">${this.capitalize(resource.condition)}</span></td>
                <td><span class="status-badge status-${resource.status}">${this.capitalize(resource.status)}</span></td>
                <td>${this.capitalize(resource.location)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" type="button" onclick="invManager.editResource(${resource.id})">Edit</button>
                        <button class="btn-assign" type="button" onclick="invManager.assignResourceToEvent(${resource.id})">Assign</button>
                        <button class="btn-delete" type="button" onclick="invManager.deleteResource(${resource.id})">Delete</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    editResource(resourceId) {
        const resource = this.resources.find(r => r.id === resourceId);
        if (resource) {
            this.currentResourceId = resourceId;
            document.getElementById('resourceName').value = resource.name;
            document.getElementById('resourceCategory').value = resource.category;
            document.getElementById('quantity').value = resource.quantity;
            document.getElementById('serialNumber').value = resource.serial;
            document.getElementById('condition').value = resource.condition;
            document.getElementById('status').value = resource.status;
            document.getElementById('location').value = resource.location;
            
            this.switchTab('add-resource');
        }
    }

    saveResource(e) {
        e.preventDefault();

        const name = document.getElementById('resourceName').value.trim();
        const category = parseInt(document.getElementById('resourceCategory').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        const serial = document.getElementById('serialNumber').value.trim();
        const condition = document.getElementById('condition').value;
        const status = document.getElementById('status').value;
        const location = document.getElementById('location').value;

        if (!name || !category || !quantity) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const resourceData = {
            id: this.currentResourceId || Math.max(...this.resources.map(r => r.id), 0) + 1,
            name, category, quantity, serial, condition, status, location
        };

        if (this.currentResourceId) {
            const index = this.resources.findIndex(r => r.id === this.currentResourceId);
            this.resources[index] = resourceData;
        } else {
            this.resources.push(resourceData);
        }

        Swal.fire({
            title: 'Success!',
            text: 'Resource saved successfully.',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.resetResourceForm();
            this.loadDashboard();
            this.switchTab('inventory-dashboard');
        });
    }

    resetResourceForm() {
        document.getElementById('resourceForm').reset();
        this.currentResourceId = null;
    }

    deleteResource(resourceId) {
        Swal.fire({
            title: 'Delete Resource?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.resources = this.resources.filter(r => r.id !== resourceId);
                this.loadDashboard();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Resource has been deleted.',
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    }

    filterResources() {
        const search = document.getElementById('searchResources').value.toLowerCase();
        const category = document.getElementById('filterCategory').value;
        const status = document.getElementById('filterStatus').value;
        const condition = document.getElementById('filterCondition').value;
        const location = document.getElementById('filterLocation').value;

        const filtered = this.resources.filter(r => {
            return (
                r.name.toLowerCase().includes(search) &&
                (!category || r.category == category) &&
                (!status || r.status === status) &&
                (!condition || r.condition === condition) &&
                (!location || r.location === location)
            );
        });

        this.displayInventory(filtered);
    }

    openAssignmentModal() {
        document.getElementById('assignmentModal').style.display = 'block';
    }

    closeAssignmentModal() {
        document.getElementById('assignmentModal').style.display = 'none';
        document.getElementById('assignmentForm').reset();
    }

    saveAssignment(e) {
        e.preventDefault();

        const resourceId = parseInt(document.getElementById('assignResource').value);
        const eventId = parseInt(document.getElementById('assignEvent').value);
        const quantity = parseInt(document.getElementById('assignQuantity').value);
        const handler = document.getElementById('assignHandler').value.trim();

        if (!resourceId || !eventId || !quantity || !handler) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const assignment = {
            id: Date.now(),
            resourceId,
            eventId,
            quantity,
            handler,
            assignmentDate: new Date().toLocaleDateString(),
            returnStatus: 'pending'
        };

        this.assignments.push(assignment);
        
        // Update resource status
        const resource = this.resources.find(r => r.id === resourceId);
        if (resource) {
            resource.status = 'in-use';
            resource.quantity -= quantity;
        }

        Swal.fire({
            title: 'Success!',
            text: 'Resource assigned successfully.',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.closeAssignmentModal();
            this.loadAssignments();
        });
    }

    loadAssignments() {
        const eventId = document.getElementById('filterAssignmentEvent')?.value;
        let filtered = this.assignments;

        if (eventId) {
            filtered = filtered.filter(a => a.eventId == eventId);
        }

        const tbody = document.getElementById('assignmentsTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No assignments found.</td></tr>';
            return;
        }

        filtered.forEach(assignment => {
            const resource = this.resources.find(r => r.id === assignment.resourceId);
            const event = this.events.find(e => e.id === assignment.eventId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${resource?.name || 'N/A'}</td>
                <td>${event?.name || 'N/A'}</td>
                <td>${assignment.quantity}</td>
                <td>${assignment.handler}</td>
                <td>${assignment.assignmentDate}</td>
                <td><span class="status-badge status-${assignment.returnStatus}">${this.capitalize(assignment.returnStatus)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-return" type="button" onclick="invManager.returnResource(${assignment.id})">Return</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    assignResourceToEvent(resourceId) {
        document.getElementById('assignResource').value = resourceId;
        this.openAssignmentModal();
    }

    returnResource(assignmentId) {
        const assignment = this.assignments.find(a => a.id === assignmentId);
        if (assignment) {
            assignment.returnStatus = 'returned';
            
            // Update resource status back to available
            const resource = this.resources.find(r => r.id === assignment.resourceId);
            if (resource) {
                resource.status = 'available';
                resource.quantity += assignment.quantity;
            }

            Swal.fire({
                title: 'Resource Returned',
                text: 'Resource has been returned to inventory.',
                icon: 'success',
                confirmButtonColor: '#0066cc'
            }).then(() => {
                this.loadAssignments();
                this.loadDashboard();
            });
        }
    }

    openMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'block';
    }

    closeMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'none';
        document.getElementById('maintenanceForm').reset();
    }

    saveMaintenance(e) {
        e.preventDefault();

        const resourceId = parseInt(document.getElementById('maintResource').value);
        const date = document.getElementById('maintDate').value;
        const technician = document.getElementById('maintTechnician').value.trim();
        const remarks = document.getElementById('maintRemarks').value.trim();

        if (!resourceId || !date || !technician) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const maintenance = {
            id: Date.now(),
            resourceId,
            date,
            technician,
            remarks,
            status: 'scheduled'
        };

        this.maintenance.push(maintenance);

        Swal.fire({
            title: 'Success!',
            text: 'Maintenance scheduled successfully.',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.closeMaintenanceModal();
            this.loadMaintenance();
        });
    }

    loadMaintenance() {
        this.displayMaintenance(this.maintenance);
    }

    displayMaintenance(maintenanceList) {
        const tbody = document.getElementById('maintenanceTableBody');
        tbody.innerHTML = '';

        if (maintenanceList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No maintenance records found.</td></tr>';
            return;
        }

        maintenanceList.forEach(maint => {
            const resource = this.resources.find(r => r.id === maint.resourceId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${resource?.name || 'N/A'}</td>
                <td>-</td>
                <td>${maint.date}</td>
                <td>${maint.technician}</td>
                <td>${maint.remarks}</td>
                <td><span class="status-badge status-${maint.status}">${this.capitalize(maint.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-delete" type="button" onclick="invManager.deleteMaintenance(${maint.id})">Delete</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    filterMaintenance() {
        const search = document.getElementById('searchMaintenance').value.toLowerCase();
        const filtered = this.maintenance.filter(m => {
            const resource = this.resources.find(r => r.id === m.resourceId);
            return resource?.name.toLowerCase().includes(search);
        });

        this.displayMaintenance(filtered);
    }

    deleteMaintenance(maintenanceId) {
        this.maintenance = this.maintenance.filter(m => m.id !== maintenanceId);
        this.loadMaintenance();
    }

    loadReports() {
        const damagedCount = this.resources.filter(r => r.condition === 'damaged').length;
        const totalValue = this.resources.length * 5000; // Mock calculation

        document.getElementById('damagedCount').textContent = damagedCount;
        document.getElementById('totalValue').textContent = '₱' + totalValue.toLocaleString();
        document.getElementById('mostUsed').textContent = 'Fire Extinguisher';
        document.getElementById('rarelyUsed').textContent = 'Ambulance';

        // Usage by event
        document.getElementById('usageByEvent').innerHTML = `
            <p>Earthquake Drill: 8 resources</p>
            <p>Fire Safety: 5 resources</p>
        `;

        // Status distribution
        const available = this.resources.filter(r => r.status === 'available').length;
        const inUse = this.resources.filter(r => r.status === 'in-use').length;
        document.getElementById('statusDistribution').innerHTML = `
            <p>Available: ${available}</p>
            <p>In Use: ${inUse}</p>
        `;
    }

    exportReport() {
        Swal.fire({
            title: 'Export Report',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select format:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button type="button" onclick="invManager.performExport('pdf')" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📄 PDF</button>
                        <button type="button" onclick="invManager.performExport('csv')" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
                    </div>
                </div>
            `,
            icon: 'question',
            showConfirmButton: false
        });
    }

    performExport(format) {
        Swal.fire({
            title: 'Exporting...',
            html: `<p>Preparing ${format.toUpperCase()} file...</p>`,
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        setTimeout(() => {
            Swal.fire({
                title: 'Export Complete!',
                text: `Report exported as ${format.toUpperCase()}.`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    openCategoryModal() {
        document.getElementById('categoryModal').style.display = 'block';
    }

    closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
        document.getElementById('categoryForm').reset();
    }

    saveCategory(e) {
        e.preventDefault();

        const name = document.getElementById('categoryName').value.trim();
        const description = document.getElementById('categoryDescription').value.trim();

        if (!name) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please enter category name.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const category = {
            id: Math.max(...this.categories.map(c => c.id), 0) + 1,
            name,
            description
        };

        this.categories.push(category);

        Swal.fire({
            title: 'Success!',
            text: 'Category added successfully.',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.closeCategoryModal();
            this.loadCategories();
            this.loadCategorySelects();
        });
    }

    loadCategories() {
        const container = document.getElementById('categoriesList');
        container.innerHTML = '';

        this.categories.forEach(category => {
            const item = document.createElement('div');
            item.className = 'category-item';
            item.innerHTML = `
                <h5>${category.name}</h5>
                <p>${category.description}</p>
                <div class="category-actions">
                    <button type="button" class="btn-edit" onclick="invManager.editCategory(${category.id})">Edit</button>
                    <button type="button" class="btn-delete" onclick="invManager.deleteCategory(${category.id})">Delete</button>
                </div>
            `;
            container.appendChild(item);
        });
    }

    editCategory(categoryId) {
        const category = this.categories.find(c => c.id === categoryId);
        if (category) {
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryDescription').value = category.description;
            this.openCategoryModal();
        }
    }

    deleteCategory(categoryId) {
        const hasResources = this.resources.some(r => r.category === categoryId);
        if (hasResources) {
            Swal.fire({
                title: 'Cannot Delete',
                text: 'This category has linked resources.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        this.categories = this.categories.filter(c => c.id !== categoryId);
        this.loadCategories();
    }

    capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ');
    }
}

// Initialize on page load
let invManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        invManager = new InventoryManager();
    });
} else {
    invManager = new InventoryManager();
}
