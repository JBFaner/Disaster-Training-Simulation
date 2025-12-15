# Features na Dapat Idagdag sa System
## Base sa Capstone Document

## 🚨 PRIORITY 1: Core Features (Critical - Main Feature ng Document)

### 1. **Gemini API Integration para sa AI Scenario Auto-Generation** ⭐⭐⭐
**Status:** ❌ WALA PA - Ito ang pinaka-importanteng feature!

**Dapat i-implement:**
- [ ] **API Service/Endpoint** para sa Gemini API
  - File: `api/gemini-scenario-generator.php` o `api/action/generate-scenario.php`
  - Function: Makipag-communicate sa Google Gemini API
  - Input: Disaster type, difficulty, location (Barangay San Agustin), conditions
  - Output: Auto-generated scenario description, events, timeline

- [ ] **Frontend Integration** sa Scenario Design page
  - Button: "Generate Scenario with AI" sa `admin-scenario-design.php`
  - Form fields para sa AI generation parameters:
    - Disaster type dropdown
    - Difficulty level
    - Location-specific details (Novaliches, Quezon City)
    - Weather conditions
    - Time of day
  - Display generated scenario sa form para ma-edit/refine

- [ ] **Gemini API Configuration**
  - API key management (secure storage)
  - Error handling (API rate limits, failures)
  - Response parsing at formatting

**Technical Requirements:**
```php
// Example structure needed:
- POST endpoint: /api/action/generate-scenario.php
- Parameters: disaster_type, difficulty, location, conditions
- Gemini API call using Google's PHP SDK
- Return JSON with generated scenario
```

**Document Reference:**
- Chapter I, Section 1.1: "The Gemini API, an advanced AI technology, offers a promising approach..."
- Chapter I, Section 1.4: "Training by AI Innovation (The 'Unpredictability Factor')"

---

### 2. **Real-time Scenario Generation during Simulation** ⭐⭐
**Status:** ❌ WALA PA

**Dapat i-implement:**
- [ ] Dynamic scenario updates habang nagra-run ang simulation
- [ ] AI-generated events na nag-a-adapt based sa participant actions
- [ ] Real-time escalation events (cascading failures)
- [ ] Integration sa Simulation Planning module

**Document Reference:**
- Chapter I, Section 1.2: "dynamically introduce localized, novel, and diverse challenges"

---

## 📊 PRIORITY 2: Enhanced Features (Important pero may existing na basic version)

### 3. **Advanced Scoring Mechanism with AI Evaluation** ⭐⭐
**Status:** ⚠️ May basic version pero kulang

**Current State:**
- May evaluation-scoring page
- Manual scoring lang

**Dapat i-improve:**
- [ ] **AI-assisted scoring** - Gemini API analyzes participant responses
- [ ] **Automatic performance metrics** calculation
- [ ] **Comparative analysis** - Compare performance across sessions
- [ ] **Detailed feedback generation** - AI generates personalized feedback

**Document Reference:**
- Chapter I, Section 1.2: "Development of a Scoring Mechanism: Creation of a quantitative system to evaluate LGU personnel response decisions, timelines, and resource allocation effectiveness"

---

### 4. **Enhanced Analytics Dashboard** ⭐
**Status:** ⚠️ May basic analytics pero kulang

**Current State:**
- May analytics tab sa scenario design
- Basic metrics lang

**Dapat i-improve:**
- [ ] **Performance trends** over time
- [ ] **Most common failed actions** analysis (mentioned sa document)
- [ ] **Scenario effectiveness metrics**
- [ ] **Participant improvement tracking**
- [ ] **Export functionality** (CSV/PDF) - mentioned sa document

**Document Reference:**
- Chapter I, Section 1.2: Analytics mentioned
- Admin Scenario Design page line 387: "Export (CSV/PDF)" button exists pero baka hindi pa functional

---

### 5. **Timeline Builder Enhancement** ⭐
**Status:** ⚠️ May basic timeline builder

**Current State:**
- May timeline tab sa scenario design
- Manual event creation

**Dapat i-improve:**
- [ ] **AI-suggested timeline events** based sa scenario type
- [ ] **Auto-populate events** from AI-generated scenario
- [ ] **Event dependencies** - link events together
- [ ] **Trigger conditions** - automatic event triggers based sa participant actions

---

## 🔧 PRIORITY 3: Integration & Data Features

### 6. **Database Integration for Scenarios** ⭐
**Status:** ⚠️ May hardcoded data lang

**Current State:**
- Scenarios stored sa JavaScript array (temporary)
- Hindi naka-save sa database

**Dapat i-implement:**
- [ ] **Database schema** para sa scenarios
- [ ] **CRUD operations** (Create, Read, Update, Delete scenarios)
- [ ] **Save AI-generated scenarios** sa database
- [ ] **Version control** - save different versions ng scenarios

**Files to create/modify:**
- `api/action/save-scenario.php`
- `api/retrieve/get-scenarios.php`
- `db.sql` - add scenario tables

---

### 7. **Localized Data Integration** ⭐
**Status:** ❌ WALA PA

**Dapat i-implement:**
- [ ] **Barangay San Agustin specific data**
  - Population data
  - Geographic information
  - Infrastructure details
  - Historical disaster data
- [ ] **Integration sa scenario generation** - use local data para mas realistic
- [ ] **Location-specific hazard maps**

**Document Reference:**
- Chapter I, Section 1.1: "geographically localized, and technically complex disaster scenarios tailored for LGU 4"
- Chapter II, Section 2.5: "Hyper-Customization: The advanced contextual understanding of models like Gemini is the perfect engine for our project. It ensures that the generated scenarios for the LGU are not generic, but highly specific and relevant, using local street names, LGU personnel roles, and specific local hazards."

