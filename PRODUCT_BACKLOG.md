# Product Backlog - LGU Disaster Preparedness System

## 📋 Backlog Structure (Agile Scrum)

**Last Updated:** December 2024  
**Sprint Duration:** 2 weeks  
**Current Sprint:** Sprint 1

---

## 🎯 SPRINT 1: Core AI Integration (CURRENT)

### PBI-001: Gemini API Integration - Basic Scenario Generation ⭐⭐⭐
**Priority:** Critical (P0)  
**Story Points:** 13  
**Status:** 🔴 Not Started

**User Story:**
As an **Admin/Trainer**, I want to **generate disaster scenarios automatically using AI** so that I can **create diverse, realistic training scenarios without manual effort**.

**Acceptance Criteria:**
- [ ] Gemini API key configured and secured
- [ ] API endpoint created: `/api/action/generate-scenario.php`
- [ ] Frontend button "Generate with AI" added to Scenario Design page
- [ ] Form fields for AI parameters (disaster type, difficulty, location)
- [ ] Generated scenario displayed in form for editing
- [ ] Error handling for API failures
- [ ] Loading state while generating

**Technical Tasks:**
- [ ] Set up Google Gemini API account
- [ ] Install/configure Gemini PHP SDK
- [ ] Create API endpoint with error handling
- [ ] Design prompt template for scenario generation
- [ ] Create frontend integration
- [ ] Add UI for AI generation form
- [ ] Test with different disaster types

**Dependencies:** None  
**Estimated Time:** 2-3 days

---

### PBI-002: Database Schema for Scenarios ⭐⭐
**Priority:** High (P1)  
**Story Points:** 8  
**Status:** 🔴 Not Started

**User Story:**
As a **System**, I need to **store scenarios in database** so that **scenarios persist and can be retrieved later**.

**Acceptance Criteria:**
- [ ] Database tables created (scenarios, events, timeline_events)
- [ ] CRUD operations implemented
- [ ] Data migration from hardcoded to database
- [ ] Foreign key relationships established

**Technical Tasks:**
- [ ] Design database schema
- [ ] Create SQL migration script
- [ ] Update `db.sql` file
- [ ] Create API endpoints for CRUD
- [ ] Update frontend to use API instead of hardcoded data

**Dependencies:** None (can work parallel with PBI-001)  
**Estimated Time:** 1-2 days

---

### PBI-003: Save AI-Generated Scenarios ⭐⭐
**Priority:** High (P1)  
**Story Points:** 5  
**Status:** 🔴 Not Started

**User Story:**
As an **Admin**, I want to **save AI-generated scenarios** so that I can **reuse and edit them later**.

**Acceptance Criteria:**
- [ ] Save button functional after AI generation
- [ ] Scenarios saved to database
- [ ] Success/error feedback
- [ ] Scenarios appear in scenarios list after saving

**Dependencies:** PBI-001, PBI-002  
**Estimated Time:** 0.5 days

---

## 📅 SPRINT 2: Enhanced Features

### PBI-004: Enhanced Analytics Dashboard ⭐
**Priority:** Medium (P2)  
**Story Points:** 8  
**Status:** ⚪ Backlog

**User Story:**
As an **Admin**, I want to **view detailed analytics** so that I can **track training effectiveness and participant performance**.

**Acceptance Criteria:**
- [ ] Performance trends chart
- [ ] Most common failed actions analysis
- [ ] Scenario effectiveness metrics
- [ ] Export to CSV/PDF functionality

**Dependencies:** PBI-002  
**Estimated Time:** 2 days

---

### PBI-005: Timeline Builder with AI Suggestions ⭐
**Priority:** Medium (P2)  
**Story Points:** 5  
**Status:** ⚪ Backlog

**User Story:**
As an **Admin**, I want **AI-suggested timeline events** so that I can **quickly build scenario timelines**.

**Acceptance Criteria:**
- [ ] AI suggests events based on scenario type
- [ ] Auto-populate timeline from AI-generated scenario
- [ ] Event dependencies and triggers

**Dependencies:** PBI-001  
**Estimated Time:** 1-2 days

---

### PBI-006: Localized Data Integration ⭐
**Priority:** Medium (P2)  
**Story Points:** 8  
**Status:** ⚪ Backlog

**User Story:**
As a **System**, I need **Barangay San Agustin specific data** so that **scenarios are realistic and localized**.

**Acceptance Criteria:**
- [ ] Population data integrated
- [ ] Geographic information added
- [ ] Infrastructure details included
- [ ] Historical disaster data referenced
- [ ] Scenarios use local street names and locations

**Dependencies:** PBI-001  
**Estimated Time:** 2 days

---

## 📅 SPRINT 3: Advanced Features

### PBI-007: AI-Assisted Scoring ⭐
**Priority:** Medium (P2)  
**Story Points:** 13  
**Status:** ⚪ Backlog

