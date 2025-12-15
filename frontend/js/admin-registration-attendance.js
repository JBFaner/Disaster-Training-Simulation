class RegistrationAttendanceManager {
    constructor() {
        this.participants = [];
        this.registrations = [];
        this.attendance = [];
        this.activities = [];
        this.currentParticipantId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadParticipants();
        this.loadRegistrations();
        this.loadAttendance();
        this.loadActivities();
        this.loadEvents();
    }

    setupEventListeners() {
        // Tab Navigation
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Participants Tab
        document.getElementById('searchParticipants').addEventListener('input', () => this.filterParticipants());
        document.getElementById('filterRole').addEventListener('change', () => this.filterParticipants());
        document.getElementById('filterDepartment').addEventListener('change', () => this.filterParticipants());
        document.getElementById('filterStatus').addEventListener('change', () => this.filterParticipants());

        // Registrations Tab
        document.getElementById('filterEventRegistration').addEventListener('change', () => this.filterRegistrations());
        document.getElementById('filterApprovalStatus').addEventListener('change', () => this.filterRegistrations());
        document.getElementById('enableAutoApproval').addEventListener('change', (e) => this.toggleAutoApproval(e.target.checked));

        // Attendance Tab
        document.getElementById('filterAttendanceEvent').addEventListener('change', () => this.loadAttendanceData());
        document.getElementById('attendanceDate').addEventListener('change', () => this.loadAttendanceData());
        document.getElementById('checkInQRBtn').addEventListener('click', () => this.openQRModal());
        document.getElementById('checkInCodeBtn').addEventListener('click', () => this.openCodeModal());
        document.getElementById('processQRBtn').addEventListener('click', () => this.processQRCode());
        document.getElementById('submitCodeBtn').addEventListener('click', () => this.processAttendanceCode());

        // History Tab
        document.getElementById('searchParticipantHistory').addEventListener('input', () => this.filterHistory());
        document.getElementById('filterEventHistory').addEventListener('change', () => this.filterHistory());

        // Analytics Tab
        document.getElementById('analyticsEventFilter').addEventListener('change', () => this.loadAnalyticsData());
        document.getElementById('analyticsStartDate').addEventListener('change', () => this.loadAnalyticsData());
        document.getElementById('analyticsEndDate').addEventListener('change', () => this.loadAnalyticsData());
        document.getElementById('exportAnalyticsBtn').addEventListener('click', () => this.exportAnalytics());

        // Edit Modal
        document.getElementById('editParticipantForm').addEventListener('submit', (e) => this.saveParticipantChanges(e));
        document.getElementById('closeEditBtn').addEventListener('click', () => this.closeEditModal());

        // Modal Close
        document.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal').style.display = 'none';
            });
        });

        // Close modal on outside click
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
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

    loadParticipants() {
        this.participants = [
            {
                id: 1,
                name: 'John Dela Cruz',
                role: 'staff',
                department: 'ops',
                contact: '09123456789',
                email: 'john@example.com',
                regDate: '2024-01-10',
                status: 'active'
            },
            {
                id: 2,
                name: 'Maria Santos',
                role: 'volunteer',
                department: 'logistics',
                contact: '09187654321',
                email: 'maria@example.com',
                regDate: '2024-01-15',
                status: 'active'
            },
            {
                id: 3,
                name: 'Juan Rizal',
                role: 'student',
                department: 'medical',
                contact: '09198765432',
                email: 'juan@example.com',
                regDate: '2024-01-20',
                status: 'inactive'
            }
        ];
        this.displayParticipants();
    }

    loadRegistrations() {
        this.registrations = [
            {
                id: 1,
                participantId: 1,
                participantName: 'John Dela Cruz',
                eventId: 1,
                eventName: 'Earthquake Evacuation Drill',
                regDate: '2024-01-25',
                status: 'approved',
                registered: 45,
                capacity: 50
            },
            {
                id: 2,
                participantId: 2,
                participantName: 'Maria Santos',
                eventId: 2,
                eventName: 'Fire Safety Exercise',
                regDate: '2024-01-26',
                status: 'pending',
                registered: 38,
                capacity: 50
            },
            {
                id: 3,
                participantId: 3,
                participantName: 'Juan Rizal',
                eventId: 1,
                eventName: 'Earthquake Evacuation Drill',
                regDate: '2024-01-27',
                status: 'auto-approved',
                registered: 45,
                capacity: 50
            }
        ];
        this.displayRegistrations();
    }

    loadAttendance() {
        this.attendance = [
            {
                id: 1,
                participantId: 1,
                participantName: 'John Dela Cruz',
                role: 'staff',
                eventId: 1,
                checkInTime: '09:00',
                checkOutTime: '11:30',
                status: 'present'
            },
            {
                id: 2,
                participantId: 2,
                participantName: 'Maria Santos',
                role: 'volunteer',
                eventId: 1,
                checkInTime: '09:15',
                checkOutTime: '11:45',
                status: 'late'
            },
            {
                id: 3,
                participantId: 3,
                participantName: 'Juan Rizal',
                role: 'student',
                eventId: 1,
                checkInTime: null,
                checkOutTime: null,
                status: 'absent'
            }
        ];
        this.displayAttendance();
        this.updateAttendanceSummary();
    }

    loadActivities() {
        this.activities = [
            {
                participantId: 1,
                participantName: 'John Dela Cruz',
                eventId: 1,
                eventName: 'Earthquake Evacuation Drill',
                date: '2024-01-25',
                status: 'completed',
                attended: true,
                completionCount: 5
            },
            {
                participantId: 2,
                participantName: 'Maria Santos',
                eventId: 2,
                eventName: 'Fire Safety Exercise',
                date: '2024-01-26',
                status: 'completed',
                attended: true,
                completionCount: 3
            }
        ];
        this.displayHistory();
    }

    loadEvents() {
        const eventSelects = [
            document.getElementById('filterEventRegistration'),
            document.getElementById('filterAttendanceEvent'),
            document.getElementById('filterEventHistory'),
            document.getElementById('analyticsEventFilter')
        ];

        const events = [
            { id: 1, name: 'Earthquake Evacuation Drill' },
            { id: 2, name: 'Fire Safety Exercise' },
            { id: 3, name: 'Flood Response Drill' }
        ];

        eventSelects.forEach(select => {
            if (select) {
                select.innerHTML = '<option value="">Select Event</option>';
                events.forEach(event => {
                    const option = document.createElement('option');
                    option.value = event.id;
                    option.textContent = event.name;
                    select.appendChild(option);
                });
            }
        });
    }

    displayParticipants() {
        const tbody = document.getElementById('participantsTableBody');
        tbody.innerHTML = '';

        if (this.participants.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No participants found.</td></tr>';
            return;
        }

        this.participants.forEach(participant => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${participant.name}</strong></td>
                <td>${this.capitalize(participant.role)}</td>
                <td>${this.capitalize(participant.department)}</td>
                <td>${participant.contact}</td>
                <td>${participant.regDate}</td>
                <td><span class="status-badge status-${participant.status}">${this.capitalize(participant.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="regManager.editParticipant(${participant.id})">Edit</button>
                        <button class="btn-delete" onclick="regManager.deleteParticipant(${participant.id})">Deactivate</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    displayRegistrations() {
        const tbody = document.getElementById('registrationsTableBody');
        tbody.innerHTML = '';

        if (this.registrations.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No registrations found.</td></tr>';
            return;
        }

        this.registrations.forEach(reg => {
            const row = document.createElement('tr');
            const statusBadge = reg.status.replace('-', ' ');
            row.innerHTML = `
                <td><strong>${reg.participantName}</strong></td>
                <td>${reg.eventName}</td>
                <td>${reg.regDate}</td>
                <td><span class="status-badge status-${reg.status}">${this.capitalize(statusBadge)}</span></td>
                <td>${reg.registered}/${reg.capacity}</td>
                <td>${reg.capacity - reg.registered}</td>
                <td>
                    <div class="action-buttons">
                        ${reg.status === 'pending' ? `
                            <button class="btn-approve" onclick="regManager.approveRegistration(${reg.id})">Approve</button>
                            <button class="btn-decline" onclick="regManager.declineRegistration(${reg.id})">Decline</button>
                        ` : ''}
                        <button class="btn-delete" onclick="regManager.deleteRegistration(${reg.id})">Remove</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    displayAttendance() {
        const tbody = document.getElementById('attendanceTableBody');
        tbody.innerHTML = '';

        if (this.attendance.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">No attendance records found.</td></tr>';
            return;
        }

        this.attendance.forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${record.participantName}</strong></td>
                <td>${this.capitalize(record.role)}</td>
                <td>${record.checkInTime || '--'}</td>
                <td>${record.checkOutTime || '--'}</td>
                <td><span class="status-badge status-${record.status}">${this.capitalize(record.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="regManager.editAttendance(${record.id})">Edit</button>
                        <button class="btn-delete" onclick="regManager.deleteAttendance(${record.id})">Remove</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    displayHistory() {
        const tbody = document.getElementById('historyTableBody');
        tbody.innerHTML = '';

        if (this.activities.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">No activity history found.</td></tr>';
            return;
        }

        this.activities.forEach(activity => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${activity.participantName}</strong></td>
                <td>${activity.eventName}</td>
                <td>${activity.date}</td>
                <td><span class="status-badge status-${activity.attended ? 'present' : 'absent'}">${activity.attended ? 'Present' : 'Absent'}</span></td>
                <td><span class="status-badge status-${activity.status}">${this.capitalize(activity.status)}</span></td>
                <td>${activity.date}</td>
            `;
            tbody.appendChild(row);
        });
    }

    editParticipant(participantId) {
        const participant = this.participants.find(p => p.id === participantId);
        if (participant) {
            this.currentParticipantId = participantId;
            document.getElementById('editParticipantName').value = participant.name;
            document.getElementById('editParticipantRole').value = participant.role;
            document.getElementById('editParticipantDept').value = participant.department;
            document.getElementById('editParticipantContact').value = participant.contact;
            document.getElementById('editParticipantEmail').value = participant.email;
            document.getElementById('editParticipantActive').checked = participant.status === 'active';

            document.getElementById('editParticipantModal').style.display = 'block';
        }
    }

    closeEditModal() {
        document.getElementById('editParticipantModal').style.display = 'none';
    }

    saveParticipantChanges(e) {
        e.preventDefault();

        if (!this.currentParticipantId) return;

        const participant = this.participants.find(p => p.id === this.currentParticipantId);
        if (participant) {
            participant.role = document.getElementById('editParticipantRole').value;
            participant.department = document.getElementById('editParticipantDept').value;
            participant.contact = document.getElementById('editParticipantContact').value;
            participant.email = document.getElementById('editParticipantEmail').value;
            participant.status = document.getElementById('editParticipantActive').checked ? 'active' : 'inactive';

            this.displayParticipants();

            Swal.fire({
                title: 'Success!',
                text: 'Participant information updated successfully.',
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });

            this.closeEditModal();
        }
    }

    deleteParticipant(participantId) {
        const participant = this.participants.find(p => p.id === participantId);
        if (participant) {
            Swal.fire({
                title: 'Deactivate Participant?',
                html: `<p>Are you sure you want to deactivate <strong>${participant.name}</strong>?</p><p style="font-size: 12px; color: #666;">They will not be able to register for future events.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, deactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    participant.status = 'inactive';
                    this.displayParticipants();
                    
                    Swal.fire({
                        title: 'Deactivated!',
                        text: 'Participant has been deactivated.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    approveRegistration(registrationId) {
        const reg = this.registrations.find(r => r.id === registrationId);
        if (reg) {
            reg.status = 'approved';
            this.displayRegistrations();

            Swal.fire({
                title: 'Approved!',
                html: `<p><strong>${reg.participantName}</strong> has been approved for <strong>${reg.eventName}</strong>.</p><p style="font-size: 12px; margin-top: 10px;">📧 Notification will be sent to participant.</p>`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }
    }

    declineRegistration(registrationId) {
        const reg = this.registrations.find(r => r.id === registrationId);
        if (reg) {
            Swal.fire({
                title: 'Decline Registration?',
                input: 'textarea',
                inputLabel: 'Reason for declining (optional)',
                inputPlaceholder: 'Enter reason...',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Decline',
                cancelButtonText: 'Cancel',
                inputValidator: () => {
                    return false;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    reg.status = 'declined';
                    this.displayRegistrations();

                    Swal.fire({
                        title: 'Declined!',
                        text: 'Registration has been declined. Notification sent to participant.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    deleteRegistration(registrationId) {
        Swal.fire({
            title: 'Remove Registration?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.registrations = this.registrations.filter(r => r.id !== registrationId);
                this.displayRegistrations();

                Swal.fire({
                    title: 'Removed!',
                    text: 'Registration has been removed.',
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    }

    openQRModal() {
        document.getElementById('qrCheckInModal').style.display = 'block';
        document.getElementById('qrInput').focus();
    }

    openCodeModal() {
        document.getElementById('codeCheckInModal').style.display = 'block';
        document.getElementById('attendanceCodeInput').focus();
    }

    processQRCode() {
        const qrData = document.getElementById('qrInput').value.trim();

        if (!qrData) {
            Swal.fire({
                title: 'Error',
                text: 'Please scan or enter a QR code.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const participantId = parseInt(qrData);
        const participant = this.participants.find(p => p.id === participantId);

        if (participant) {
            const checkInTime = new Date().toLocaleTimeString();
            Swal.fire({
                title: '✅ Check-in Successful!',
                html: `<p><strong>${participant.name}</strong></p><p>Checked in at ${checkInTime}</p>`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });

            document.getElementById('qrInput').value = '';
            document.getElementById('qrCheckInModal').style.display = 'none';
            this.updateAttendanceSummary();
        } else {
            Swal.fire({
                title: 'Not Found',
                text: 'Participant not found. Please check QR code.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
        }
    }

    processAttendanceCode() {
        const code = document.getElementById('attendanceCodeInput').value.trim();
        const name = document.getElementById('participantNameInput').value.trim();

        if (!code) {
            Swal.fire({
                title: 'Error',
                text: 'Please enter attendance code.',
                icon: 'error',
                confirmButtonColor: '#0066cc'
            });
            return;
        }

        const checkInTime = new Date().toLocaleTimeString();
        Swal.fire({
            title: '✅ Check-in Successful!',
            html: `<p><strong>${name || 'Participant'}</strong></p><p>Checked in at ${checkInTime}</p>`,
            icon: 'success',
            confirmButtonColor: '#0066cc'
        });

        document.getElementById('attendanceCodeInput').value = '';
        document.getElementById('participantNameInput').value = '';
        document.getElementById('codeCheckInModal').style.display = 'none';
        this.updateAttendanceSummary();
    }

    updateAttendanceSummary() {
        const present = this.attendance.filter(a => a.status === 'present').length;
        const late = this.attendance.filter(a => a.status === 'late').length;
        const absent = this.attendance.filter(a => a.status === 'absent').length;
        const total = this.attendance.length;
        const rate = total > 0 ? Math.round(((present + late) / total) * 100) : 0;

        document.getElementById('presentCount').textContent = present;
        document.getElementById('lateCount').textContent = late;
        document.getElementById('absentCount').textContent = absent;
        document.getElementById('attendanceRate').textContent = rate + '%';
    }

    loadAttendanceData() {
        this.updateAttendanceSummary();
    }

    editAttendance(attendanceId) {
        const record = this.attendance.find(a => a.id === attendanceId);
        if (record) {
            Swal.fire({
                title: 'Edit Attendance',
                html: `
                    <div style="text-align: left;">
                        <label style="display: block; margin-bottom: 15px;">
                            <strong>Status:</strong>
                            <select id="attendanceStatusEdit" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-top: 5px;">
                                <option value="present" ${record.status === 'present' ? 'selected' : ''}>Present</option>
                                <option value="late" ${record.status === 'late' ? 'selected' : ''}>Late</option>
                                <option value="absent" ${record.status === 'absent' ? 'selected' : ''}>Absent</option>
                            </select>
                        </label>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#0066cc',
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    record.status = document.getElementById('attendanceStatusEdit').value;
                    this.displayAttendance();
                    this.updateAttendanceSummary();
                    
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Attendance record has been updated.',
                        icon: 'success',
                        confirmButtonColor: '#0066cc'
                    });
                }
            });
        }
    }

    deleteAttendance(attendanceId) {
        Swal.fire({
            title: 'Remove Attendance Record?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.attendance = this.attendance.filter(a => a.id !== attendanceId);
                this.displayAttendance();
                this.updateAttendanceSummary();

                Swal.fire({
                    title: 'Removed!',
                    text: 'Attendance record has been removed.',
                    icon: 'success',
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    }

    filterParticipants() {
        const search = document.getElementById('searchParticipants').value.toLowerCase();
        const role = document.getElementById('filterRole').value;
        const dept = document.getElementById('filterDepartment').value;
        const status = document.getElementById('filterStatus').value;

        const filtered = this.participants.filter(p => {
            return (
                p.name.toLowerCase().includes(search) &&
                (!role || p.role === role) &&
                (!dept || p.department === dept) &&
                (!status || p.status === status)
            );
        });

        const tbody = document.getElementById('participantsTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No participants match your filters.</td></tr>';
            return;
        }

        filtered.forEach(participant => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${participant.name}</strong></td>
                <td>${this.capitalize(participant.role)}</td>
                <td>${this.capitalize(participant.department)}</td>
                <td>${participant.contact}</td>
                <td>${participant.regDate}</td>
                <td><span class="status-badge status-${participant.status}">${this.capitalize(participant.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-edit" onclick="regManager.editParticipant(${participant.id})">Edit</button>
                        <button class="btn-delete" onclick="regManager.deleteParticipant(${participant.id})">Deactivate</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    filterRegistrations() {
        const eventId = document.getElementById('filterEventRegistration').value;
        const status = document.getElementById('filterApprovalStatus').value;

        const filtered = this.registrations.filter(r => {
            return (
                (!eventId || r.eventId == eventId) &&
                (!status || r.status === status)
            );
        });

        const tbody = document.getElementById('registrationsTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No registrations match your filters.</td></tr>';
            return;
        }

        filtered.forEach(reg => {
            const row = document.createElement('tr');
            const statusBadge = reg.status.replace('-', ' ');
            row.innerHTML = `
                <td><strong>${reg.participantName}</strong></td>
                <td>${reg.eventName}</td>
                <td>${reg.regDate}</td>
                <td><span class="status-badge status-${reg.status}">${this.capitalize(statusBadge)}</span></td>
                <td>${reg.registered}/${reg.capacity}</td>
                <td>${reg.capacity - reg.registered}</td>
                <td>
                    <div class="action-buttons">
                        ${reg.status === 'pending' ? `
                            <button class="btn-approve" onclick="regManager.approveRegistration(${reg.id})">Approve</button>
                            <button class="btn-decline" onclick="regManager.declineRegistration(${reg.id})">Decline</button>
                        ` : ''}
                        <button class="btn-delete" onclick="regManager.deleteRegistration(${reg.id})">Remove</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    filterHistory() {
        const search = document.getElementById('searchParticipantHistory').value.toLowerCase();
        const eventId = document.getElementById('filterEventHistory').value;

        const filtered = this.activities.filter(a => {
            return (
                a.participantName.toLowerCase().includes(search) &&
                (!eventId || a.eventId == eventId)
            );
        });

        const tbody = document.getElementById('historyTableBody');
        tbody.innerHTML = '';

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">No activity history found.</td></tr>';
            return;
        }

        filtered.forEach(activity => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${activity.participantName}</strong></td>
                <td>${activity.eventName}</td>
                <td>${activity.date}</td>
                <td><span class="status-badge status-${activity.attended ? 'present' : 'absent'}">${activity.attended ? 'Present' : 'Absent'}</span></td>
                <td><span class="status-badge status-${activity.status}">${this.capitalize(activity.status)}</span></td>
                <td>${activity.date}</td>
            `;
            tbody.appendChild(row);
        });
    }

    toggleAutoApproval(enabled) {
        if (enabled) {
            Swal.fire({
                title: 'Auto-Approval Enabled',
                text: 'All future registrations will be automatically approved.',
                icon: 'info',
                confirmButtonColor: '#0066cc'
            });
        }
    }

    loadAnalyticsData() {
        const eventId = document.getElementById('analyticsEventFilter').value;

        const totalParticipants = this.participants.length;
        const presentCount = this.attendance.filter(a => a.status === 'present').length;
        const absentCount = this.attendance.filter(a => a.status === 'absent').length;
        const completedCount = this.activities.filter(a => a.status === 'completed').length;

        document.getElementById('totalParticipantsAnalytics').textContent = totalParticipants;
        document.getElementById('presentAnalytics').textContent = presentCount;
        document.getElementById('absentAnalytics').textContent = absentCount;
        document.getElementById('completedAnalytics').textContent = completedCount;

        const roleBreakdown = this.getAttendanceByRole();
        const deptBreakdown = this.getAttendanceByDept();

        document.getElementById('attendanceByRole').innerHTML = roleBreakdown;
        document.getElementById('attendanceByDepartment').innerHTML = deptBreakdown;
    }

    getAttendanceByRole() {
        const roles = {};
        this.participants.forEach(p => {
            roles[p.role] = (roles[p.role] || 0) + 1;
        });

        return Object.entries(roles)
            .map(([role, count]) => `<p>${this.capitalize(role)}: ${count}</p>`)
            .join('');
    }

    getAttendanceByDept() {
        const depts = {};
        this.participants.forEach(p => {
            depts[p.department] = (depts[p.department] || 0) + 1;
        });

        return Object.entries(depts)
            .map(([dept, count]) => `<p>${this.capitalize(dept)}: ${count}</p>`)
            .join('');
    }

    exportAnalytics() {
        Swal.fire({
            title: 'Export Analytics',
            html: `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;">Select export format:</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button onclick="regManager.performExport('pdf')" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📄 PDF</button>
                        <button onclick="regManager.performExport('csv')" style="padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">📊 CSV</button>
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
                text: `Analytics exported as ${format.toUpperCase()} successfully.`,
                icon: 'success',
                confirmButtonColor: '#0066cc'
            });
        }, 2000);
    }

    capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/-/g, ' ');
    }
}

// Initialize on page load
const regManager = new RegistrationAttendanceManager();