---

### 8. **Training Module Integration** ⭐
**Status:** ⚠️ May training modules page pero hindi integrated

**Dapat i-implement:**
- [ ] **Link scenarios to training modules** (mentioned sa scenario design form)
- [ ] **Pre-requisite modules** - certain modules must be completed before scenario
- [ ] **Learning objectives tracking**

**Current State:**
- Line 241-244 sa `admin-scenario-design.php`: May dropdown for training module pero baka hindi functional

---

## 🎯 PRIORITY 4: User Experience Features

### 9. **Pilot Training Session Features** ⭐
**Status:** ❌ WALA PA

**Dapat i-implement:**
- [ ] **Live simulation interface** para sa participants
- [ ] **Real-time scenario updates** during training
- [ ] **Participant dashboard** - view their progress
- [ ] **Instructor control panel** - manage live simulations
- [ ] **Feedback collection** - gather feedback after pilot session

**Document Reference:**
- Chapter I, Section 1.2: "Validation through a Pilot Training Session: Execution of a formal pilot simulation session with select LGU 4 personnel"

---

### 10. **Multi-hazard Scenario Support** ⭐
**Status:** ⚠️ May dropdown pero baka hindi fully supported

**Dapat i-implement:**
- [ ] **Complex multi-hazard scenarios** (mentioned sa document)
- [ ] **Cascading failure simulation** - one disaster triggers another
- [ ] **Scenario combinations** - earthquake + fire + power outage simultaneously

**Document Reference:**
- Chapter I, Section 1.3: "cascading failures (e.g., simultaneous infrastructure damage, communication outages, and mass displacement)"

---

## 📱 PRIORITY 5: Additional Features

### 11. **Export/Import Functionality**
**Status:** ⚠️ May export button pero baka hindi functional

**Dapat i-implement:**
- [ ] **Export scenarios** to JSON/CSV
- [ ] **Export analytics** to PDF/CSV (mentioned sa document)
- [ ] **Import scenarios** from file
- [ ] **Bulk operations**

---

### 12. **User Roles and Permissions**
**Status:** ⚠️ May basic structure pero baka kulang

**Dapat i-implement:**
- [ ] **Role-based access** (Admin, Trainer, Participant, Evaluator)
- [ ] **Permission system** - who can create/edit scenarios
- [ ] **Audit trail** - track who made changes

---

### 13. **Notification System**
**Status:** ❌ WALA PA

**Dapat i-implement:**
- [ ] **Email notifications** for:
  - Scenario assignments
  - Training session reminders
  - Certificate issuance
  - Evaluation completion

---

## 🗂️ IMPLEMENTATION ROADMAP

### Phase 1: Core AI Integration (WEEK 1-2)
1. ✅ Set up Gemini API account and get API key
2. ✅ Create API service for scenario generation
3. ✅ Integrate sa Scenario Design page
4. ✅ Test basic scenario generation

### Phase 2: Database & Data (WEEK 3)
1. ✅ Create database schema
2. ✅ Implement CRUD operations
3. ✅ Add localized data (Barangay San Agustin)
4. ✅ Save/load scenarios from database

### Phase 3: Enhanced Features (WEEK 4-5)
1. ✅ Advanced scoring with AI
2. ✅ Enhanced analytics
3. ✅ Timeline builder improvements
4. ✅ Training module integration

### Phase 4: User Experience (WEEK 6)
1. ✅ Pilot training session features
2. ✅ Live simulation interface
3. ✅ Export/import functionality
4. ✅ Notification system

---

## 📝 QUICK WINS (Pwedeng gawin agad)

### Easy to Implement:
1. **Database Schema** - Create tables for scenarios, events, participants
2. **API Endpoints** - Basic CRUD endpoints
3. **Export to CSV** - Simple export functionality
4. **Form Validation** - Improve existing forms

### Medium Difficulty:
1. **Gemini API Integration** - Need API key, PHP SDK setup
2. **Analytics Enhancement** - Add more charts/metrics
3. **Timeline Builder** - Improve UI and functionality

### Complex:
1. **Real-time Simulation** - Need WebSocket or similar
2. **AI-assisted Scoring** - Complex AI integration
3. **Multi-hazard Scenarios** - Complex logic

---

## 🎯 RECOMMENDED STARTING POINT

**Start with Gemini API Integration** kasi:
1. ✅ Ito ang MAIN feature ng document
2. ✅ Core differentiator ng project
3. ✅ Required para sa objectives
4. ✅ Foundation para sa ibang features

**Next Steps:**
1. Get Gemini API key from Google
2. Install Google Gemini PHP SDK
3. Create API endpoint
4. Integrate sa frontend
5. Test with sample scenarios

---

## ❓ Questions para sa Team:

1. **May Gemini API key na ba kayo?**
2. **Ano ang preferred tech stack?** (PHP lang ba o pwede Python for AI service?)
3. **May database na ba?** (MySQL, PostgreSQL?)
4. **Ano ang timeline?** (Kailan due?)
5. **May budget ba for API calls?** (Gemini API may have costs)

---

## 📚 Resources Needed:

1. **Google Gemini API Documentation**
   - https://ai.google.dev/docs
   - PHP SDK: `composer require google/generative-ai-php`

2. **Database Design**
   - Tables: scenarios, events, participants, evaluations, training_modules

3. **API Documentation**
   - RESTful API design
   - Error handling
   - Security (API key management)

---

**Gusto mo bang simulan natin ang Gemini API integration? Pwede kong gawin:**
1. Create API endpoint structure
2. Set up Gemini API integration code
3. Create frontend integration
4. Add error handling and validation

Sabi mo lang kung saan tayo magsisimula! 🚀