**User Story:**
As an **Evaluator**, I want **AI-assisted scoring** so that **evaluation is more objective and detailed**.

**Acceptance Criteria:**
- [ ] AI analyzes participant responses
- [ ] Automatic performance metrics
- [ ] Personalized feedback generation
- [ ] Comparative analysis across sessions

**Dependencies:** PBI-001, Existing evaluation system  
**Estimated Time:** 3 days

---

### PBI-008: Real-time Scenario Updates ⭐
**Priority:** Low (P3)  
**Story Points:** 21  
**Status:** ⚪ Backlog

**User Story:**
As a **Participant**, I want **dynamic scenario updates** so that **training feels realistic and adaptive**.

**Acceptance Criteria:**
- [ ] Real-time event triggers
- [ ] AI-generated escalation events
- [ ] Adaptive scenarios based on actions
- [ ] WebSocket or polling implementation

**Dependencies:** PBI-001  
**Estimated Time:** 5 days

---

### PBI-009: Pilot Training Session Interface ⭐
**Priority:** Medium (P2)  
**Story Points:** 13  
**Status:** ⚪ Backlog

**User Story:**
As a **Participant**, I want a **live simulation interface** so that I can **participate in training sessions**.

**Acceptance Criteria:**
- [ ] Participant dashboard
- [ ] Live scenario display
- [ ] Action input interface
- [ ] Real-time feedback

**Dependencies:** PBI-001, PBI-008  
**Estimated Time:** 3-4 days

---

## 📅 SPRINT 4: Polish & Integration

### PBI-010: Training Module Integration ⭐
**Priority:** Medium (P2)  
**Story Points:** 5  
**Status:** ⚪ Backlog

**User Story:**
As an **Admin**, I want to **link scenarios to training modules** so that **learning is structured**.

**Acceptance Criteria:**
- [ ] Scenarios linked to modules
- [ ] Pre-requisite module checking
- [ ] Learning objectives tracking

**Dependencies:** Existing training modules  
**Estimated Time:** 1-2 days

---

### PBI-011: Export/Import Functionality ⭐
**Priority:** Low (P3)  
**Story Points:** 5  
**Status:** ⚪ Backlog

**User Story:**
As an **Admin**, I want to **export/import scenarios** so that I can **backup and share scenarios**.

**Acceptance Criteria:**
- [ ] Export scenarios to JSON/CSV
- [ ] Export analytics to PDF/CSV
- [ ] Import scenarios from file
- [ ] Bulk operations

**Dependencies:** PBI-002  
**Estimated Time:** 1-2 days

---

### PBI-012: Notification System ⭐
**Priority:** Low (P3)  
**Story Points:** 8  
**Status:** ⚪ Backlog

**User Story:**
As a **User**, I want to **receive notifications** so that I am **informed about training sessions and updates**.

**Acceptance Criteria:**
- [ ] Email notifications
- [ ] Scenario assignment alerts
- [ ] Training session reminders
- [ ] Certificate issuance notifications

**Dependencies:** None  
**Estimated Time:** 2 days

---

## 📊 Backlog Summary

### By Priority:
- **P0 (Critical):** 1 item
- **P1 (High):** 2 items
- **P2 (Medium):** 6 items
- **P3 (Low):** 3 items

### By Status:
- 🔴 **Not Started:** 3 items (Sprint 1)
- ⚪ **Backlog:** 9 items

### Total Story Points:
- **Sprint 1:** 26 points
- **Sprint 2:** 21 points
- **Sprint 3:** 47 points
- **Sprint 4:** 18 points
- **Total:** 112 points

---

## 🎯 Definition of Done

A Product Backlog Item is considered "Done" when:
- [ ] Code is written and reviewed
- [ ] Unit tests pass (if applicable)
- [ ] Feature works as per acceptance criteria
- [ ] UI/UX is complete and tested
- [ ] Documentation updated (if needed)
- [ ] No critical bugs
- [ ] Deployed to development environment

---

## 📝 Notes

- **Sprint 1 Focus:** Get Gemini API working - this is the core feature
- **Parallel Work:** Database schema can be done alongside API integration
- **Quick Wins:** PBI-003 is small and can be done quickly after PBI-001 and PBI-002
- **Risk Items:** PBI-008 (Real-time) is complex - may need to simplify or defer

---

## 🚀 Ready to Start?

**Recommended Sprint 1 Plan:**
1. **Day 1-2:** PBI-001 (Gemini API Integration) - Main feature
2. **Day 2-3:** PBI-002 (Database Schema) - Can work parallel
3. **Day 3:** PBI-003 (Save Scenarios) - Quick win

**Total Sprint 1:** ~3 days of focused work

---

**Next Steps:**
1. ✅ Backlog created
2. 🔄 Start Sprint 1 - PBI-001
3. 📋 Daily standups to track progress
4. 🎯 Sprint Review after 2 weeks

