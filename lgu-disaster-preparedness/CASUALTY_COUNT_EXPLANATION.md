# Casualty Count - Explanation

## 🎯 Ano ang Casualty Count?

**Casualty Count** = Bilang ng mga **taong nasugatan, namatay, o nawawala** sa disaster scenario.

### Definition:
- **Casualties** = People who are injured, killed, or missing due to the disaster
- **Count** = Total number of casualties in the scenario

---

## 📋 Sa Context ng Scenario Design

### Location sa Form:
- **Section:** "Scenario Conditions"
- **Field:** "Casualty Count *" (Required field)
- **Type:** Number input (minimum: 0)
- **Default Value:** 0

### Purpose:
Ginagamit para i-specify kung ilan ang casualties sa scenario para sa training.

---

## 🎯 Bakit Important?

### 1. **Training Realism**
- Realistic scenarios = may casualties
- Trainees need to practice handling casualties
- Different scenarios = different casualty counts

### 2. **Resource Planning**
- Higher casualty count = more resources needed
- Affects response planning
- Determines evacuation priorities

### 3. **Difficulty Level**
- **Basic:** Low casualty count (0-5)
- **Intermediate:** Moderate (5-20)
- **Advanced:** High (20+)

### 4. **Training Objectives**
- First aid training
- Triage (prioritizing casualties)
- Evacuation procedures
- Medical response

---

## 📊 Example Usage

### Scenario 1: Basic Level
- **Disaster:** Small fire in a building
- **Casualty Count:** 2-3
- **Reason:** Minor injuries, smoke inhalation

### Scenario 2: Intermediate Level
- **Disaster:** Earthquake in residential area
- **Casualty Count:** 10-15
- **Reason:** Building collapse, injuries from falling debris

### Scenario 3: Advanced Level
- **Disaster:** Major flood with building collapse
- **Casualty Count:** 50+
- **Reason:** Multiple buildings affected, drowning, injuries

---

## 🔗 Related Fields

### Victim Condition Presets:
May related field na "Victim Condition Presets" na naka-link sa casualty count:

- **Minor Injury** - Light casualties
- **Major Injury** - Serious casualties
- **Unconscious** - Critical casualties
- **Trapped** - Casualties that need rescue
- **Psychological Trauma** - Mental health casualties

**Relationship:**
- Casualty Count = Total number
- Victim Conditions = Types of casualties

---

## 💡 Sa AI Generation

### Current Implementation:
**⚠️ HINDI PA kasama sa AI generation**

Ang AI generate-scenario ay **hindi pa nagge-generate ng casualty count** automatically.

### Recommendation:
Dapat i-add sa AI generation:
- AI should suggest appropriate casualty count based on:
  - Disaster type
  - Difficulty level
  - Location type
  - Scenario complexity

### Example AI Logic:
```
If disaster_type = "earthquake" AND difficulty = "advanced":
    casualty_count = 20-50 (high)
    
If disaster_type = "fire" AND difficulty = "basic":
    casualty_count = 0-5 (low)
```

---

## 🎯 How It's Used in Training

### 1. **Scenario Setup**
- Trainer sets casualty count
- Determines how many "victims" (actors/props) needed
- Affects training resources

### 2. **During Simulation**
- Trainees encounter casualties
- Must assess and prioritize
- Practice triage and first aid

### 3. **Evaluation**
- How trainees handle casualties
- Response time
- Resource allocation
- Decision-making under pressure

---

## 📝 Current Status

### ✅ Implemented:
- Form field exists
- Required field
- Number input with validation
- Default value: 0

### ⚠️ Missing:
- Not included in AI generation
- Not saved to database (if using hardcoded data)
- Not used in scenario display

### 🔧 Recommendations:

1. **Add to AI Generation:**
   ```php
   // In generate-scenario.php prompt
   "casualty_count": "Suggested number based on disaster type and difficulty"
   ```

2. **Add to Database:**
   - Include in scenarios table
   - Store with scenario data

3. **Display in Scenario:**
   - Show casualty count in scenario preview
   - Use in training simulation

---

## 🎯 Summary

**Casualty Count** = Bilang ng mga nasugatan/namatay/nawawala sa disaster scenario.

**Purpose:**
- Make scenarios realistic
- Plan training resources
- Set difficulty level
- Practice casualty management

**Current Status:**
- ✅ Field exists in form
- ⚠️ Not yet integrated with AI generation
- ⚠️ May need database integration

**Next Steps:**
1. Add to AI generation prompt
2. Include in database schema
3. Use in scenario display/simulation

---

**Gusto mo bang i-add ang casualty count sa AI generation? Pwede kong gawin yun!** 🚀

