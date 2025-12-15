# Participant UI - Quick Reference Guide

## File Structure
```
frontend/
├── login.php                           # Login & Sign Up
├── part-index.php                      # Dashboard
├── part-training-modules.php           # View Training Modules
├── part-my-events.php                  # Event Registration & Attendance
├── part-scenarios.php                  # Disaster Scenarios
├── part-evaluation-results.php         # View Evaluation Scores
├── part-certificate.php                # Download Certificates
├── part-profile.php                    # Profile & History + QR Code
│
├── css/
│   ├── login.css                       # Login page styles
│   ├── part-styles.css                 # Main styles (sidebar, nav, cards)
│   ├── part-modules.css                # Training modules
│   ├── part-events.css                 # Events & registration
│   ├── part-scenarios.css              # Scenarios
│   ├── part-results.css                # Evaluation results
│   ├── part-certificates.css           # Certificates
│   └── part-profile.css                # Profile & history
│
└── js/
    ├── part-main.js                    # Navigation & sidebar logic
    └── login.js                        # Login form validation
```

## User Flow

### 1. New User
```
login.php (Sign Up)
    ↓
Enter: Full Name, Email, Password
    ↓
Email Verification Code Sent
    ↓
Enter Verification Code
    ↓
part-index.php (Dashboard)
```

### 2. Existing User
```
login.php (Login)
    ↓
Enter: Email, Password
    ↓
MFA Code Sent to Email
    ↓
Enter MFA Code
    ↓
part-index.php (Dashboard)
```

### 3. From Dashboard
```
part-index.php
├── Quick Actions
│   ├── View Training Modules → part-training-modules.php
│   ├── Register for Events → part-my-events.php
│   ├── View Results → part-evaluation-results.php
│   └── My Certificates → part-certificate.php
│
└── Sidebar Navigation
    ├── Dashboard
    ├── Training Modules
    ├── My Events
    ├── Scenarios
    ├── Evaluation Results
    ├── Certificates
    └── Profile & History
```

## Page Components

### part-index.php (Dashboard)
- [X] Welcome header with user name
- [X] 4 stat cards (Trainings, Events, Certificates, Score)
- [X] Quick action cards (4 main features)
- [X] Upcoming events section (2 events preview)
- [X] Activity timeline (4 recent actions)

### part-training-modules.php
- [X] Filter buttons (All, Completed, In Progress, Not Started)
- [X] 6 module cards with:
  - Status badge
  - Progress bar (0-100%)
  - Description
  - Action button
- [X] Modal for module details
- [X] Learning objectives list
- [X] Course content details

### part-my-events.php
- [X] 3 tabs:
  - Available Drills (3 events)
  - My Registered Events (1 event)
  - Completed Events (2 events)
- [X] Event cards with:
  - Date badge
  - Status badge
  - Location & time
  - Capacity/spots
  - Requirements list
  - Action button

### part-scenarios.php
- [X] 5 scenario cards:
  - Earthquake
  - Fire
  - Flood
  - Typhoon
  - Medical Emergency
- [X] Timeline steps for each scenario
- [X] Modal with full scenario details

### part-evaluation-results.php
- [X] 3 result cards:
  - Fire Evacuation (Passed - 92%)
  - Earthquake (Passed - 88%)
  - First Aid (Needs Improvement - 72%)
- [X] Score circle visualization
- [X] Score breakdown by component
- [X] Strengths list
- [X] Improvement areas
- [X] Trainer comments
- [X] Action buttons (Download, Share)

### part-certificate.php
- [X] Stats section (2 certificates, 5 hours, 90% avg)
- [X] 2 certificate cards with preview mockup
- [X] Download & Share buttons
- [X] Pending certificates section
- [X] Certificate guidelines (4 items)

### part-profile.php
- [X] Profile card with avatar, name, email, join date
- [X] Profile stats (6 items)
- [X] Edit Profile & Change Password buttons
- [X] QR Code section with download/print
- [X] Training progress grid (6 modules)
- [X] Event attendance timeline (4 events)
- [X] Account settings toggles (3 settings + delete)

## Color Scheme

### Primary Colors
- Primary Teal: `#3a7675`
- Dark Teal: `#2d5a58`
- Light Teal: `#4a9b8e`

### Status Colors
- Success/Completed: `#2e7d32` (Green)
- In Progress: `#e65100` (Orange)
- Warning/Needs Improvement: `#f39c12` (Amber)
- Info: `#1976d2` (Blue)
- Error/Delete: `#c33` (Red)

### Neutral Colors
- Text: `#1a202c` (Dark)
- Secondary Text: `#718096` (Gray)
- Border: `#e0e0e0` (Light Gray)
- Background: `#f5f5f5` (Lighter Gray)
- White: `#ffffff`

## Key Features by Page

