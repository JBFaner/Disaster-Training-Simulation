# Participant UI - Complete Implementation

## Overview
A comprehensive participant interface for the LGU Disaster Preparedness Training & Simulation system. Participants have a simplified, read-only interface separate from the admin system.

## Created Files

### 1. **Login & Authentication**
- **login.php** - Main login page
  - Sign in with email/password
  - Sign up for new account
  - Email verification for new accounts
  - MFA (Multi-Factor Authentication) via email
  - Demo credentials: jbfaner8@gmail.com / part123

- **css/login.css** - Login page styling

### 2. **Core Pages**

#### Dashboard (part-index.php)
- Welcome message with participant name
- Statistics: Trainings Completed, Events Attended, Certificates Earned, Average Score
- Quick Action Cards linking to all major features
- Upcoming Events preview
- Recent Activity timeline
- Responsive design with sidebar navigation

#### Training Modules (part-training-modules.php)
- View all assigned training modules
- Progress tracking with percentage bars
- Status badges: Completed, In Progress, Not Started
- Filter buttons (All, Completed, In Progress, Not Started)
- Modal popup for detailed module information
- 6 sample modules:
  - Earthquake Skills (Completed)
  - Fire Skills (In Progress)
  - Typhoon (Not Started)
  - Flood Response Training (Completed)
  - First Aid Basics (Not Started)
  - Emergency Communication (In Progress)

#### My Events (part-my-events.php)
- Three tabs: Available Drills, Registered Events, Completed Events
- **Available Drills**:
  - Earthquake Preparedness Drill
  - Flood Response Training
  - Fire Evacuation Drill
  - Detailed event information, requirements, location, time
  - One-click registration
- **Registered Events**:
  - Show participant's registered events
  - QR code display for check-in
  - Unregister option
- **Completed Events**:
  - Past events with scores
  - Links to view results
  - Certificate download
  - Event details and duration

#### Scenarios (part-scenarios.php)
- 5 disaster scenario cards:
  - Earthquake Scenario - "Magnitude 6.5 Earthquake Strike"
  - Fire Scenario - "Multi-Floor Fire Outbreak"
  - Flood Scenario - "Flash Flood Warning & Evacuation"
  - Typhoon Scenario - "Typhoon Preparedness & Shelter In Place"
  - Medical Emergency - "Mass Casualty Management"
- Timeline-based steps with detailed actions
- Modal popup for full scenario details
- Color-coded scenario types
- Practice guides for simulation events

#### Evaluation Results (part-evaluation-results.php)
- Display all evaluation results from completed events
- Score visualization with circular score display
- Score breakdown by category with percentage bars
- **Result Cards with**:
  - Status: Passed, Needs Improvement
  - Score percentage
  - Component scores (Evacuation Speed, Safety Compliance, Teamwork, etc.)
  - Strengths list (green checkmarks)
  - Areas for improvement
  - Trainer comments
- Download PDF report
- Share feedback option
- Register for refresher course (for low scores)
- Sample evaluations: Fire Evacuation (92%), Earthquake Drill (88%), First Aid (72%)

#### Certificates (part-certificate.php)
- Certificate statistics (Count, Hours, Average Score)
- Certificate cards with:
  - Certificate preview mockup
  - Title, issued date, score
  - Validity period (2 years)
  - Download PDF button
  - Share button (LinkedIn, Facebook, WhatsApp)
- Pending Certificates section
- Certificate Guidelines:
  - Eligibility (≥75% score)
  - Validity period
  - Usage rights
  - Renewal process
- Sample certificates: Fire Safety Expert, Earthquake Preparedness

#### Profile & History (part-profile.php)
- **Profile Section**:
  - Avatar display
  - Full name, email, join date
  - Statistics: Modules Assigned, Completed, Events Attended, Certificates
  - Edit Profile button
  - Change Password button

- **QR Code Section** (For Event Check-in):
  - QR code display
  - Download QR Code button
  - Print QR Code button
  - Unique participant ID

- **Training Progress**:
  - Grid view of all modules with progress bars
  - Status labels for each module

- **Event Attendance History**:
  - Timeline view of all past/upcoming events
  - Event details with dates and locations
  - Results and certificates earned
  - Upcoming events pending

