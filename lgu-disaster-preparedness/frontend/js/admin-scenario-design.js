class ScenarioDesignManager {
    constructor() {
        this.scenarios = [];
        this.events = [];
        this.metrics = [];
        this.checklist = [];
        this.currentScenarioId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadScenarios();
        this.loadTrainingModules();
    }

    setupEventListeners() {
        // Tab Navigation
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Scenario Creation
        document.getElementById('createScenarioBtn').addEventListener('click', () => {
            this.resetForm();
            this.switchTab('create-edit');
        });

        // AI Generation
        document.getElementById('generateWithAIBtn').addEventListener('click', () => {
            this.openAIGenerationModal();
        });
        document.getElementById('aiGenerationForm').addEventListener('submit', (e) => this.generateScenarioWithAI(e));
        document.getElementById('cancelAIGeneration').addEventListener('click', () => this.closeAIGenerationModal());
        document.getElementById('closeAIModal').addEventListener('click', () => this.closeAIGenerationModal());

        // Form Submission
        document.getElementById('scenarioForm').addEventListener('submit', (e) => this.saveScenario(e));
        document.getElementById('cancelScenarioBtn').addEventListener('click', () => this.confirmCancel());

        // Timeline Events
        document.getElementById('addEventBtn').addEventListener('click', () => this.openEventModal());
        document.getElementById('eventForm').addEventListener('submit', (e) => this.saveEvent(e));
        document.getElementById('closeEventBtn').addEventListener('click', () => this.closeEventModal());

        // Evaluation
        document.getElementById('addMetricBtn').addEventListener('click', () => this.addMetricRow());
        document.getElementById('addChecklistBtn').addEventListener('click', () => this.addChecklistRow());
        document.getElementById('evaluationForm').addEventListener('submit', (e) => this.saveEvaluation(e));

        // Analytics
        document.getElementById('exportScenarioAnalyticsBtn').addEventListener('click', () => this.exportScenarioAnalytics());

        // Filters
        document.getElementById('searchScenarios').addEventListener('input', () => this.filterScenarios());
        document.getElementById('filterDisasterType').addEventListener('change', () => this.filterScenarios());
        document.getElementById('filterDifficulty').addEventListener('change', () => this.filterScenarios());
        document.getElementById('filterStatus').addEventListener('change', () => this.filterScenarios());

        // File Upload Areas
        document.querySelectorAll('.file-upload-area').forEach(area => {
            area.addEventListener('click', () => {
                area.querySelector('input').click();
            });
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.style.background = '#e3f2fd';
            });
            area.addEventListener('dragleave', () => {
                area.style.background = '';
            });
            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.style.background = '';
                this.handleFileUpload(e, area.querySelector('input'));
            });
        });

        // Modal Close
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal').style.display = 'none';
            });
        });
    }

    switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

        const tabElement = document.getElementById(tabName);
        if (tabElement) {
            tabElement.classList.add('active');
        }

        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    }

    loadScenarios() {
        this.scenarios = [
            {
                id: 1,
                title: 'Downtown Building Earthquake',
                disasterType: 'earthquake',
                difficulty: 'intermediate',
                status: 'published',
                eventsUsed: 12,
                avgScore: 78,
                description: 'High-rise building evacuation during earthquake'
            },
            {
                id: 2,
                title: 'Hospital Fire Evacuation',
                disasterType: 'fire',
                difficulty: 'advanced',
                status: 'published',
                eventsUsed: 8,
                avgScore: 82,
                description: 'Emergency evacuation with patient care'
            }
        ];
        this.displayScenarios();
    }

    loadTrainingModules() {
        const select = document.getElementById('trainingModule');
        select.innerHTML = '<option value="">Select Module</option>';
        
        const modules = ['Earthquake Response', 'Fire Safety', 'Flood Management', 'Typhoon Preparedness'];
        modules.forEach(module => {
            const option = document.createElement('option');
            option.value = module.toLowerCase().replace(/\s+/g, '-');
            option.textContent = module;
            select.appendChild(option);
        });

        const analyticsSelect = document.getElementById('analyticsScenarioFilter');
        analyticsSelect.innerHTML = '<option value="">Select Scenario</option>';
        this.scenarios.forEach(scenario => {
            const option = document.createElement('option');
            option.value = scenario.id;
            option.textContent = scenario.title;
            analyticsSelect.appendChild(option);
        });
    }

    displayScenarios() {
        const tbody = document.getElementById('scenariosTableBody');
        tbody.innerHTML = '';

        this.scenarios.forEach(scenario => {
            if (scenario.status !== 'archived') {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${scenario.title}</strong></td>
                    <td>${this.capitalize(scenario.disasterType)}</td>
                    <td><span class="difficulty-badge difficulty-${scenario.difficulty}">${this.capitalize(scenario.difficulty)}</span></td>
                    <td><span class="status-badge status-${scenario.status}">${this.capitalize(scenario.status)}</span></td>
                    <td>${scenario.eventsUsed}</td>
                    <td>${scenario.avgScore}%</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-edit" onclick="scenarioManager.editScenario(${scenario.id})">Edit</button>
                            <button class="action-btn btn-preview" onclick="scenarioManager.previewScenario(${scenario.id})">Preview</button>
                            <button class="action-btn btn-assign" onclick="scenarioManager.assignScenario(${scenario.id})">Assign</button>
                            <button class="action-btn btn-duplicate" onclick="scenarioManager.duplicateScenario(${scenario.id})">Duplicate</button>
                            <button class="action-btn btn-delete" onclick="scenarioManager.deleteScenario(${scenario.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }
        });
    }

    editScenario(scenarioId) {
        const scenario = this.scenarios.find(s => s.id === scenarioId);
        if (scenario) {
            this.currentScenarioId = scenarioId;
            document.getElementById('scenarioTitle').value = scenario.title;
            document.getElementById('scenarioDescription').value = scenario.description;
            document.getElementById('disasterType').value = scenario.disasterType;
            document.getElementById('difficultyLevel').value = scenario.difficulty;
            
            Swal.fire({
                title: 'Editing Scenario',
                html: `<strong>${scenario.title}</strong> is now being edited.`,
                icon: 'info',
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            this.switchTab('create-edit');
        }
    }

    previewScenario(scenarioId) {
        const scenario = this.scenarios.find(s => s.id === scenarioId);
        if (scenario) {
            Swal.fire({
                title: scenario.title,
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <div style="background: #f0f7ff; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                            <p style="margin: 8px 0;"><strong>📍 Type:</strong> ${this.capitalize(scenario.disasterType)}</p>
                            <p style="margin: 8px 0;"><strong>📊 Difficulty:</strong> ${this.capitalize(scenario.difficulty)}</p>
                            <p style="margin: 8px 0;"><strong>👥 Status:</strong> ${this.capitalize(scenario.status)}</p>
                            <p style="margin: 8px 0;"><strong>📈 Events Used:</strong> ${scenario.eventsUsed}</p>
                            <p style="margin: 8px 0;"><strong>⭐ Average Score:</strong> ${scenario.avgScore}%</p>
                        </div>
                        <p><strong>Description:</strong></p>
                        <p style="color: #555;">${scenario.description}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonColor: '#0066cc',
                confirmButtonText: 'Close'
            });
        }
    }

    assignScenario(scenarioId) {
        const scenario = this.scenarios.find(s => s.id === scenarioId);
        if (scenario) {
            Swal.fire({
                title: 'Assign Scenario to Event',
                html: `
                    <div style="text-align: left;">
                        <p>Assign <strong>${scenario.title}</strong> to a simulation event:</p>
                        <select id="eventSelect" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin: 10px 0;">
                            <option value="">Select Event</option>
                            <option value="event1">Disaster Preparedness Drill - Jan 25, 2024</option>
                            <option value="event2">Emergency Response Training - Feb 10, 2024</option>
                            <option value="event3">Community Safety Workshop - Feb 28, 2024</option>
                        </select>
                        <div style="margin-top: 15px; text-align: left;">
                            <label style="display: flex; align-items: center;">
                                <input type="checkbox" id="setDefault" style="margin-right: 8px;">
                                <span>Set as default scenario for this event</span>
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Assign',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const eventSelect = document.getElementById('eventSelect');
                    if (!eventSelect.value) {
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please select an event.',
                            icon: 'error',
                            confirmButtonColor: '#0066cc'
                        });
                        return;
                    }

                    const isDefault = document.getElementById('setDefault').checked;
                    Swal.fire({
                        title: 'Assigned!',
                        text: `Scenario has been assigned to the event${isDefault ? ' and set as default' : ''}.`,
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    duplicateScenario(scenarioId) {
        const scenario = this.scenarios.find(s => s.id === scenarioId);
        if (scenario) {
            Swal.fire({
                title: 'Duplicate Scenario',
                input: 'text',
                inputLabel: 'New Scenario Name',
                inputValue: `${scenario.title} (Copy)`,
                showCancelButton: true,
                confirmButtonColor: '#0066cc',
                confirmButtonText: 'Create',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please enter a scenario name';
                    }
                    if (value.length < 3) {
                        return 'Scenario name must be at least 3 characters long';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const newScenario = {
                        id: Date.now(),
                        ...scenario,
                        title: result.value,
                        status: 'draft'
                    };
                    this.scenarios.push(newScenario);
                    this.displayScenarios();
                    
                    Swal.fire({
                        title: 'Success!',
                        text: 'Scenario duplicated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    deleteScenario(scenarioId) {
        const scenario = this.scenarios.find(s => s.id === scenarioId);
        if (scenario) {
            Swal.fire({
                title: 'Delete Scenario?',
                html: `<p>Are you sure you want to permanently delete <strong>${scenario.title}</strong>?</p><p style="font-size: 12px; color: #d32f2f;">⚠️ This action cannot be undone!</p>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete permanently',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.scenarios = this.scenarios.filter(s => s.id !== scenarioId);
                    this.displayScenarios();
                    
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Scenario has been deleted permanently.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    saveScenario(e) {
        e.preventDefault();

        const title = document.getElementById('scenarioTitle').value.trim();
        const description = document.getElementById('scenarioDescription').value.trim();
        const disasterType = document.getElementById('disasterType').value;
        const difficulty = document.getElementById('difficultyLevel').value;

        if (!title || !description || !disasterType || !difficulty) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const formData = new FormData(document.getElementById('scenarioForm'));
        const existingScenario = this.currentScenarioId ? this.scenarios.find(s => s.id === this.currentScenarioId) : null;
        
        const scenarioData = {
            id: this.currentScenarioId || Date.now(),
            title: title,
            description: description,
            disasterType: disasterType,
            difficulty: difficulty,
            status: formData.get('publish_status'),
            eventsUsed: existingScenario ? existingScenario.eventsUsed : 0,
            avgScore: existingScenario ? existingScenario.avgScore : 0
        };

        if (this.currentScenarioId) {
            const index = this.scenarios.findIndex(s => s.id === this.currentScenarioId);
            this.scenarios[index] = { ...this.scenarios[index], ...scenarioData };
        } else {
            this.scenarios.push(scenarioData);
        }

        Swal.fire({
            title: 'Success!',
            text: this.currentScenarioId ? 'Scenario updated successfully!' : 'Scenario created successfully!',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.displayScenarios();
            this.resetForm();
            this.switchTab('scenarios-list');
        });
    }

    confirmCancel() {
        Swal.fire({
            title: 'Cancel Scenario Creation?',
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
                this.switchTab('scenarios-list');
            }
        });
    }

    openEventModal() {
        document.getElementById('eventModal').style.display = 'block';
    }

    closeEventModal() {
        document.getElementById('eventModal').style.display = 'none';
        document.getElementById('eventForm').reset();
    }

    saveEvent(e) {
        e.preventDefault();

        const title = document.getElementById('eventTitle').value.trim();
        const triggerTime = parseInt(document.getElementById('triggerTime').value);
        const expectedAction = document.getElementById('expectedAction').value.trim();

        if (!title || isNaN(triggerTime) || !expectedAction) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const event = {
            id: Date.now(),
            title: title,
            triggerTime: triggerTime,
            instructorPrompt: document.getElementById('instructorPrompt').value,
            expectedAction: expectedAction,
            isEscalation: document.getElementById('eventForm').querySelector('input[name="is_escalation"]').checked,
            isEndScenario: document.getElementById('eventForm').querySelector('input[name="is_end_scenario"]').checked
        };

        this.events.push(event);
        this.displayTimelineEvents();

        Swal.fire({
            title: 'Success!',
            text: 'Event added to timeline.',
            icon: 'success',
            timer: 1500,
            timerProgressBar: true
        });

        this.closeEventModal();
    }

    displayTimelineEvents() {
        const container = document.getElementById('timelineEvents');
        container.innerHTML = '';

        if (this.events.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">No events added yet. Click "Add Event Step" to begin.</p>';
            return;
        }

        this.events.sort((a, b) => a.triggerTime - b.triggerTime).forEach((event, index) => {
            const eventDiv = document.createElement('div');
            eventDiv.className = 'timeline-event';
            eventDiv.innerHTML = `
                <div class="timeline-event-header">
                    <div>
                        <h4>${index + 1}. ${event.title}</h4>
                    </div>
                    <span class="timeline-event-trigger">⏱️ ${event.triggerTime}s</span>
                </div>
                <div class="timeline-event-content">
                    <p><strong>Expected Action:</strong> ${event.expectedAction}</p>
                    ${event.instructorPrompt ? `<p><strong>Instructor Prompt:</strong> ${event.instructorPrompt}</p>` : ''}
                    <div style="margin-top: 10px;">
                        ${event.isEscalation ? '<span style="color: #dc3545; font-weight: 600; margin-right: 10px;">⚠️ Escalation Event</span>' : ''}
                        ${event.isEndScenario ? '<span style="color: #17a2b8; font-weight: 600;">🏁 End of Scenario</span>' : ''}
                    </div>
                </div>
                <div class="timeline-event-actions">
                    <button class="btn-edit" onclick="scenarioManager.editEvent(${index})">Edit</button>
                    <button class="btn-delete" onclick="scenarioManager.deleteEvent(${index})">Delete</button>
                </div>
            `;
            container.appendChild(eventDiv);
        });
    }

    editEvent(index) {
        const event = this.events[index];
        document.getElementById('eventTitle').value = event.title;
        document.getElementById('triggerTime').value = event.triggerTime;
        document.getElementById('instructorPrompt').value = event.instructorPrompt;
        document.getElementById('expectedAction').value = event.expectedAction;
        document.querySelector('input[name="is_escalation"]').checked = event.isEscalation;
        document.querySelector('input[name="is_end_scenario"]').checked = event.isEndScenario;

        Swal.fire({
            title: 'Edit Event',
            text: 'Update the event details in the modal.',
            icon: 'info',
            confirmButtonColor: '#0066cc'
        });

        this.openEventModal();
    }

    deleteEvent(index) {
        Swal.fire({
            title: 'Delete Event?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.events.splice(index, 1);
                this.displayTimelineEvents();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Event has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    }

    addMetricRow() {
        const container = document.getElementById('metricsContainer');
        const metricId = Date.now();
        const metricDiv = document.createElement('div');
        metricDiv.className = 'metric-item';
        metricDiv.id = `metric-${metricId}`;
        metricDiv.innerHTML = `
            <div class="metric-item-header">
                <input type="text" placeholder="Metric Name (e.g., Reaction Time, Accuracy, Teamwork)" class="metric-name" required>
                <input type="number" placeholder="Points" min="0" max="100" class="metric-points" style="max-width: 100px;" required>
                <button type="button" class="btn-delete" onclick="scenarioManager.deleteMetric(${metricId})">Delete</button>
            </div>
        `;
        container.appendChild(metricDiv);
    }

    deleteMetric(metricId) {
        const element = document.getElementById(`metric-${metricId}`);
        if (element) {
            element.remove();
        }
    }

    addChecklistRow() {
        const container = document.getElementById('checklistContainer');
        const checklistId = Date.now();
        const itemDiv = document.createElement('div');
        itemDiv.className = 'checklist-item';
        itemDiv.id = `checklist-${checklistId}`;
        itemDiv.innerHTML = `
            <input type="text" placeholder="Checklist item description (e.g., Evacuated building within 5 minutes)" required>
            <button type="button" class="btn-delete" onclick="scenarioManager.deleteChecklist(${checklistId})">Delete</button>
        `;
        container.appendChild(itemDiv);
    }

    deleteChecklist(checklistId) {
        const element = document.getElementById(`checklist-${checklistId}`);
        if (element) {
            element.remove();
        }
    }

    saveEvaluation(e) {
        e.preventDefault();

        const metrics = [];
        document.querySelectorAll('.metric-item').forEach(item => {
            const name = item.querySelector('.metric-name').value.trim();
            const points = parseInt(item.querySelector('.metric-points').value);
            if (name && !isNaN(points)) {
                metrics.push({ name, points });
            }
        });

        if (metrics.length === 0) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please add at least one evaluation metric.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        Swal.fire({
            title: 'Success!',
            text: 'Evaluation setup saved successfully with ' + metrics.length + ' metric(s)!',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        });
    }

    handleFileUpload(e, inputElement) {
        const files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
        if (files.length > 0) {
            Swal.fire({
                title: 'Uploading Files',
                html: `<p>Uploading ${files.length} file(s)...</p><div style="margin-top: 15px;"><div style="width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;"><div style="width: 0%; height: 100%; background: #0066cc; transition: width 0.3s;"></div></div></div>`,
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            setTimeout(() => {
                Swal.fire({
                    title: 'Upload Complete!',
                    html: `<p>${files.length} file(s) uploaded successfully.</p><p style="font-size: 12px; color: #666; margin-top: 10px;">Files: ${Array.from(files).map(f => f.name).join(', ')}</p>`,
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }, 2000);
        }
    }

    filterScenarios() {
        const search = document.getElementById('searchScenarios').value.toLowerCase();
        const disasterType = document.getElementById('filterDisasterType').value;
        const difficulty = document.getElementById('filterDifficulty').value;
        const status = document.getElementById('filterStatus').value;

        const filtered = this.scenarios.filter(scenario => {
            return (
                scenario.title.toLowerCase().includes(search) &&
                (disasterType === '' || scenario.disasterType === disasterType) &&
                (difficulty === '' || scenario.difficulty === difficulty) &&
                (status === '' || scenario.status === status)
            );
        });

        const tbody = document.getElementById('scenariosTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No scenarios found matching your filters.</td></tr>';
            return;
        }

        filtered.forEach(scenario => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${scenario.title}</strong></td>
                <td>${this.capitalize(scenario.disasterType)}</td>
                <td><span class="difficulty-badge difficulty-${scenario.difficulty}">${this.capitalize(scenario.difficulty)}</span></td>
                <td><span class="status-badge status-${scenario.status}">${this.capitalize(scenario.status)}</span></td>
                <td>${scenario.eventsUsed}</td>
                <td>${scenario.avgScore}%</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn btn-edit" onclick="scenarioManager.editScenario(${scenario.id})">Edit</button>
                        <button class="action-btn btn-preview" onclick="scenarioManager.previewScenario(${scenario.id})">Preview</button>
                        <button class="action-btn btn-assign" onclick="scenarioManager.assignScenario(${scenario.id})">Assign</button>
                        <button class="action-btn btn-duplicate" onclick="scenarioManager.duplicateScenario(${scenario.id})">Duplicate</button>
                        <button class="action-btn btn-delete" onclick="scenarioManager.deleteScenario(${scenario.id})">Delete</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    exportScenarioAnalytics() {
        const scenarioId = document.getElementById('analyticsScenarioFilter').value;
        
        if (!scenarioId) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select a scenario to export.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const scenario = this.scenarios.find(s => s.id == scenarioId);
        
        Swal.fire({
            title: 'Export Analytics',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select export format for <strong>${scenario.title}</strong>:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button onclick="scenarioManager.performExport('csv', ${scenarioId})" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
                        <button onclick="scenarioManager.performExport('pdf', ${scenarioId})" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📄 PDF</button>
                    </div>
                </div>
            `,
            icon: 'question',
            showConfirmButton: false
        });
    }

    performExport(format, scenarioId) {
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
        document.getElementById('scenarioForm').reset();
        this.events = [];
        this.metrics = [];
        this.checklist = [];
        this.currentScenarioId = null;
        document.getElementById('timelineEvents').innerHTML = '';
        document.getElementById('metricsContainer').innerHTML = '';
        document.getElementById('checklistContainer').innerHTML = '';
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ');
    }

    openAIGenerationModal() {
        document.getElementById('aiGenerationModal').style.display = 'block';
        document.getElementById('aiGenerationForm').reset();
    }

    closeAIGenerationModal() {
        document.getElementById('aiGenerationModal').style.display = 'none';
    }

    async generateScenarioWithAI(e) {
        e.preventDefault();

        const form = document.getElementById('aiGenerationForm');
        const formData = new FormData(form);
        
        // Get form values
        const params = {
            disaster_type: formData.get('disaster_type'),
            difficulty: formData.get('difficulty') || 'intermediate',
            location: 'Barangay San Agustin, Novaliches, Quezon City',
            incident_time: formData.get('incident_time') || 'day',
            weather_condition: formData.get('weather_condition') || 'sunny',
            location_type: formData.get('location_type') || 'building',
            additional_context: formData.get('additional_context') || ''
        };

        // Validate
        if (!params.disaster_type) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select a disaster type.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        // Show loading state
        const generateBtn = document.getElementById('generateScenarioBtn');
        const btnText = document.getElementById('generateBtnText');
        const btnLoading = document.getElementById('generateBtnLoading');
        
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        generateBtn.disabled = true;

        try {
            // Call API
            const response = await fetch('../api/generate-scenario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(params)
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error || 'Failed to generate scenario');
            }

            // Populate form with generated scenario
            this.populateFormWithAIScenario(result.data);

            // Close modal
            this.closeAIGenerationModal();

            // Switch to create-edit tab if not already there
            this.switchTab('create-edit');

            // Show success message
            Swal.fire({
                title: 'Success!',
                text: 'Scenario generated successfully! Review and edit as needed.',
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });

        } catch (error) {
            console.error('AI Generation Error:', error);
            Swal.fire({
                title: 'Generation Failed',
                html: `
                    <p>${error.message}</p>
                    <p style="font-size: 12px; color: #666; margin-top: 10px;">
                        Make sure the Gemini API key is configured in config.php
                    </p>
                `,
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
        } finally {
            // Reset button state
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
            generateBtn.disabled = false;
        }
    }

    populateFormWithAIScenario(data) {
        // Populate basic fields
        if (data.title) {
            document.getElementById('scenarioTitle').value = data.title;
        }
        
        // Combine description and initial_conditions for complete scenario description
        let fullDescription = '';
        if (data.description) {
            fullDescription = data.description;
        }
        if (data.initial_conditions) {
            if (fullDescription) {
                fullDescription += '\n\n**Initial Conditions:**\n' + data.initial_conditions;
            } else {
                fullDescription = '**Initial Conditions:**\n' + data.initial_conditions;
            }
        }
        // Add challenges and expected actions to description for reference
        if (data.challenges && data.challenges.length > 0) {
            fullDescription += '\n\n**Key Challenges:**\n';
            data.challenges.forEach((challenge, index) => {
                fullDescription += `${index + 1}. ${challenge}\n`;
            });
        }
        if (data.expected_actions && data.expected_actions.length > 0) {
            fullDescription += '\n\n**Expected Actions:**\n';
            data.expected_actions.forEach((action, index) => {
                fullDescription += `${index + 1}. ${action}\n`;
            });
        }
        
        if (fullDescription) {
            document.getElementById('scenarioDescription').value = fullDescription.trim();
        }

        // Set disaster type if it matches
        const disasterTypeSelect = document.getElementById('disasterType');
        if (data.disaster_type) {
            disasterTypeSelect.value = data.disaster_type;
        }

        // Set difficulty if available
        const difficultySelect = document.getElementById('difficultyLevel');
        if (data.difficulty) {
            difficultySelect.value = data.difficulty;
        }

        // Set incident time if available
        const incidentTimeSelect = document.getElementById('incidentTime');
        if (data.incident_time && incidentTimeSelect) {
            incidentTimeSelect.value = data.incident_time;
        }

        // Set weather condition if available
        const weatherConditionSelect = document.getElementById('weatherCondition');
        if (data.weather_condition && weatherConditionSelect) {
            weatherConditionSelect.value = data.weather_condition;
        }

        // Set location type if available
        const locationTypeSelect = document.getElementById('locationType');
        if (data.location_type && locationTypeSelect) {
            locationTypeSelect.value = data.location_type;
        }

        // Populate safety notes
        if (data.safety_notes) {
            document.getElementById('safetyNotes').value = data.safety_notes;
        }

        // Populate learning objectives
        if (data.learning_objectives && Array.isArray(data.learning_objectives)) {
            const learningObjectivesTextarea = document.getElementById('learningObjectives');
            learningObjectivesTextarea.value = data.learning_objectives.join('\n• ');
        }

        // Show summary notification with all generated details
        let notificationHtml = '<div style="text-align: left;">';
        
        if (data.challenges && data.challenges.length > 0) {
            const challengesText = data.challenges.map((c, i) => `${i + 1}. ${c}`).join('\n');
            notificationHtml += `<h4>Key Challenges:</h4><p style="white-space: pre-line; margin-bottom: 15px;">${challengesText}</p>`;
        }
        
        if (data.expected_actions && data.expected_actions.length > 0) {
            const actionsText = data.expected_actions.map((a, i) => `${i + 1}. ${a}`).join('\n');
            notificationHtml += `<h4>Expected Actions:</h4><p style="white-space: pre-line; margin-bottom: 15px;">${actionsText}</p>`;
        }
        
        if (data.initial_conditions) {
            notificationHtml += `<h4>Initial Conditions:</h4><p style="white-space: pre-line; margin-bottom: 15px;">${data.initial_conditions}</p>`;
        }
        
        notificationHtml += '<p style="color: #666; font-size: 12px; margin-top: 15px;">All information has been populated in the form fields below.</p>';
        notificationHtml += '</div>';
        
        Swal.fire({
            title: 'Scenario Generated Successfully!',
            html: notificationHtml,
            icon: 'success',
            confirmButtonColor: '#0066cc',
            width: '700px'
        });
    }
}

// Dropdown menu toggle function
function toggleActionMenu(event, scenarioId) {
    event.stopPropagation();
    const menu = document.getElementById(`menu-${scenarioId}`);
    const allMenus = document.querySelectorAll('.dropdown-menu');
    
    // Close all other menus and clear inline positioning
    allMenus.forEach(m => {
        if (m !== menu) {
            m.classList.remove('show');
            m.classList.remove('drop-up');
            m.style.top = '';
            m.style.bottom = '';
            m.style.left = '';
            m.style.right = '';
            m.style.position = '';
        }
    });
    
    // Toggle current menu and adjust direction so it stays in view
    menu.classList.toggle('show');

    if (menu.classList.contains('show')) {
        // Use fixed positioning so parent overflow does not clip the menu
        const btnRect = event.currentTarget.getBoundingClientRect();
        const menuHeight = menu.scrollHeight;
        const menuWidth = menu.offsetWidth || 160; // fallback width
        const spaceBelow = window.innerHeight - btnRect.bottom;
        const spaceAbove = btnRect.top;

        // Prefer the side with enough space; if neither has enough, use the side with more space
        let openUp = false;
        if (spaceBelow >= menuHeight) {
            openUp = false;
        } else if (spaceAbove >= menuHeight) {
            openUp = true;
        } else {
            openUp = spaceAbove > spaceBelow;
        }

        const top = openUp ? btnRect.top - menuHeight - 6 : btnRect.bottom + 6;
        const left = Math.min(window.innerWidth - menuWidth - 8, Math.max(8, btnRect.left));

        menu.classList.toggle('drop-up', openUp);
        menu.style.position = 'fixed';
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
        menu.style.right = 'auto';
        menu.style.bottom = 'auto';
    } else {
        // reset when closing
        menu.style.top = '';
        menu.style.bottom = '';
        menu.style.left = '';
        menu.style.right = '';
        menu.style.position = '';
        menu.classList.remove('drop-up');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.classList.remove('show');
    });
});

// Initialize on page load
const scenarioManager = new ScenarioDesignManager();