### Login Page (login.php)
```
┌─────────────────────────┐
│   [LOGO]                │
│   Login                 │
│   "Disaster Preparedness Training & Simulation" │
│                         │
│   Email: [______]       │
│   Password: [______]    │
│   [  LOGIN BUTTON  ]    │
│   Don't have account?   │
│   [Sign up here]        │
│                         │
│   Demo Credentials:     │
│   jbfaner8@gmail.com    │
│   part123               │
└─────────────────────────┘
```

### Dashboard (part-index.php)
```
┌──────────────────────────────────────────┐
│ [SIDEBAR] │ [TOP NAV] [USER MENU]       │
│           ├──────────────────────────────┤
│ Dashboard │ Welcome back, [Name]! 👋     │
│ Training  │                              │
│ My Events │ ┌──────┬──────┬──────┬──────┐
│ Scenarios │ │Books │Events│Certs │Score │
│ Results   │ └──────┴──────┴──────┴──────┘
│ Certs     │                              │
│ Profile   │ Quick Actions (4 cards)      │
│           │                              │
│ [LOGOUT]  │ Upcoming Events (2 cards)    │
│           │                              │
│           │ Recent Activity (Timeline)   │
└──────────────────────────────────────────┘
```

## Session Variables Used

```php
$_SESSION['participant_id']     // Unique ID
$_SESSION['participant_email']  // Email
$_SESSION['participant_name']   // Full Name
$_SESSION['mfa_pending']        // MFA status
$_SESSION['verification_pending'] // Email verification status
```

## Sample Data Included

### Training Modules
1. Earthquake Skills - 100% Completed
2. Fire Skills - 65% In Progress
3. Typhoon - 0% Not Started
4. Flood Response Training - 100% Completed
5. First Aid Basics - 0% Not Started
6. Emergency Communication - 45% In Progress

### Events
- Earthquake Preparedness Drill (Dec 20)
- Flood Response Training (Dec 27)
- Fire Evacuation Drill (Jan 10)

### Evaluation Results
- Fire Evacuation Training: 92% ✓ Passed
- Earthquake Drill Phase 1: 88% ✓ Passed
- First Aid Assessment: 72% ⚠ Needs Improvement

### Certificates
- Fire Safety Expert (Nov 15, 2024)
- Earthquake Preparedness (Oct 28, 2024)

## Button Types

```css
.btn-primary              /* Teal gradient, white text */
.btn-secondary           /* Gray, dark text */
.btn-danger              /* Red, white text */
.btn-read-more           /* Teal gradient */
.btn-register            /* Teal gradient */
.btn-download            /* Teal gradient */
.btn-share               /* Gray */
.filter-btn              /* Gray/Teal toggle */
.status-badge            /* Color varies */
```

## Responsive Breakpoints

```css
Desktop:    > 1200px (Full sidebar + multi-column)
Tablet:     768px - 1200px (Collapsible sidebar)
Mobile:     < 768px (Single column, hamburger menu)
```

## Icons Used

- 👤 User/Profile
- 📚 Training/Books
- 🎯 Events/Target
- 📜 Certificates
- ⭐ Rating/Stars
- 📖 Training Materials
- 📅 Calendar/Events
- 📊 Results/Analytics
- 🏆 Achievements/Certificates
- 📧 Email
- ⏰ Time/Clock
- 📍 Location
- ✓ Success/Checkmark
- ⚠ Warning
- 🌊 Flood/Water
- 🏢 Earthquake/Building
- 🔥 Fire
- 🌪️ Typhoon/Wind
- 🏥 Medical/Health
- 📥 Download
- 📤 Share
- 🔐 Security/Password
- 🖨️ Print
- ✏️ Edit

## Styling Patterns

### Cards
```css
.card {
    background: white;
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 16px rgba(58, 118, 117, 0.15);
    transform: translateY(-2px);
}
```

### Buttons
```css
.btn {
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}
```

### Progress Bar
```css
.progress-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill {
    background: linear-gradient(90deg, #3a7675, #4a9b8e);
}
```

## Testing Checklist

- [X] Login with email & password
- [X] Sign up new account
- [X] Email verification
- [X] MFA verification
- [X] Dashboard loads correctly
- [X] All navigation links work
- [X] Sidebar toggle on mobile
- [X] All cards render properly
- [X] Buttons are clickable
- [X] Modals open/close
- [X] Form validation
- [X] Responsive on mobile/tablet
- [X] Logout confirmation
- [X] Session persistence

## Notes for Developers

1. All pages check for `$_SESSION['participant_id']` to ensure logged-in status
2. Sample data is hardcoded for demo purposes - replace with database queries
3. SweetAlert2 is used for confirmations and notifications
4. All CSS uses mobile-first approach
5. JavaScript is vanilla (no jQuery dependency)
6. Email sending uses `EmailHelper::` class methods
7. Forms use POST method with CSRF protection ready
8. Date formats are flexible for localization

---

**Design Theme**: Teal Green Professional  
**Target Users**: Regular participants (non-admin)  
**Access Level**: Read-only for content, manage own registrations  
**Mobile Friendly**: Yes (Responsive Design)