- **Account Settings**:
  - Email Notifications toggle
  - SMS Reminders toggle
  - Two-Factor Authentication toggle
  - Delete Account option (danger zone)

### 3. **Styling Files**

#### CSS Files (part-*.css)
- **css/login.css** - Login page styling
- **css/part-styles.css** - Main participant styles
  - Sidebar navigation
  - Top navigation
  - Cards and layouts
  - Dashboard components
- **css/part-modules.css** - Training modules page
- **css/part-events.css** - Events/Registration page
- **css/part-scenarios.css** - Scenarios page
- **css/part-results.css** - Evaluation results page
- **css/part-certificates.css** - Certificates page
- **css/part-profile.css** - Profile & history page

**Design Theme**:
- Primary Color: #3a7675 (Teal Green)
- Secondary Color: #2d5a58 (Dark Teal)
- Accent Color: #4a9b8e (Light Teal)
- Text: #1a202c (Dark Gray)
- Background: #f5f5f5 (Light Gray)
- Responsive grid layouts
- Mobile-friendly (media queries for tablets and phones)

### 4. **JavaScript Files**

- **js/part-main.js** - Sidebar toggle, navigation, common functions
- **js/login.js** - Login form validation

### 5. **Features Implemented**

✅ **User Authentication**
- Login with email/password
- Sign up with email verification
- MFA verification
- Session management

✅ **Dashboard**
- Statistics overview
- Quick action cards
- Upcoming events preview
- Activity timeline

✅ **Training Modules** (Read-only)
- View assigned modules
- Progress tracking
- Filter by status
- Module details modal

✅ **Event Registration**
- Browse available drills
- Register for events
- View registered events
- Unregister from events
- QR code for check-in
- View completed events
- Download certificates

✅ **Simulation Scenarios**
- 5 detailed disaster scenarios
- Timeline-based procedures
- Full scenario details modal
- Practice guides

✅ **Evaluation Results**
- View evaluation scores
- Score breakdown visualization
- Trainer feedback
- Strengths and improvements
- Download result PDF

✅ **Certificates**
- View earned certificates
- Download as PDF
- Share on social media
- Certificate preview
- Pending certificates tracking

✅ **Profile & History**
- Personal information management
- QR code for events
- Training progress tracking
- Event attendance history
- Account settings
- Password management

## Key Differences from Admin Interface

| Feature | Admin | Participant |
|---------|-------|-------------|
| Create Content | ✅ | ✗ (Read-only) |
| Manage Events | ✅ | ✗ (Register only) |
| Grade/Score | ✅ | ✗ (View only) |
| Design Scenarios | ✅ | ✗ (Follow only) |
| Access Reports | ✅ | ✓ (Own only) |
| Download Certificates | ✗ | ✅ |
| View Training | ✅ | ✅ |
| Register Events | ✗ | ✅ |

## Demo Credentials

**Email**: jbfaner8@gmail.com  
**Password**: part123

After login, enter the MFA code sent to the email.

## Navigation Structure

```
login.php (Entry point)
    ↓
part-index.php (Dashboard)
    ├── part-training-modules.php
    ├── part-my-events.php
    ├── part-scenarios.php
    ├── part-evaluation-results.php
    ├── part-certificate.php
    └── part-profile.php
```

## Responsive Design

- **Desktop**: Full sidebar + multi-column layouts
- **Tablet**: Collapsible sidebar + 2-column grids
- **Mobile**: Single column, hamburger menu

## Security Features

1. Session-based authentication
2. MFA verification for login
3. Email verification for sign-up
4. Rate limiting on login attempts
5. CAPTCHA after 3 failed attempts (configurable)
6. Password strength requirements
7. Logout confirmation dialogs

## Customization Ready

All pages include:
- Easy-to-modify sample data
- Placeholder functions for future backend integration
- SweetAlert2 for user confirmations
- Responsive CSS with media queries
- Accessible color scheme

## Future Integration Points

- Database connection for user accounts
- Real email service integration
- PDF certificate generation
- QR code generation library
- File upload for certificates
- API endpoints for data persistence
