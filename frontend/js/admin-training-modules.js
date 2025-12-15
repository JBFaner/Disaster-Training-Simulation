class TrainingModuleManager {
    constructor() {
        this.modules = [];
        this.lessons = [];
        this.currentModuleId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadModules();
        this.setupTabNavigation();
    }

    setupEventListeners() {
        // Tab Navigation
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Module Creation
        document.getElementById('createModuleBtn').addEventListener('click', () => {
            this.resetForm();
            this.switchTab('create-edit');
        });

        // Module Form
        document.getElementById('moduleForm').addEventListener('submit', (e) => this.saveModule(e));
        document.getElementById('cancelBtn').addEventListener('click', () => {
            Swal.fire({
                title: 'Cancel Module Creation?',
                text: 'Any unsaved changes will be lost.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel',
                cancelButtonText: 'Keep editing'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.resetForm();
                    this.switchTab('modules-list');
                }
            });
        });

        // Lessons
        document.getElementById('addLessonBtn').addEventListener('click', () => this.openLessonModal());
        document.getElementById('lessonForm').addEventListener('submit', (e) => this.saveLesson(e));
        document.getElementById('closeLesson').addEventListener('click', () => this.closeLessonModal());

        // File Upload
        document.getElementById('materialUpload').addEventListener('click', () => {
            document.getElementById('instructionalMaterials').click();
        });

        document.getElementById('instructionalMaterials').addEventListener('change', (e) => {
            this.handleFileUpload(e, 'materials');
        });

        // Filters
        document.getElementById('searchModules').addEventListener('input', () => this.filterModules());
        document.getElementById('filterCategory').addEventListener('change', () => this.filterModules());
        document.getElementById('filterStatus').addEventListener('change', () => this.filterModules());

        // Visibility Control
        document.getElementById('visibility').addEventListener('change', (e) => {
            const specificGroup = document.getElementById('specificGroupsSelect');
            specificGroup.style.display = e.target.value === 'specific' ? 'block' : 'none';
        });

        // Analytics Export
        document.getElementById('exportAnalyticsBtn').addEventListener('click', () => this.exportAnalytics());

        // Modal Close
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal').style.display = 'none';
            });
        });
    }

    setupTabNavigation() {
        // Initialize
    }

    switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Deactivate all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        const tabElement = document.getElementById(tabName);
        if (tabElement) {
            tabElement.classList.add('active');
        }

        // Activate button
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    }

    loadModules() {
        // Mock data - Replace with API call
        this.modules = [
            {
                id: 1,
                title: 'Earthquake Response Drills',
                category: 'earthquake',
                difficulty: 'intermediate',
                status: 'published',
                participants: 45,
                completionRate: 78,
                createdDate: '2024-01-15',
                lessons: 5
            },
            {
                id: 2,
                title: 'Fire Safety Training',
                category: 'fire',
                difficulty: 'beginner',
                status: 'draft',
                participants: 20,
                completionRate: 50,
                createdDate: '2024-01-20',
                lessons: 3
            }
        ];
        this.displayModules();
    }

    displayModules() {
        const tbody = document.getElementById('modulesTableBody');
        tbody.innerHTML = '';

        this.modules.forEach(module => {
            if (module.status !== 'archived') {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${module.title}</strong></td>
                    <td>${this.capitalize(module.category)}</td>
                    <td><span class="difficulty-badge difficulty-${module.difficulty}">${this.capitalize(module.difficulty)}</span></td>
                    <td><span class="status-badge status-${module.status}">${this.capitalize(module.status)}</span></td>
                    <td>${module.participants}</td>
                    <td>${module.completionRate}%</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" onclick="manager.editModule(${module.id})">Edit</button>
                            <button class="btn-view-versions" onclick="manager.viewVersions(${module.id})">Versions</button>
                            <button class="btn-archive" onclick="manager.archiveModule(${module.id})">Archive</button>
                            <button class="btn-delete" onclick="manager.deleteModule(${module.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }
        });
    }

    editModule(moduleId) {
        const module = this.modules.find(m => m.id === moduleId);
        if (module) {
            this.currentModuleId = moduleId;
            document.getElementById('moduleTitle').value = module.title;
            document.getElementById('moduleDescription').value = module.title;
            document.getElementById('moduleDifficulty').value = module.difficulty;
            document.getElementById('moduleCategory').value = module.category;
            this.switchTab('create-edit');
            
            Swal.fire({
                title: 'Editing Module',
                html: `<strong>${module.title}</strong> is now being edited.`,
                icon: 'info',
                timer: 2000,
                timerProgressBar: true
            });
        }
    }

    saveModule(e) {
        e.preventDefault();

        // Validation
        const title = document.getElementById('moduleTitle').value.trim();
        const description = document.getElementById('moduleDescription').value.trim();
        const difficulty = document.getElementById('moduleDifficulty').value;
        const category = document.getElementById('moduleCategory').value;

        if (!title || !description || !difficulty || !category) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const formData = new FormData(document.getElementById('moduleForm'));
        const moduleData = {
            id: this.currentModuleId || Date.now(),
            title: title,
            description: description,
            difficulty: difficulty,
            category: category,
            status: formData.get('publish_status'),
            visibility: formData.get('visibility'),
            editAccess: formData.get('edit_access'),
            participants: 0,
            completionRate: 0,
            lessons: this.lessons.length
        };

        if (this.currentModuleId) {
            const index = this.modules.findIndex(m => m.id === this.currentModuleId);
            this.modules[index] = { ...this.modules[index], ...moduleData };
        } else {
            this.modules.push(moduleData);
        }

        Swal.fire({
            title: 'Success!',
            text: this.currentModuleId ? 'Module updated successfully!' : 'Module created successfully!',
            icon: 'success',
            confirmButtonColor: '#0066cc',
            confirmButtonText: 'OK'
        }).then(() => {
            this.displayModules();
            this.resetForm();
            this.switchTab('modules-list');
        });
    }

    openLessonModal() {
        document.getElementById('lessonModal').style.display = 'block';
    }

    closeLessonModal() {
        document.getElementById('lessonModal').style.display = 'none';
        document.getElementById('lessonForm').reset();
    }

    saveLesson(e) {
        e.preventDefault();

        const title = document.getElementById('lessonTitle').value.trim();
        const description = document.getElementById('lessonDescription').value.trim();

        if (!title || !description) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in lesson title and description.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const formData = new FormData(document.getElementById('lessonForm'));
        const lesson = {
            id: Date.now(),
            title: title,
            description: description,
            quiz: formData.get('quiz')
        };

        this.lessons.push(lesson);
        this.displayLessons();
        
        Swal.fire({
            title: 'Success!',
            text: 'Lesson added successfully!',
            icon: 'success',
            timer: 1500,
            timerProgressBar: true
        });

        this.closeLessonModal();
    }

    displayLessons() {
        const container = document.getElementById('lessonsContainer');
        container.innerHTML = '';

        this.lessons.forEach((lesson, index) => {
            const lessonDiv = document.createElement('div');
            lessonDiv.className = 'lesson-item';
            lessonDiv.innerHTML = `
                <div class="lesson-item-info">
                    <h4>${index + 1}. ${lesson.title}</h4>
                    <p>${lesson.description}</p>
                </div>
                <div class="lesson-item-actions">
                    <button class="btn-edit-lesson" onclick="manager.editLesson(${index})">Edit</button>
                    <button class="btn-delete-lesson" onclick="manager.deleteLesson(${index})">Delete</button>
                </div>
            `;
            container.appendChild(lessonDiv);
        });
    }

    deleteLesson(index) {
        Swal.fire({
            title: 'Delete Lesson?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.lessons.splice(index, 1);
                this.displayLessons();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Lesson has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    }

    handleFileUpload(e, type) {
        const files = e.target.files;
        if (files.length > 0) {
            Swal.fire({
                title: 'Uploading Files',
                html: `<p>Uploading ${files.length} file(s)...</p>`,
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulate upload delay
            setTimeout(() => {
                Swal.fire({
                    title: 'Upload Complete!',
                    text: `${files.length} file(s) uploaded successfully.`,
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }, 2000);
        }
    }

    filterModules() {
        const search = document.getElementById('searchModules').value.toLowerCase();
        const category = document.getElementById('filterCategory').value;
        const status = document.getElementById('filterStatus').value;

        const filtered = this.modules.filter(module => {
            return (
                module.title.toLowerCase().includes(search) &&
                (category === '' || module.category === category) &&
                (status === '' || module.status === status) &&
                module.status !== 'archived'
            );
        });

        // Display filtered modules
        const tbody = document.getElementById('modulesTableBody');
        tbody.innerHTML = '';

        filtered.forEach(module => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${module.title}</strong></td>
                <td>${this.capitalize(module.category)}</td>
                <td><span class="difficulty-badge difficulty-${module.difficulty}">${this.capitalize(module.difficulty)}</span></td>
                <td><span class="status-badge status-${module.status}">${this.capitalize(module.status)}</span></td>
                <td>${module.participants}</td>
                <td>${module.completionRate}%</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="manager.editModule(${module.id})">Edit</button>
                        <button class="btn-view-versions" onclick="manager.viewVersions(${module.id})">Versions</button>
                        <button class="btn-archive" onclick="manager.archiveModule(${module.id})">Archive</button>
                        <button class="btn-delete" onclick="manager.deleteModule(${module.id})">Delete</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    archiveModule(moduleId) {
        const module = this.modules.find(m => m.id === moduleId);
        if (module) {
            Swal.fire({
                title: 'Archive Module?',
                html: `<p>Are you sure you want to archive <strong>${module.title}</strong>?</p><p style="font-size: 12px; color: #666;">It will be hidden from participants but can be restored later.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, archive it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    module.status = 'archived';
                    this.displayModules();
                    
                    Swal.fire({
                        title: 'Archived!',
                        text: `${module.title} has been archived successfully.`,
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    deleteModule(moduleId) {
        const module = this.modules.find(m => m.id === moduleId);
        if (module) {
            Swal.fire({
                title: 'Delete Module?',
                html: `<p>Are you sure you want to permanently delete <strong>${module.title}</strong>?</p><p style="font-size: 12px; color: #d32f2f;">This action cannot be undone!</p>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete permanently',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.modules = this.modules.filter(m => m.id !== moduleId);
                    this.displayModules();
                    
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Module has been deleted permanently.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    viewVersions(moduleId) {
        const module = this.modules.find(m => m.id === moduleId);
        
        Swal.fire({
            title: 'Version History',
            html: `
                <div style="text-align: left;">
                    <div style="padding: 10px; margin-bottom: 10px; border-left: 3px solid #0066cc; background: #f0f7ff;">
                        <strong>Version 2</strong> - 2024-01-25
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">Updated lesson content and assessment criteria</p>
                        <button onclick="alert('Restoring version 2...')" style="padding: 5px 10px; background: #0066cc; color: white; border: none; border-radius: 3px; cursor: pointer;">Restore</button>
                    </div>
                    <div style="padding: 10px; border-left: 3px solid #6c757d; background: #f9f9f9;">
                        <strong>Version 1</strong> - 2024-01-20
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">Initial module creation</p>
                        <button onclick="alert('Restoring version 1...')" style="padding: 5px 10px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer;">Restore</button>
                    </div>
                </div>
            `,
            icon: 'info',
            confirmButtonColor: '#0066cc',
            confirmButtonText: 'Close'
        });
    }

    exportAnalytics() {
        Swal.fire({
            title: 'Export Analytics',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select export format:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button onclick="manager.performExport('csv')" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
                        <button onclick="manager.performExport('pdf')" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📄 PDF</button>
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
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            Swal.fire({
                title: 'Export Complete!',
                text: `Analytics exported as ${format.toUpperCase()} successfully.`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    resetForm() {
        document.getElementById('moduleForm').reset();
        this.lessons = [];
        this.currentModuleId = null;
        this.displayLessons();
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).replace('-', ' ');
    }

    editLesson(index) {
        const lesson = this.lessons[index];
        document.getElementById('lessonTitle').value = lesson.title;
        document.getElementById('lessonDescription').value = lesson.description;
        this.openLessonModal();
    }
}

// Initialize on page load
const manager = new TrainingModuleManager();
