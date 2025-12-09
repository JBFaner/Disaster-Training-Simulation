class SimulationPlanningManager {
    constructor() {
        this.events = [];
        this.trainers = [];
        this.phases = [];
        this.resources = [];
        this.currentEventId = null;
        this.currentTrainerId = null;
        this.currentPhaseId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadEvents();
        this.loadScenarios();
    }

    setupEventListeners() {
        // Tab Navigation
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Event Creation
        document.getElementById('createEventBtn').addEventListener('click', () => {
            this.resetForm();
            this.switchTab('create-edit');
        });

        // Form Submission
        document.getElementById('eventForm').addEventListener('submit', (e) => this.saveEvent(e));
        document.getElementById('cancelEventBtn').addEventListener('click', () => this.confirmCancel());

        // Time duration calculation
        document.getElementById('startTime').addEventListener('change', () => this.calculateDuration());
        document.getElementById('endTime').addEventListener('change', () => this.calculateDuration());

        // Scenario selection
        document.getElementById('assignedScenario').addEventListener('change', (e) => this.showScenarioSummary(e.target.value));

        // Trainers
        document.getElementById('addTrainerBtn').addEventListener('click', () => this.openTrainerModal());
        document.getElementById('trainerForm').addEventListener('submit', (e) => this.saveTrainer(e));
        document.getElementById('closeTrainerBtn').addEventListener('click', () => this.closeTrainerModal());

        // Workflow Phases
        document.getElementById('addPhaseBtn').addEventListener('click', () => this.openPhaseModal());
        document.getElementById('phaseForm').addEventListener('submit', (e) => this.savePhase(e));
        document.getElementById('closePhaseBtn').addEventListener('click', () => this.closePhaseModal());

        // Resources
        document.getElementById('addResourceBtn').addEventListener('click', () => this.addResourceRow());

        // Filters
        document.getElementById('searchEvents').addEventListener('input', () => this.filterEvents());
        document.getElementById('filterDisasterType').addEventListener('change', () => this.filterEvents());
        document.getElementById('filterCategory').addEventListener('change', () => this.filterEvents());
        document.getElementById('filterStatus').addEventListener('change', () => this.filterEvents());

        // Reports
        document.getElementById('generateReportBtn').addEventListener('click', () => this.generateReport());
        document.getElementById('exportReportBtn').addEventListener('click', () => this.exportReport());

        // File uploads
        document.querySelectorAll('.file-upload-area').forEach(area => {
            area.addEventListener('click', () => area.querySelector('input').click());
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

    loadEvents() {
        this.events = [
            {
                id: 1,
                title: 'Earthquake Evacuation Drill',
                disasterType: 'earthquake',
                category: 'drill',
                date: '2024-02-15',
                startTime: '09:00',
                endTime: '11:00',
                location: 'City Hall Building',
                participants: 45,
                status: 'scheduled',
                description: 'City-wide earthquake evacuation drill',
                checkedIn: 42,
                noShows: 3
            },
            {
                id: 2,
                title: 'Hospital Fire Safety Exercise',
                disasterType: 'fire',
                category: 'full-scale',
                date: '2024-02-20',
                startTime: '14:00',
                endTime: '16:30',
                location: 'General Hospital',
                participants: 80,
                status: 'scheduled',
                description: 'Full-scale fire evacuation with patient handling',
                checkedIn: 0,
                noShows: 0
            }
        ];
        this.displayEvents();
    }

    loadScenarios() {
        const select = document.getElementById('assignedScenario');
        select.innerHTML = '<option value="">Select Scenario</option>';
        
        const scenarios = [
            { id: 1, title: 'Downtown Building Earthquake', type: 'earthquake', difficulty: 'intermediate' },
            { id: 2, title: 'Hospital Fire Evacuation', type: 'fire', difficulty: 'advanced' },
            { id: 3, title: 'Flood Response Exercise', type: 'flood', difficulty: 'basic' }
        ];
        scenarios.forEach(scenario => {
            const option = document.createElement('option');
            option.value = scenario.id;
            option.textContent = scenario.title;
            option.dataset.type = scenario.type;
            option.dataset.difficulty = scenario.difficulty;
            select.appendChild(option);
        });

        const reportSelect = document.getElementById('reportEventFilter');
        reportSelect.innerHTML = '<option value="">Select Event</option>';
        this.events.forEach(event => {
            const option = document.createElement('option');
            option.value = event.id;
            option.textContent = event.title;
            reportSelect.appendChild(option);
        });
    }

    displayEvents() {
        const tbody = document.getElementById('eventsTableBody');
        tbody.innerHTML = '';

        if (this.events.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No events found.</td></tr>';
            return;
        }

        this.events.forEach(event => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${event.title}</strong></td>
                <td>${this.capitalize(event.disasterType)}</td>
                <td>${this.capitalize(event.category)}</td>
                <td>${event.date} ${event.startTime}</td>
                <td>${event.location}</td>
                <td>${event.participants}</td>
                <td><span class="status-badge status-${event.status}">${this.capitalize(event.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="simManager.editEvent(${event.id})">Edit</button>
                        <button class="btn-preview" onclick="simManager.previewEvent(${event.id})">Preview</button>
                        <button class="btn-start" onclick="simManager.startEvent(${event.id})">Start</button>
                        <button class="btn-delete" onclick="simManager.deleteEvent(${event.id})">Delete</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    editEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            this.currentEventId = eventId;
            document.getElementById('eventTitle').value = event.title;
            document.getElementById('eventDescription').value = event.description;
            document.getElementById('disasterType').value = event.disasterType;
            document.getElementById('eventCategory').value = event.category;
            document.getElementById('eventDate').value = event.date;
            document.getElementById('startTime').value = event.startTime;
            document.getElementById('endTime').value = event.endTime;
            document.getElementById('eventLocation').value = event.location;
            this.calculateDuration();

            Swal.fire({
                title: 'Editing Event',
                html: `<strong>${event.title}</strong> is now being edited.`,
                icon: 'info',
                timer: 2000,
                timerProgressBar: true
            });

            this.switchTab('create-edit');
        }
    }

    previewEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            Swal.fire({
                title: event.title,
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <div style="background: #f0f7ff; padding: 15px; border-radius: 5px;">
                            <p style="margin: 8px 0;"><strong>📍 Location:</strong> ${event.location}</p>
                            <p style="margin: 8px 0;"><strong>📅 Date:</strong> ${event.date}</p>
                            <p style="margin: 8px 0;"><strong>⏰ Time:</strong> ${event.startTime} - ${event.endTime}</p>
                            <p style="margin: 8px 0;"><strong>👥 Participants:</strong> ${event.participants}</p>
                            <p style="margin: 8px 0;"><strong>🏷️ Category:</strong> ${this.capitalize(event.category)}</p>
                            <p style="margin: 8px 0;"><strong>🌍 Disaster Type:</strong> ${this.capitalize(event.disasterType)}</p>
                            <p style="margin: 8px 0;"><strong>📝 Description:</strong><br>${event.description}</p>
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonColor: '#0066cc',
                confirmButtonText: 'Close',
                width: '600px'
            });
        }
    }

    startEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            Swal.fire({
                title: 'Start Event?',
                html: `<p>Start <strong>${event.title}</strong>?</p><p style="font-size: 12px; color: #666;">All participants will receive notifications.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, start it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.status = 'in-progress';
                    this.displayEvents();
                    
                    Swal.fire({
                        title: 'Started!',
                        text: `${event.title} has started successfully. Notifications sent to ${event.participants} participants.`,
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    deleteEvent(eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (event) {
            Swal.fire({
                title: 'Delete Event?',
                html: `<p>Are you sure you want to delete <strong>${event.title}</strong>?</p><p style="font-size: 12px; color: #d32f2f;">⚠️ This action cannot be undone!</p>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.events = this.events.filter(e => e.id !== eventId);
                    this.displayEvents();
                    
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Event has been deleted.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    saveEvent(e) {
        e.preventDefault();

        const title = document.getElementById('eventTitle').value.trim();
        const description = document.getElementById('eventDescription').value.trim();
        const disasterType = document.getElementById('disasterType').value;
        const category = document.getElementById('eventCategory').value;
        const date = document.getElementById('eventDate').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;
        const location = document.getElementById('eventLocation').value.trim();

        if (!title || !description || !disasterType || !category || !date || !startTime || !endTime || !location) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const formData = new FormData(document.getElementById('eventForm'));
        const existingEvent = this.currentEventId ? this.events.find(e => e.id === this.currentEventId) : null;
        
        const eventData = {
            id: this.currentEventId || Date.now(),
            title: title,
            description: description,
            disasterType: disasterType,
            category: category,
            date: date,
            startTime: startTime,
            endTime: endTime,
            location: location,
            status: formData.get('publish_status'),
            participants: existingEvent ? existingEvent.participants : 0,
            checkedIn: existingEvent ? existingEvent.checkedIn : 0,
            noShows: existingEvent ? existingEvent.noShows : 0
        };

        if (this.currentEventId) {
            const index = this.events.findIndex(e => e.id === this.currentEventId);
            this.events[index] = { ...this.events[index], ...eventData };
        } else {
            this.events.push(eventData);
        }

        Swal.fire({
            title: 'Success!',
            text: this.currentEventId ? 'Event updated successfully!' : 'Event created successfully!',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.displayEvents();
            this.loadScenarios();
            this.resetForm();
            this.switchTab('events-list');
        });
    }

    calculateDuration() {
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (startTime && endTime) {
            const start = new Date(`2024-01-01 ${startTime}`);
            const end = new Date(`2024-01-01 ${endTime}`);
            const diffMs = end - start;
            const diffMins = Math.round(diffMs / 60000);

            const hours = Math.floor(diffMins / 60);
            const mins = diffMins % 60;
            const duration = hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;

            document.getElementById('eventDuration').value = duration;
        }
    }

    showScenarioSummary(scenarioId) {
        const summaryDiv = document.getElementById('scenarioSummary');
        const summaryText = document.getElementById('scenarioSummaryText');

        if (!scenarioId) {
            summaryDiv.style.display = 'none';
            return;
        }

        const scenarios = {
            1: { title: 'Downtown Building Earthquake', type: 'Earthquake', difficulty: 'Intermediate', duration: '45 minutes' },
            2: { title: 'Hospital Fire Evacuation', type: 'Fire', difficulty: 'Advanced', duration: '60 minutes' },
            3: { title: 'Flood Response Exercise', type: 'Flood', difficulty: 'Basic', duration: '30 minutes' }
        };

        const scenario = scenarios[scenarioId];
        if (scenario) {
            summaryText.innerHTML = `
                <strong>${scenario.title}</strong><br>
                Type: ${scenario.type} | Difficulty: ${scenario.difficulty} | Duration: ${scenario.duration}
            `;
            summaryDiv.style.display = 'block';
        }
    }

    confirmCancel() {
        Swal.fire({
            title: 'Cancel Event Creation?',
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
                this.switchTab('events-list');
            }
        });
    }

    openTrainerModal() {
        document.getElementById('trainerModal').style.display = 'block';
    }

    closeTrainerModal() {
        document.getElementById('trainerModal').style.display = 'none';
        document.getElementById('trainerForm').reset();
        this.currentTrainerId = null;
    }

    saveTrainer(e) {
        e.preventDefault();

        const name = document.getElementById('trainerName').value.trim();
        const role = document.getElementById('trainerRole').value;
        const contact = document.getElementById('trainerContact').value.trim();
        const email = document.getElementById('trainerEmail').value.trim();

        if (!name || !role || !contact || !email) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        // Basic email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                title: 'Invalid Email',
                text: 'Please enter a valid email address.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const trainer = {
            id: this.currentTrainerId || Date.now(),
            name: name,
            role: role,
            contact: contact,
            email: email
        };

        if (!this.currentTrainerId) {
            this.trainers.push(trainer);
        }

        this.displayTrainers();

        Swal.fire({
            title: 'Success!',
            text: 'Trainer added successfully.',
            icon: 'success',
            timer: 1500,
            timerProgressBar: true
        });

        this.closeTrainerModal();
    }

    editTrainer(index) {
        const trainer = this.trainers[index];
        this.currentTrainerId = trainer.id;
        document.getElementById('trainerName').value = trainer.name;
        document.getElementById('trainerRole').value = trainer.role;
        document.getElementById('trainerContact').value = trainer.contact;
        document.getElementById('trainerEmail').value = trainer.email;
        this.openTrainerModal();
    }

    displayTrainers() {
        const container = document.getElementById('trainersContainer');
        container.innerHTML = '';

        if (this.trainers.length === 0) {
            container.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">No trainers added yet. Click "Add Trainer" to get started.</p>';
            return;
        }

        this.trainers.forEach((trainer, index) => {
            const trainerDiv = document.createElement('div');
            trainerDiv.className = 'trainer-item';
            trainerDiv.innerHTML = `
                <h5>${trainer.name}</h5>
                <p><strong>Role:</strong> ${this.capitalize(trainer.role)}</p>
                <p><strong>Contact:</strong> ${trainer.contact}</p>
                <p><strong>Email:</strong> ${trainer.email}</p>
                <span class="role-badge">${this.capitalize(trainer.role)}</span>
                <div class="trainer-item-actions">
                    <button class="btn-edit" onclick="simManager.editTrainer(${index})">Edit</button>
                    <button class="btn-delete" onclick="simManager.deleteTrainer(${index})">Delete</button>
                </div>
            `;
            container.appendChild(trainerDiv);
        });
    }

    deleteTrainer(index) {
        Swal.fire({
            title: 'Delete Trainer?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.trainers.splice(index, 1);
                this.displayTrainers();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Trainer has been removed.',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    }

    openPhaseModal() {
        document.getElementById('phaseModal').style.display = 'block';
    }

    closePhaseModal() {
        document.getElementById('phaseModal').style.display = 'none';
        document.getElementById('phaseForm').reset();
        this.currentPhaseId = null;
    }

    savePhase(e) {
        e.preventDefault();

        const name = document.getElementById('phaseName').value.trim();
        const duration = parseInt(document.getElementById('phaseDuration').value);
        const description = document.getElementById('phaseDescription').value.trim();
        const instructions = document.getElementById('facilitatorInstructions').value.trim();

        if (!name || isNaN(duration) || !description) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const phase = {
            id: this.currentPhaseId || Date.now(),
            name: name,
            duration: duration,
            description: description,
            instructions: instructions,
            order: this.phases.length + 1
        };

        if (!this.currentPhaseId) {
            this.phases.push(phase);
        }

        this.displayWorkflowPhases();

        Swal.fire({
            title: 'Success!',
            text: 'Event phase added successfully.',
            icon: 'success',
            timer: 1500,
            timerProgressBar: true
        });

        this.closePhaseModal();
    }

    displayWorkflowPhases() {
        const container = document.getElementById('workflowPhases');
        container.innerHTML = '';

        if (this.phases.length === 0) {
            container.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">No workflow phases added yet. Click "Add Event Phase" to create one.</p>';
            return;
        }

        let totalDuration = 0;
        this.phases.forEach((phase, index) => {
            totalDuration += phase.duration;
            const phaseDiv = document.createElement('div');
            phaseDiv.className = 'workflow-phase';
            phaseDiv.innerHTML = `
                <div class="workflow-phase-header">
                    <div>
                        <h4>Phase ${index + 1}: ${phase.name}</h4>
                    </div>
                    <span class="phase-duration-badge">⏱️ ${phase.duration} mins</span>
                </div>
                <div class="workflow-phase-content">
                    <p><strong>Description:</strong> ${phase.description}</p>
                    ${phase.instructions ? `<p><strong>Facilitator Instructions:</strong> ${phase.instructions}</p>` : ''}
                </div>
                <div class="workflow-phase-actions">
                    <button class="btn-edit" onclick="simManager.editPhase(${index})">Edit</button>
                    <button class="btn-delete" onclick="simManager.deletePhase(${index})">Delete</button>
                </div>
            `;
            container.appendChild(phaseDiv);
        });

        // Add total duration info
        const totalDiv = document.createElement('div');
        totalDiv.style.cssText = 'background: #f0f7ff; padding: 15px; border-radius: 5px; margin-top: 20px; text-align: center;';
        totalDiv.innerHTML = `<strong>Total Event Duration: ${totalDuration} minutes (${Math.floor(totalDuration / 60)}h ${totalDuration % 60}m)</strong>`;
        container.appendChild(totalDiv);
    }

    editPhase(index) {
        const phase = this.phases[index];
        this.currentPhaseId = phase.id;
        document.getElementById('phaseName').value = phase.name;
        document.getElementById('phaseDuration').value = phase.duration;
        document.getElementById('phaseDescription').value = phase.description;
        document.getElementById('facilitatorInstructions').value = phase.instructions;
        this.openPhaseModal();
    }

    deletePhase(index) {
        Swal.fire({
            title: 'Delete Phase?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.phases.splice(index, 1);
                this.displayWorkflowPhases();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Phase has been removed.',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    }

    addResourceRow() {
        const container = document.getElementById('resourcesContainer');
        const resourceId = Date.now();
        const resourceDiv = document.createElement('div');
        resourceDiv.className = 'resource-item';
        resourceDiv.id = `resource-${resourceId}`;
        resourceDiv.innerHTML = `
            <h5>Resource Item</h5>
            <p><input type="text" placeholder="Resource name (e.g., Fire Extinguisher)" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" class="resource-name"></p>
            <p><input type="number" placeholder="Quantity" min="1" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" class="resource-quantity" value="1"></p>
            <p>
                <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" class="resource-status-select">
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </p>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-edit" onclick="simManager.updateResource(${resourceId})" style="flex: 1;">Update</button>
                <button type="button" class="btn-delete" onclick="simManager.deleteResource(${resourceId})" style="flex: 1;">Delete</button>
            </div>
        `;
        container.appendChild(resourceDiv);
    }

    updateResource(resourceId) {
        const element = document.getElementById(`resource-${resourceId}`);
        if (element) {
            const name = element.querySelector('.resource-name').value;
            const quantity = element.querySelector('.resource-quantity').value;
            const status = element.querySelector('.resource-status-select').value;

            if (!name || !quantity) {
                Swal.fire({
                    title: 'Validation Error',
                    text: 'Please fill in all resource details.',
                    icon: 'error',
                    confirmButtonColor: '#0066cc'
                });
                return;
            }

            element.innerHTML = `
                <h5>${name}</h5>
                <p><strong>Quantity:</strong> ${quantity}</p>
                <span class="resource-status ${status}">${this.capitalize(status)}</span>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" class="btn-edit" onclick="simManager.editResourceDetail(${resourceId})">Edit</button>
                    <button type="button" class="btn-delete" onclick="simManager.deleteResource(${resourceId})">Delete</button>
                </div>
            `;

            Swal.fire({
                title: 'Success!',
                text: 'Resource updated successfully.',
                icon: 'success',
                timer: 1500,
                timerProgressBar: true
            });
        }
    }

    editResourceDetail(resourceId) {
        const element = document.getElementById(`resource-${resourceId}`);
        const innerText = element.innerText;
        element.innerHTML = `
            <p><input type="text" placeholder="Resource name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" class="resource-name" value="${innerText.split('\n')[0]}"></p>
            <p><input type="number" placeholder="Quantity" min="1" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" class="resource-quantity" value="1"></p>
            <p>
                <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;" class="resource-status-select">
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </p>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-edit" onclick="simManager.updateResource(${resourceId})" style="flex: 1;">Save</button>
                <button type="button" class="btn-secondary" onclick="simManager.cancelEditResource(${resourceId})" style="flex: 1;">Cancel</button>
            </div>
        `;
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
                const element = document.getElementById(`resource-${resourceId}`);
                if (element) {
                    element.remove();
                }
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Resource has been removed.',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });
    }

    generateReport() {
        const eventId = document.getElementById('reportEventFilter').value;

        if (!eventId) {
            Swal.fire({
                title: 'Select Event',
                text: 'Please select an event to generate report.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const event = this.events.find(e => e.id == eventId);
        
        // Update report cards
        document.getElementById('registeredParticipants').textContent = event.participants;
        document.getElementById('checkedInCount').textContent = event.checkedIn;
        document.getElementById('noShowCount').textContent = event.noShows;
        
        const attendanceRate = event.participants > 0 
            ? Math.round((event.checkedIn / event.participants) * 100) 
            : 0;
        document.getElementById('attendanceRate').textContent = attendanceRate + '%';

        // Generate sample logs
        const logsTable = document.getElementById('eventLogsTable');
        logsTable.innerHTML = `
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Participant Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Role</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Check-in Time</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">John Dela Cruz</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">Trainer</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">08:45 AM</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 3px; font-size: 12px;">Checked In</span></td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">Maria Santos</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">Participant</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">09:15 AM</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 3px; font-size: 12px;">Checked In</span></td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">Juan Rizal</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">Participant</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">--</td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><span style="background: #f8d7da; color: #842029; padding: 4px 8px; border-radius: 3px; font-size: 12px;">No-show</span></td>
                    </tr>
                </tbody>
            </table>
        `;

        Swal.fire({
            title: 'Report Generated!',
            text: `Report for ${event.title} has been generated successfully.`,
            icon: 'success',
            confirmButtonColor: '#0066cc'
        });
    }

    exportReport() {
        const eventId = document.getElementById('reportEventFilter').value;

        if (!eventId) {
            Swal.fire({
                title: 'Select Event',
                text: 'Please select an event to export report.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        Swal.fire({
            title: 'Export Report',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select export format:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button onclick="simManager.performExport('pdf')" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📄 PDF</button>
                        <button onclick="simManager.performExport('csv')" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
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
                text: `Report exported as ${format.toUpperCase()} successfully.`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    handleFileUpload(e, inputElement) {
        const files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
        if (files.length > 0) {
            Swal.fire({
                title: 'Uploading Files',
                html: `<p>Uploading ${files.length} file(s)...</p>`,
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

    filterEvents() {
        const search = document.getElementById('searchEvents').value.toLowerCase();
        const disasterType = document.getElementById('filterDisasterType').value;
        const category = document.getElementById('filterCategory').value;
        const status = document.getElementById('filterStatus').value;

        const filtered = this.events.filter(event => {
            return (
                event.title.toLowerCase().includes(search) &&
                (disasterType === '' || event.disasterType === disasterType) &&
                (category === '' || event.category === category) &&
                (status === '' || event.status === status)
            );
        });

        const tbody = document.getElementById('eventsTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No events found matching your filters.</td></tr>';
            return;
        }

        filtered.forEach(event => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${event.title}</strong></td>
                <td>${this.capitalize(event.disasterType)}</td>
                <td>${this.capitalize(event.category)}</td>
                <td>${event.date} ${event.startTime}</td>
                <td>${event.location}</td>
                <td>${event.participants}</td>
                <td><span class="status-badge status-${event.status}">${this.capitalize(event.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="simManager.editEvent(${event.id})">Edit</button>
                        <button class="btn-preview" onclick="simManager.previewEvent(${event.id})">Preview</button>
                        <button class="btn-start" onclick="simManager.startEvent(${event.id})">Start</button>
                        <button class="btn-delete" onclick="simManager.deleteEvent(${event.id})">Delete</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    resetForm() {
        document.getElementById('eventForm').reset();
        this.trainers = [];
        this.phases = [];
        this.resources = [];
        this.currentEventId = null;
        document.getElementById('trainersContainer').innerHTML = '';
        document.getElementById('workflowPhases').innerHTML = '';
        document.getElementById('resourcesContainer').innerHTML = '';
        document.getElementById('eventDuration').value = '';
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ');
    }
}

// Initialize on page load
const simManager = new SimulationPlanningManager();
