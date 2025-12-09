class CertificateManager {
    constructor() {
        this.templates = [];
        this.certificates = [];
        this.events = [];
        this.participants = [];
        this.issueHistory = [];
        this.currentTemplateId = null;
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

        // Dashboard
        // (loads automatically)

        // Template Management
        document.getElementById('uploadTemplateBtn').addEventListener('click', () => this.openTemplateModal());
        document.getElementById('templateForm').addEventListener('submit', (e) => this.saveTemplate(e));
        document.getElementById('closeTemplateBtn').addEventListener('click', () => this.closeTemplateModal());

        // Generation
        document.getElementById('genEvent').addEventListener('change', () => this.loadEligibleParticipants());
        document.getElementById('genTemplate').addEventListener('change', () => this.updatePreview());
        document.getElementById('previewCertBtn').addEventListener('click', () => this.previewCertificate());
        document.getElementById('generateBulkBtn').addEventListener('click', () => this.generateBulkCertificates());

        // Issuance
        document.getElementById('filterIssueEvent').addEventListener('change', () => this.loadIssuance());
        document.getElementById('filterIssueStatus').addEventListener('change', () => this.loadIssuance());

        // History
        document.getElementById('searchCertHistory').addEventListener('input', () => this.filterHistory());
        document.getElementById('historyStartDate').addEventListener('change', () => this.filterHistory());
        document.getElementById('historyEndDate').addEventListener('change', () => this.filterHistory());
        document.getElementById('exportLogsBtn').addEventListener('click', () => this.exportLogs());

        // Validation
        document.getElementById('validateBtn').addEventListener('click', () => this.validateCertificate());
        document.getElementById('enableQRValidation').addEventListener('change', (e) => this.toggleQRValidation(e.target.checked));

        // File Upload
        const templateUploadArea = document.getElementById('templateUploadArea');
        if (templateUploadArea) {
            templateUploadArea.addEventListener('click', () => {
                templateUploadArea.querySelector('input').click();
            });
            templateUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                templateUploadArea.style.background = '#e3f2fd';
            });
            templateUploadArea.addEventListener('dragleave', () => {
                templateUploadArea.style.background = '';
            });
        }

        // Modal Close
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal').style.display = 'none';
            });
        });

        // Issue Method
        document.getElementById('issueMethod').addEventListener('change', (e) => {
            const emailNote = document.getElementById('emailNote');
            if (e.target.value === 'email') {
                emailNote.style.display = 'block';
            } else {
                emailNote.style.display = 'none';
            }
        });

        document.getElementById('issueForm').addEventListener('submit', (e) => this.issueCertificates(e));
        document.getElementById('closeIssueBtn').addEventListener('click', () => this.closeIssueModal());
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

        if (tabName === 'dashboard') {
            this.loadDashboard();
        } else if (tabName === 'templates') {
            this.loadTemplates();
        } else if (tabName === 'generation') {
            this.loadGenerationForm();
        } else if (tabName === 'issuance') {
            this.loadIssuance();
        } else if (tabName === 'history') {
            this.loadHistory();
        }
    }

    loadData() {
        this.events = [
            { id: 1, name: 'Earthquake Evacuation Drill', type: 'simulation' },
            { id: 2, name: 'Fire Safety Training', type: 'training' },
            { id: 3, name: 'First Aid Certification', type: 'training' }
        ];

        this.participants = [
            { id: 1, name: 'John Dela Cruz', email: 'john@example.com', eventId: 1, completed: true, score: 85 },
            { id: 2, name: 'Maria Santos', email: 'maria@example.com', eventId: 1, completed: true, score: 92 },
            { id: 3, name: 'Juan Rizal', email: 'juan@example.com', eventId: 1, completed: false, score: null }
        ];

        this.templates = [
            { id: 1, name: 'Standard Completion', type: 'both', active: true },
            { id: 2, name: 'Excellence Certificate', type: 'training', active: true },
            { id: 3, name: 'Simulation Completion', type: 'simulation', active: false }
        ];

        this.loadEventSelects();
        this.loadDashboard();
    }

    loadEventSelects() {
        const genEvent = document.getElementById('genEvent');
        const filterEvent = document.getElementById('filterIssueEvent');
        const filterHEvent = document.getElementById('filterEventHistory');

        const selects = [genEvent, filterEvent, filterHEvent];
        selects.forEach(select => {
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

        const genTemplate = document.getElementById('genTemplate');
        if (genTemplate) {
            genTemplate.innerHTML = '<option value="">Select Template</option>';
            this.templates.filter(t => t.active).forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = template.name;
                genTemplate.appendChild(option);
            });
        }

        const maintResource = document.getElementById('maintResource');
        if (maintResource) {
            maintResource.innerHTML = '<option value="">Select Resource</option>';
            this.templates.forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = template.name;
                maintResource.appendChild(option);
            });
        }
    }

    loadDashboard() {
        const totalIssued = this.certificates.filter(c => c.status === 'issued').length;
        const pending = this.certificates.filter(c => c.status === 'pending').length;
        const activeTemplates = this.templates.filter(t => t.active).length;
        const thisMonth = this.certificates.filter(c => {
            const certDate = new Date(c.issuedDate);
            const now = new Date();
            return certDate.getMonth() === now.getMonth() && certDate.getFullYear() === now.getFullYear();
        }).length;

        document.getElementById('totalIssued').textContent = totalIssued;
        document.getElementById('pendingCerts').textContent = pending;
        document.getElementById('activeTemplates').textContent = activeTemplates;
        document.getElementById('thisMonth').textContent = thisMonth;

        this.displayRecentCertificates();
    }

    displayRecentCertificates() {
        const tbody = document.getElementById('recentCertsBody');
        tbody.innerHTML = '';

        const recent = this.certificates.slice(-5).reverse();
        if (recent.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #999;">No certificates issued yet.</td></tr>';
            return;
        }

        recent.forEach(cert => {
            const participant = this.participants.find(p => p.id === cert.participantId);
            const event = this.events.find(e => e.id === cert.eventId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${participant?.name || 'N/A'}</td>
                <td>${event?.name || 'N/A'}</td>
                <td>${cert.certificateId}</td>
                <td>${cert.issuedDate}</td>
                <td><span class="status-badge status-${cert.status}">${this.capitalize(cert.status)}</span></td>
            `;
            tbody.appendChild(row);
        });
    }

    openTemplateModal() {
        document.getElementById('templateModal').style.display = 'block';
    }

    closeTemplateModal() {
        document.getElementById('templateModal').style.display = 'none';
        document.getElementById('templateForm').reset();
    }

    saveTemplate(e) {
        e.preventDefault();

        const name = document.getElementById('templateName').value.trim();
        const type = document.getElementById('templateType').value;
        const active = document.querySelector('input[name="active"]').checked;

        if (!name || !type) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const template = {
            id: Math.max(...this.templates.map(t => t.id), 0) + 1,
            name,
            type,
            active
        };

        this.templates.push(template);

        Swal.fire({
            title: 'Success!',
            text: 'Template uploaded successfully.',
            icon: 'success',
            confirmButtonColor: '#0066cc'
        }).then(() => {
            this.closeTemplateModal();
            this.loadTemplates();
            this.loadEventSelects();
        });
    }

    loadTemplates() {
        const grid = document.getElementById('templatesGrid');
        grid.innerHTML = '';

        this.templates.forEach(template => {
            const card = document.createElement('div');
            card.className = 'template-card';
            card.innerHTML = `
                <div class="template-preview">
                    [Certificate Template Preview]
                </div>
                <div class="template-info">
                    <h4>${template.name}</h4>
                    <p>Type: ${this.capitalize(template.type)}</p>
                    <p>Status: ${template.active ? '✓ Active' : '○ Inactive'}</p>
                </div>
                <div class="template-actions">
                    <button type="button" class="btn-primary" onclick="certManager.editTemplate(${template.id})">Edit</button>
                    <button type="button" class="btn-secondary" onclick="certManager.toggleTemplate(${template.id})">
                        ${template.active ? 'Deactivate' : 'Activate'}
                    </button>
                    <button type="button" class="btn-delete" onclick="certManager.deleteTemplate(${template.id})">Delete</button>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    editTemplate(templateId) {
        Swal.fire({
            title: 'Edit Template',
            text: 'Edit functionality coming soon',
            icon: 'info',
            confirmButtonColor: '#0066cc'
        });
    }

    toggleTemplate(templateId) {
        const template = this.templates.find(t => t.id === templateId);
        if (template) {
            template.active = !template.active;
            this.loadTemplates();
        }
    }

    deleteTemplate(templateId) {
        Swal.fire({
            title: 'Delete Template?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.templates = this.templates.filter(t => t.id !== templateId);
                this.loadTemplates();
                
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Template has been deleted.',
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    }

    loadGenerationForm() {
        document.getElementById('eligibleCount').textContent = '0';
    }

    loadEligibleParticipants() {
        const eventId = parseInt(document.getElementById('genEvent').value);
        const eligible = this.participants.filter(p => p.eventId == eventId && p.completed && p.score);
        document.getElementById('eligibleCount').textContent = eligible.length;
    }

    updatePreview() {
        const eventId = parseInt(document.getElementById('genEvent').value);
        const templateId = parseInt(document.getElementById('genTemplate').value);

        if (eventId && templateId) {
            const event = this.events.find(e => e.id === eventId);
            const template = this.templates.find(t => t.id === templateId);
            
            document.getElementById('certPreview').innerHTML = `
                <div style="background: #f0f0f0; padding: 40px; border-radius: 5px; min-height: 300px;">
                    <h3>${template.name}</h3>
                    <p>For: ${event.name}</p>
                    <p style="color: #999; font-size: 12px;">Certificate will include participant name, completion date, and certificate ID</p>
                </div>
            `;
        }
    }

    previewCertificate() {
        Swal.fire({
            title: 'Certificate Preview',
            html: `<div style="background: #f0f0f0; padding: 40px; border-radius: 5px; min-height: 400px;">
                        <h2 style="margin: 20px 0;">Certificate of Completion</h2>
                        <p style="font-size: 16px;">This is to certify that</p>
                        <h3 style="margin: 20px 0;">John Dela Cruz</h3>
                        <p style="font-size: 14px;">has successfully completed</p>
                        <h4>Earthquake Evacuation Drill</h4>
                        <p style="margin-top: 30px; font-size: 12px; color: #666;">
                            Certificate ID: CERT-2024-001<br>
                            Issued: ${new Date().toLocaleDateString()}
                        </p>
                    </div>`,
            confirmButtonColor: '#0066cc',
            confirmButtonText: 'Close'
        });
    }

    generateBulkCertificates() {
        const eventId = parseInt(document.getElementById('genEvent').value);
        const templateId = parseInt(document.getElementById('genTemplate').value);

        if (!eventId || !templateId) {
            Swal.fire({
                title: 'Selection Required',
                text: 'Please select an event and template first.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const eligible = this.participants.filter(p => p.eventId == eventId && p.completed && p.score);
        if (eligible.length === 0) {
            Swal.fire({
                title: 'No Eligible Participants',
                text: 'There are no eligible participants for this event.',
                icon: 'info',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        // Show progress
        document.getElementById('generationProgress').style.display = 'block';
        let generated = 0;

        const interval = setInterval(() => {
            generated++;
            const progress = (generated / eligible.length) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
            document.getElementById('progressText').textContent = `${generated} / ${eligible.length} generated`;

            if (generated >= eligible.length) {
                clearInterval(interval);
                
                // Generate certificates
                eligible.forEach(participant => {
                    const cert = {
                        id: Date.now() + Math.random(),
                        certificateId: `CERT-${new Date().getFullYear()}-${Math.floor(Math.random() * 10000)}`,
                        participantId: participant.id,
                        eventId: eventId,
                        templateId: templateId,
                        issuedDate: new Date().toLocaleDateString(),
                        status: 'pending'
                    };
                    this.certificates.push(cert);
                });

                Swal.fire({
                    title: 'Generated!',
                    text: `${eligible.length} certificates generated successfully.`,
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                }).then(() => {
                    document.getElementById('generationProgress').style.display = 'none';
                    this.showIssueOptions();
                });
            }
        }, 100);
    }

    showIssueOptions() {
        document.getElementById('issueModal').style.display = 'block';
    }

    closeIssueModal() {
        document.getElementById('issueModal').style.display = 'none';
        document.getElementById('issueForm').reset();
    }

    issueCertificates(e) {
        e.preventDefault();

        const method = document.getElementById('issueMethod').value;

        if (method === 'email') {
            this.certificates.forEach(cert => {
                if (cert.status === 'pending') {
                    cert.status = 'issued';
                    cert.issuedDate = new Date().toLocaleDateString();
                    this.issueHistory.push({
                        ...cert,
                        issuedBy: 'Admin',
                        issuedAt: new Date().toLocaleString()
                    });
                }
            });

            Swal.fire({
                title: 'Certificates Emailed!',
                text: 'Certificates have been sent to all participants.',
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        } else if (method === 'download') {
            Swal.fire({
                title: 'Download Started',
                text: 'PDF download will begin shortly.',
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }

        this.closeIssueModal();
        this.loadIssuance();
    }

    loadIssuance() {
        const eventId = document.getElementById('filterIssueEvent').value;
        const status = document.getElementById('filterIssueStatus').value;

        let filtered = this.certificates;
        if (eventId) {
            filtered = filtered.filter(c => c.eventId == eventId);
        }
        if (status) {
            filtered = filtered.filter(c => c.status === status);
        }

        const tbody = document.getElementById('issuanceTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">No certificates found.</td></tr>';
            return;
        }

        filtered.forEach(cert => {
            const participant = this.participants.find(p => p.id === cert.participantId);
            const event = this.events.find(e => e.id === cert.eventId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${participant?.name || 'N/A'}</td>
                <td>${event?.name || 'N/A'}</td>
                <td>${cert.certificateId}</td>
                <td>${cert.issuedDate}</td>
                <td><span class="status-badge status-${cert.status}">${this.capitalize(cert.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        ${cert.status === 'pending' ? `<button class="btn-issue" type="button" onclick="certManager.issueCert(${cert.id})">Issue</button>` : ''}
                        <button class="btn-download" type="button" onclick="certManager.downloadCert(${cert.id})">Download</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    issueCert(certId) {
        const cert = this.certificates.find(c => c.id === certId);
        if (cert) {
            cert.status = 'issued';
            this.loadIssuance();
        }
    }

    downloadCert(certId) {
        Swal.fire({
            title: 'Downloading...',
            text: 'Certificate PDF is being prepared.',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        setTimeout(() => {
            Swal.fire({
                title: 'Downloaded!',
                text: 'Certificate has been downloaded.',
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    loadHistory() {
        this.displayHistory(this.issueHistory);
    }

    displayHistory(history) {
        const tbody = document.getElementById('historyTableBody');
        tbody.innerHTML = '';

        if (history.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No history found.</td></tr>';
            return;
        }

        history.forEach(record => {
            const participant = this.participants.find(p => p.id === record.participantId);
            const event = this.events.find(e => e.id === record.eventId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${participant?.name || 'N/A'}</td>
                <td>${event?.name || 'N/A'}</td>
                <td>${record.certificateId}</td>
                <td>${record.issuedDate}</td>
                <td>${record.issuedBy}</td>
                <td><span class="status-badge status-${record.status}">${this.capitalize(record.status)}</span></td>
                <td>
                    <button class="btn-download" type="button" onclick="certManager.downloadCert(${record.id})">Download</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    filterHistory() {
        const search = document.getElementById('searchCertHistory').value.toLowerCase();
        const startDate = document.getElementById('historyStartDate').value;
        const endDate = document.getElementById('historyEndDate').value;

        const filtered = this.issueHistory.filter(record => {
            const participant = this.participants.find(p => p.id === record.participantId);
            const matchesSearch = !search || participant?.name.toLowerCase().includes(search) || record.certificateId.includes(search);
            
            const recordDate = new Date(record.issuedDate);
            const start = startDate ? new Date(startDate) : null;
            const end = endDate ? new Date(endDate) : null;
            
            const matchesDate = (!start || recordDate >= start) && (!end || recordDate <= end);
            
            return matchesSearch && matchesDate;
        });

        this.displayHistory(filtered);
    }

    exportLogs() {
        Swal.fire({
            title: 'Export Logs',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select format:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button type="button" onclick="certManager.performExport('csv')" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
                        <button type="button" onclick="certManager.performExport('excel')" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📈 Excel</button>
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
            text: 'Preparing file...',
            icon: 'info',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        setTimeout(() => {
            Swal.fire({
                title: 'Export Complete!',
                text: `Logs exported as ${format.toUpperCase()}.`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    validateCertificate() {
        const certId = document.getElementById('validateCertId').value.trim();

        if (!certId) {
            Swal.fire({
                title: 'Error',
                text: 'Please enter a Certificate ID.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const cert = this.certificates.find(c => c.certificateId === certId);

        if (cert) {
            const participant = this.participants.find(p => p.id === cert.participantId);
            const event = this.events.find(e => e.id === cert.eventId);
            
            document.getElementById('validationResult').style.display = 'block';
            document.getElementById('validationCard').className = 'validation-card';
            document.getElementById('validationCard').innerHTML = `
                <h4>✓ Valid Certificate</h4>
                <div class="validation-details">
                    <p><strong>Participant:</strong> ${participant?.name || 'N/A'}</p>
                    <p><strong>Training/Event:</strong> ${event?.name || 'N/A'}</p>
                    <p><strong>Certificate ID:</strong> ${cert.certificateId}</p>
                    <p><strong>Issued Date:</strong> ${cert.issuedDate}</p>
                    <p><strong>Status:</strong> ${this.capitalize(cert.status)}</p>
                </div>
            `;
        } else {
            document.getElementById('validationResult').style.display = 'block';
            document.getElementById('validationCard').className = 'validation-card invalid';
            document.getElementById('validationCard').innerHTML = `
                <h4>✗ Invalid Certificate</h4>
                <p>Certificate ID not found in the system.</p>
            `;
        }
    }

    toggleQRValidation(enabled) {
        Swal.fire({
            title: enabled ? 'QR Validation Enabled' : 'QR Validation Disabled',
            text: enabled ? 'Participants can now scan QR codes to verify certificates.' : 'QR validation has been disabled.',
            icon: 'info',
            confirmButtonColor: '#0066cc'
        });
    }

    capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ');
    }
}

// Initialize on page load
let certManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        certManager = new CertificateManager();
    });
} else {
    certManager = new CertificateManager();
}
