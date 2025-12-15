class EvaluationScoringManager {
    constructor() {
        this.events = [];
        this.participants = [];
        this.evaluations = [];
        this.criteria = [];
        this.templates = [];
        this.evaluationLogs = [];
        this.currentParticipantId = null;
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

        // Dashboard Filters
        const filterEvent = document.getElementById('filterEvaluationEvent');
        const filterStatus = document.getElementById('filterEvaluationStatus');
        if (filterEvent) filterEvent.addEventListener('change', () => this.loadDashboard());
        if (filterStatus) filterStatus.addEventListener('change', () => this.loadDashboard());

        // Criteria Setup
        const addCriteriaBtn = document.getElementById('addCriteriaBtn');
        const createTemplateBtn = document.getElementById('createTemplateBtn');
        const saveAsTemplateBtn = document.getElementById('saveAsTemplateCriteriaBtn');
        const loadTemplateBtn = document.getElementById('loadTemplateBtn');
        const criteriaForm = document.getElementById('criteriaForm');

        if (addCriteriaBtn) {
            addCriteriaBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.addCriteriaItem();
            });
        }
        
        if (createTemplateBtn) {
            createTemplateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.createNewTemplate();
            });
        }
        
        if (saveAsTemplateBtn) {
            saveAsTemplateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveAsTemplate();
            });
        }
        
        if (loadTemplateBtn) {
            loadTemplateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openTemplateModal();
            });
        }
        
        if (criteriaForm) {
            criteriaForm.addEventListener('submit', (e) => this.saveCriteria(e));
        }

        // Participant Scoring
        const filterScoringEvent = document.getElementById('filterScoringEvent');
        if (filterScoringEvent) filterScoringEvent.addEventListener('change', () => this.loadParticipantScoring());

        // Performance Analysis
        const analyticsEventFilter = document.getElementById('analyticsEventFilter');
        const exportPerformanceBtn = document.getElementById('exportPerformanceBtn');
        if (analyticsEventFilter) analyticsEventFilter.addEventListener('change', () => this.loadPerformanceAnalysis());
        if (exportPerformanceBtn) exportPerformanceBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.exportPerformanceReport();
        });

        // Evaluation Logs
        const searchLogs = document.getElementById('searchEvaluationLogs');
        const filterEvaluator = document.getElementById('filterEvaluatorLogs');
        if (searchLogs) searchLogs.addEventListener('input', () => this.filterLogs());
        if (filterEvaluator) filterEvaluator.addEventListener('change', () => this.filterLogs());

        // Scoring Modal
        const scoringForm = document.getElementById('scoringForm');
        const closeScoringBtn = document.getElementById('closeScoringBtn');
        if (scoringForm) scoringForm.addEventListener('submit', (e) => this.saveEvaluation(e));
        if (closeScoringBtn) closeScoringBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.closeScoringModal();
        });

        // Modal Close Buttons
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const modal = e.target.closest('.modal');
                if (modal) modal.style.display = 'none';
            });
        });

        // Close modal on outside click
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });

        // Prevent form submission for button clicks
        document.querySelectorAll('button[type="button"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!btn.form) {
                    e.preventDefault();
                }
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

        const btnElement = document.querySelector(`[data-tab="${tabName}"]`);
        if (btnElement) {
            btnElement.classList.add('active');
        }

        // Load data when switching tabs
        if (tabName === 'dashboard') {
            this.loadDashboard();
        } else if (tabName === 'participant-scoring') {
            this.loadParticipantScoring();
        } else if (tabName === 'performance') {
            this.loadPerformanceAnalysis();
        } else if (tabName === 'logs') {
            this.loadEvaluationLogs();
        }
    }

    loadData() {
        this.events = [
            { id: 1, name: 'Earthquake Evacuation Drill', date: '2024-02-15' },
            { id: 2, name: 'Fire Safety Exercise', date: '2024-02-20' }
        ];

        this.participants = [
            { id: 1, name: 'John Dela Cruz', role: 'staff', eventId: 1, attended: true },
            { id: 2, name: 'Maria Santos', role: 'volunteer', eventId: 1, attended: true },
            { id: 3, name: 'Juan Rizal', role: 'student', eventId: 1, attended: false }
        ];

        this.criteria = [
            { id: 1, name: 'Response Time', weight: 25 },
            { id: 2, name: 'Accuracy', weight: 25 },
            { id: 3, name: 'Teamwork', weight: 25 },
            { id: 4, name: 'Communication', weight: 25 }
        ];

        this.templates = [
            { id: 1, name: 'Standard Drill Evaluation', criteria: JSON.parse(JSON.stringify(this.criteria)) }
        ];

        this.loadEventSelects();
        this.loadDashboard();
    }

    loadEventSelects() {
        const selects = [
            'filterEvaluationEvent',
            'filterScoringEvent',
            'analyticsEventFilter'
        ];

        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.innerHTML = '<option value="">Select Event</option>';
                this.events.forEach(event => {
                    const option = document.createElement('option');
                    option.value = event.id;
                    option.textContent = event.name;
                    select.appendChild(option);
                });
            }
        });
    }

    loadDashboard() {
        const eventId = document.getElementById('filterEvaluationEvent')?.value;
        const status = document.getElementById('filterEvaluationStatus')?.value;

        let filteredParticipants = this.participants;
        if (eventId) {
            filteredParticipants = filteredParticipants.filter(p => p.eventId == eventId);
        }

        // Update summary cards
        document.getElementById('totalParticipantsEval').textContent = filteredParticipants.length;
        
        const pending = filteredParticipants.filter(p => !this.evaluations.find(e => e.participantId === p.id && e.eventId === (eventId || 1))).length;
        document.getElementById('pendingEvalCount').textContent = pending;

        const evaluatedParticipants = filteredParticipants.filter(p => this.evaluations.find(e => e.participantId === p.id && e.eventId === (eventId || 1)));
        const avgScore = evaluatedParticipants.length > 0 
            ? Math.round(evaluatedParticipants.reduce((sum, p) => {
                const evaluation = this.evaluations.find(e => e.participantId === p.id && e.eventId === (eventId || 1));
                return sum + (evaluation?.score || 0);
              }, 0) / evaluatedParticipants.length)
            : 0;
        document.getElementById('avgEventScore').textContent = avgScore + '%';

        const passCount = evaluatedParticipants.filter(p => {
            const evaluation = this.evaluations.find(e => e.participantId === p.id && e.eventId === (eventId || 1));
            return evaluation?.result === 'passed';
        }).length;
        const passRate = evaluatedParticipants.length > 0 ? Math.round((passCount / evaluatedParticipants.length) * 100) : 0;
        document.getElementById('eventPassRate').textContent = passRate + '%';

        this.displayDashboard(filteredParticipants, status);
    }

    displayDashboard(participants, status) {
        const tbody = document.getElementById('evaluationDashboardBody');
        tbody.innerHTML = '';

        participants.forEach(participant => {
            const evaluation = this.evaluations.find(e => e.participantId === participant.id);
            const evalStatus = evaluation?.status || 'pending';

            if (!status || evalStatus === status) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${participant.name}</strong></td>
                    <td>${this.capitalize(participant.role)}</td>
                    <td><span class="status-badge status-${participant.attended ? 'passed' : 'failed'}">${participant.attended ? 'Present' : 'Absent'}</span></td>
                    <td><span class="status-badge status-${evalStatus}">${this.capitalize(evalStatus)}</span></td>
                    <td>${evaluation?.score || '--'}%</td>
                    <td><span class="status-badge status-${evaluation?.result || 'pending'}">${this.capitalize(evaluation?.result || 'pending')}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" type="button" onclick="evalManager.evaluateParticipant(${participant.id})">Evaluate</button>
                            ${evaluation ? `<button class="btn-delete" type="button" onclick="evalManager.deleteEvaluation(${participant.id})">Delete</button>` : ''}
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }
        });
    }

    addCriteriaItem() {
        const container = document.getElementById('criteriaContainer');
        const itemId = Date.now();
        const item = document.createElement('div');
        item.className = 'criteria-item';
        item.id = `criteria-${itemId}`;
        item.innerHTML = `
            <div class="criteria-item-header">
                <input type="text" placeholder="Criterion name" class="criterion-name">
                <input type="number" placeholder="Weight (%)" min="0" max="100" class="criterion-weight" value="25">
                <div class="criteria-item-actions">
                    <button type="button" class="btn-delete" onclick="evalManager.deleteCriteria(${itemId})">Delete</button>
                </div>
            </div>
        `;
        container.appendChild(item);
    }

    deleteCriteria(itemId) {
        // Convert itemId to string for comparison
        const stringId = String(itemId);
        
        // Try numeric ID format first
        let element = document.getElementById(`criteria-${itemId}`);
        if (element) {
            element.remove();
            return;
        }
        
        // Try direct ID format (for template loading)
        element = document.getElementById(stringId);
        if (element) {
            element.remove();
            return;
        }
        
        // Try with quotes stripped
        const cleanId = stringId.replace(/['"]/g, '');
        element = document.getElementById(cleanId);
        if (element) {
            element.remove();
        }
    }

    saveCriteria(e) {
        e.preventDefault();

        const scoringMethod = document.getElementById('scoringMethod');
        if (!scoringMethod || !scoringMethod.value) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select a scoring method.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        this.criteria = [];
        const criteriaContainer = document.getElementById('criteriaContainer');
        
        if (!criteriaContainer) {
            Swal.fire({
                title: 'Error',
                text: 'Criteria container not found.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        let uniqueId = 1000; // Start with a base number
        criteriaContainer.querySelectorAll('.criteria-item').forEach(item => {
            const nameInput = item.querySelector('.criterion-name');
            const weightInput = item.querySelector('.criterion-weight');
            
            if (nameInput && weightInput) {
                const name = nameInput.value.trim();
                const weight = parseInt(weightInput.value) || 0;
                
                if (name && weight > 0) {
                    this.criteria.push({ 
                        id: uniqueId++,  // Use sequential IDs instead of timestamp
                        name, 
                        weight 
                    });
                }
            }
        });

        if (this.criteria.length === 0) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please add at least one evaluation criterion with a name and weight.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        Swal.fire({
            title: 'Success!',
            text: 'Evaluation criteria saved successfully.',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        });
    }

    saveAsTemplate() {
        const templateName = document.getElementById('templateName').value.trim();

        if (!templateName) {
            Swal.fire({
                title: 'Error',
                text: 'Please enter a template name.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        this.templates.push({
            id: Date.now(),
            name: templateName,
            criteria: JSON.parse(JSON.stringify(this.criteria))
        });

        Swal.fire({
            title: 'Saved!',
            text: `Template "${templateName}" has been saved.`,
            icon: 'success',
            confirmButtonColor: '#0066cc'
        });
    }

    openTemplateModal() {
        const list = document.getElementById('templateList');
        list.innerHTML = '';

        if (this.templates.length === 0) {
            list.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">No templates saved yet.</p>';
        } else {
            this.templates.forEach(template => {
                const item = document.createElement('div');
                item.className = 'template-item';
                item.innerHTML = `
                    <div>
                        <h5>${template.name}</h5>
                        <p>${template.criteria.length} criteria</p>
                    </div>
                    <button type="button" onclick="evalManager.loadTemplate(${template.id})">Load</button>
                `;
                list.appendChild(item);
            });
        }

        document.getElementById('templateModal').style.display = 'block';
    }

    loadTemplate(templateId) {
        const template = this.templates.find(t => t.id === templateId);
        if (template) {
            this.criteria = JSON.parse(JSON.stringify(template.criteria));
            const container = document.getElementById('criteriaContainer');
            
            if (!container) {
                console.error('Criteria container not found');
                return;
            }
            
            container.innerHTML = '';

            this.criteria.forEach((criterion, index) => {
                const itemId = `template-${templateId}-${index}`;
                const item = document.createElement('div');
                item.className = 'criteria-item';
                item.id = itemId;
                item.innerHTML = `
                    <div class="criteria-item-header">
                        <input type="text" value="${criterion.name}" class="criterion-name">
                        <input type="number" value="${criterion.weight}" min="0" max="100" class="criterion-weight">
                        <div class="criteria-item-actions">
                            <button type="button" class="btn-delete" onclick="evalManager.deleteCriteria('${itemId}')">Delete</button>
                        </div>
                    </div>
                `;
                container.appendChild(item);
            });

            const templateModal = document.getElementById('templateModal');
            if (templateModal) {
                templateModal.style.display = 'none';
            }

            Swal.fire({
                title: 'Template Loaded!',
                text: `${template.name} has been loaded.`,
                icon: 'success',
                timer: 1500,
                timerProgressBar: true
            });
        }
    }

    createNewTemplate() {
        Swal.fire({
            title: 'Create New Template',
            input: 'text',
            inputLabel: 'Template Name',
            inputPlaceholder: 'e.g., Earthquake Drill Evaluation',
            showCancelButton: true,
            confirmButtonColor: '#0066cc',
            confirmButtonText: 'Create',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                document.getElementById('templateName').value = result.value;
            }
        });
    }

    loadParticipantScoring() {
        const eventId = document.getElementById('filterScoringEvent')?.value;

        let participants = this.participants;
        if (eventId) {
            participants = participants.filter(p => p.eventId == eventId);
        }

        const tbody = document.getElementById('participantsScoringBody');
        tbody.innerHTML = '';

        participants.forEach(participant => {
            const evaluation = this.evaluations.find(e => e.participantId === participant.id);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${participant.name}</strong></td>
                <td>${this.capitalize(participant.role)}</td>
                <td><span class="status-badge status-${evaluation?.status || 'pending'}">${this.capitalize(evaluation?.status || 'pending')}</span></td>
                <td>${evaluation?.score || '--'}%</td>
                <td><span class="status-badge status-${evaluation?.result || 'pending'}">${this.capitalize(evaluation?.result || 'pending')}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" type="button" onclick="evalManager.evaluateParticipant(${participant.id})">Score</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    evaluateParticipant(participantId) {
        const participant = this.participants.find(p => p.id === participantId);
        if (!participant) {
            Swal.fire({
                title: 'Error',
                text: 'Participant not found.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        this.currentParticipantId = participantId;

        // Set participant info
        const nameHeader = document.getElementById('participantNameHeader');
        const roleInfo = document.getElementById('participantRoleInfo');
        const eventInfo = document.getElementById('eventNameInfo');
        
        if (nameHeader) nameHeader.textContent = participant.name;
        if (roleInfo) roleInfo.textContent = this.capitalize(participant.role);
        
        const event = this.events.find(e => e.id === participant.eventId);
        if (eventInfo) eventInfo.textContent = event?.name || 'N/A';

        // Build scoring criteria fields
        const scoringCriteria = document.getElementById('scoringCriteria');
        if (!scoringCriteria) {
            Swal.fire({
                title: 'Error',
                text: 'Scoring criteria container not found.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        scoringCriteria.innerHTML = '';

        if (this.criteria.length === 0) {
            scoringCriteria.innerHTML = '<p style="color: #999; text-align: center;">No criteria defined. Please set up criteria in the Criteria Setup tab.</p>';
        } else {
            this.criteria.forEach(criterion => {
                const div = document.createElement('div');
                div.className = 'scoring-criterion';
                div.innerHTML = `
                    <label>${criterion.name} (Weight: ${criterion.weight}%)</label>
                    <input type="number" min="0" max="100" class="criterion-score" data-criterion="${criterion.id}" placeholder="Score 0-100" value="0">
                `;
                scoringCriteria.appendChild(div);
            });
        }

        const scoringModal = document.getElementById('scoringModal');
        if (scoringModal) {
            scoringModal.style.display = 'block';
        }
    }

    closeScoringModal() {
        document.getElementById('scoringModal').style.display = 'none';
        document.getElementById('scoringForm').reset();
    }

    saveEvaluation(e) {
        e.preventDefault();

        if (!this.currentParticipantId) {
            Swal.fire({
                title: 'Error',
                text: 'No participant selected.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const evaluationResult = document.getElementById('evaluationResult');
        if (!evaluationResult || !evaluationResult.value) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select an evaluation result.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const scores = [];
        let totalScore = 0;
        let totalWeight = 0;

        document.querySelectorAll('.criterion-score').forEach(input => {
            const score = parseInt(input.value) || 0;
            const criterionId = input.dataset.criterion;
            const criterion = this.criteria.find(c => c.id == criterionId);

            if (criterion) {
                scores.push({ criterionId, score });
                totalScore += (score * criterion.weight);
                totalWeight += criterion.weight;
            }
        });

        if (totalWeight === 0) {
            Swal.fire({
                title: 'Error',
                text: 'No valid criteria weights found.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const finalScore = Math.round(totalScore / totalWeight);
        const result = evaluationResult.value;
        const feedback = document.getElementById('evaluatorFeedback')?.value || '';

        const evaluation = {
            participantId: this.currentParticipantId,
            eventId: this.participants.find(p => p.id === this.currentParticipantId)?.eventId,
            scores,
            score: finalScore,
            result,
            feedback,
            status: 'completed',
            evaluator: 'Admin',
            timestamp: new Date().toLocaleString()
        };

        const existingIndex = this.evaluations.findIndex(e => e.participantId === this.currentParticipantId);
        if (existingIndex >= 0) {
            this.evaluations[existingIndex] = evaluation;
        } else {
            this.evaluations.push(evaluation);
        }

        this.evaluationLogs.push({
            ...evaluation,
            id: Date.now()
        });

        Swal.fire({
            title: 'Success!',
            text: `Evaluation saved. Score: ${finalScore}% - Result: ${result.replace('-', ' ')}`,
            icon: 'success',
            confirmButtonColor: '#0066cc'
        });

        this.closeScoringModal();
        this.loadParticipantScoring();
        this.loadDashboard();
    }

    deleteEvaluation(participantId) {
        Swal.fire({
            title: 'Delete Evaluation?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.evaluations = this.evaluations.filter(e => e.participantId !== participantId);
                this.loadDashboard();
                this.loadParticipantScoring();

                Swal.fire({
                    title: 'Deleted!',
                    text: 'Evaluation has been deleted.',
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    }

    loadPerformanceAnalysis() {
        const eventId = document.getElementById('analyticsEventFilter')?.value;

        const eventEvaluations = eventId 
            ? this.evaluations.filter(e => e.eventId == eventId)
            : this.evaluations;

        if (eventEvaluations.length === 0) {
            document.getElementById('avgScoreAnalytics').textContent = '0%';
            document.getElementById('highestScoreAnalytics').textContent = '0%';
            document.getElementById('lowestScoreAnalytics').textContent = '0%';
            document.getElementById('passRateAnalytics').textContent = '0%';
            return;
        }

        const scores = eventEvaluations.map(e => e.score);
        const avgScore = Math.round(scores.reduce((a, b) => a + b, 0) / scores.length);
        const highestScore = Math.max(...scores);
        const lowestScore = Math.min(...scores);
        const passCount = eventEvaluations.filter(e => e.result === 'passed').length;
        const passRate = Math.round((passCount / eventEvaluations.length) * 100);

        document.getElementById('avgScoreAnalytics').textContent = avgScore + '%';
        document.getElementById('highestScoreAnalytics').textContent = highestScore + '%';
        document.getElementById('lowestScoreAnalytics').textContent = lowestScore + '%';
        document.getElementById('passRateAnalytics').textContent = passRate + '%';

        document.getElementById('criterionBreakdown').innerHTML = this.criteria
            .map(c => `<p>${c.name}: ${Math.round(Math.random() * 100)}%</p>`)
            .join('');

        const roleData = {};
        eventEvaluations.forEach(evaluation => {
            const participant = this.participants.find(p => p.id === eval.participantId);
            if (participant) {
                if (!roleData[participant.role]) {
                    roleData[participant.role] = { scores: [] };
                }
                roleData[participant.role].scores.push(eval.score);
            }
        });

        document.getElementById('roleBreakdown').innerHTML = Object.entries(roleData)
            .map(([role, data]) => {
                const avg = Math.round(data.scores.reduce((a, b) => a + b, 0) / data.scores.length);
                return `<p>${this.capitalize(role)}: ${avg}%</p>`;
            })
            .join('');
    }

    exportPerformanceReport() {
        Swal.fire({
            title: 'Export Report',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select export format:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button type="button" onclick="evalManager.performExport('pdf')" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📄 PDF</button>
                        <button type="button" onclick="evalManager.performExport('csv')" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
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
                text: `Performance report exported as ${format.toUpperCase()}.`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    loadEvaluationLogs() {
        this.displayLogs(this.evaluationLogs);
    }

    displayLogs(logs) {
        const tbody = document.getElementById('logsTableBody');
        tbody.innerHTML = '';

        if (logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No evaluation logs found.</td></tr>';
            return;
        }

        logs.forEach(log => {
            const participant = this.participants.find(p => p.id === log.participantId);
            const event = this.events.find(e => e.id === log.eventId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${participant?.name || 'N/A'}</td>
                <td>${event?.name || 'N/A'}</td>
                <td>${log.evaluator}</td>
                <td>${log.score}%</td>
                <td><span class="status-badge status-${log.result}">${this.capitalize(log.result)}</span></td>
                <td>${log.timestamp}</td>
                <td><span class="status-badge status-${log.status}">${this.capitalize(log.status)}</span></td>
            `;
            tbody.appendChild(row);
        });
    }

    filterLogs() {
        const searchInput = document.getElementById('searchEvaluationLogs');
        const evaluatorSelect = document.getElementById('filterEvaluatorLogs');
        
        const search = (searchInput?.value || '').toLowerCase();
        const evaluator = evaluatorSelect?.value || '';

        const filtered = this.evaluationLogs.filter(log => {
            const participant = this.participants.find(p => p.id === log.participantId);
            const participantName = participant?.name || '';
            
            return (
                (!search || participantName.toLowerCase().includes(search)) &&
                (!evaluator || log.evaluator === evaluator)
            );
        });

        this.displayLogs(filtered);
    }

    capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ');
    }
}

// Initialize on page load
let evalManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        evalManager = new EvaluationScoringManager();
    });
} else {
    evalManager = new EvaluationScoringManager();
}
