# Resource & Equipment Inventory - Quick Reference

## 🎯 System Purpose

The Resource & Equipment Inventory is directly connected to Simulation Events. It allows LGU admins to:
- ✅ Know what equipment they have
- ✅ Know what's available
- ✅ Assign equipment to specific simulation events
- ✅ Avoid double-booking resources
- ✅ Track usage, damage, and availability

## 📊 Resource Status Flow

```
┌─────────────┐
│ AVAILABLE   │ ← New resource or after maintenance
└──────┬──────┘
       │ (Assigned to event)
       ↓
┌──────────────┐
│ RESERVED     │ ← Locked to event, can't be used elsewhere
└──────┬───────┘
       │ (Event starts)
       ↓
┌──────────────┐
│ IN USE       │ ← Currently deployed in simulation
└──────┬───────┘
       │ (Event ends)
       ↓
    ┌──────────────────────────────┐
    │ Was it damaged?             │
    └───┬──────────────────────┬───┘
        │ NO                   │ YES
        ↓                      ↓
    ┌─────────────┐       ┌────────────────┐
    │ AVAILABLE   │       │ DAMAGED/        │
    │ (Returned)  │       │ MAINTENANCE     │
    └─────────────┘       └────────────────┘
                              │
                              │ (After repair)
                              ↓
                          ┌─────────────┐
                          │ AVAILABLE   │
                          └─────────────┘
```

## 🏗️ Database Tables

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `resources` | Master inventory | id, resource_id (R-001), name, category, quantity, status |
| `simulation_event_resources` | Event assignments | event_id, resource_id, quantity_required, status, quantity_used, quantity_damaged |
| `resource_event_logs` | Audit trail | action (assigned/deployed/returned/damaged), recorded_by, timestamp |

## 🔗 Key Relationships

```
Simulation Event (1) ─→ (Many) SimulationEventResource ─→ (1) Resource
                              ↓
                       (Many) ResourceEventLog
                       (Audit trail for tracking)
```

## 📱 React Components

| Component | Location | Purpose |
|-----------|----------|---------|
| **ResourceInventory** | `/resources/js/pages/Resources/ResourceInventory.jsx` | Main dashboard, view/add/edit resources |
| **ResourceAssignmentModal** | `/resources/js/components/ResourceAssignmentModal.jsx` | Assign resources to events |
| **PostEventResourceUpdate** | `/resources/js/components/PostEventResourceUpdate.jsx` | Track usage and damage post-event |
| **ResourceReports** | `/resources/js/pages/Resources/ResourceReports.jsx` | Analytics and reporting |

## 🛠️ Controller Actions

| Controller | Method | Purpose |
|-----------|--------|---------|
| **ResourceController** | index() | List resources with filters |
| **ResourceController** | store() | Create new resource |
| **ResourceController** | update() | Update resource details |
| **ResourceController** | archive() | Archive (soft delete) resource |
| **EventResourceController** | assignResources() | Assign to event |
| **EventResourceController** | updateUsage() | Track usage/damage |
| **EventResourceController** | report() | Generate analytics |

## 🔗 API Routes

```
# Core Resource APIs
GET    /resources/api                              # List all resources
POST   /resources                                  # Create resource
PUT    /resources/{id}                             # Update resource
POST   /resources/{id}/archive                     # Archive resource

# Event-Resource APIs
GET    /api/events/{eventId}/resources             # Get event's assigned resources
POST   /api/events/{eventId}/assign-resources      # Assign resources to event
POST   /api/events/{eventId}/resources/{id}/usage  # Update usage during event
GET    /api/resources/availability                 # Check date availability
GET    /api/resources/report                       # Get analytics report
```

## 💾 Database Migration

File: `database/migrations/2025_12_20_000001_create_simulation_event_resource_link.php`

Migration creates:
- ✅ `simulation_event_resources` table
- ✅ `resource_event_logs` table
- ✅ Enhanced `resources` table with tracking columns

## 🚀 Admin Workflow

### 1. Add Resource to Inventory
```
Dashboard → Resources → "Add Resource" Button
├─ Name: "Fire Extinguisher"
├─ Category: "Fire Safety"
├─ Quantity: 15
├─ Condition: "Good"
├─ Location: "Equipment Storage A"
└─ System auto-generates ID: "R-012"
```

### 2. Create Simulation Event
```
Events → New Event → Fill event details
Status: "Available" (no resources yet)
```

### 3. Assign Resources to Event
```
Event Details → "Assign Resources" Button
ResourceAssignmentModal Opens:
├─ Select: "Fire Extinguisher (R-012)"
├─ Quantity: 10
├─ Purpose: "Primary fire suppression"
├─ Click "Add Resource"
└─ Click "Save Assignments"
Result: Resources status = "Reserved"
```

### 4. Event Execution
```
Event Day:
├─ Resources confirmed deployed
├─ Status changes: Reserved → In Use
└─ Real-time damage tracking (if issues occur)
```

### 5. Post-Event Update
```
After Event:
PostEventResourceUpdate Modal:
├─ Fire Extinguisher: 
│  ├─ Quantity Used: 9
│  ├─ Damaged: 1
│  ├─ Remarks: "Hose damaged, needs new one"
│  └─ Click "Save & Return"
└─ System updates:
   ├─ Status: In Use → Returned
   ├─ last_used_event: Set to this event
   ├─ times_used: Incremented
   └─ Audit log entry created
```

### 6. View Analytics
```
Reports Dashboard:
├─ Date Range: "Last 30 days"
├─ View:
│  ├─ Total assignments: 45
│  ├─ Total resources used: 127
│  ├─ Total damaged: 3
│  └─ By Category breakdown
└─ Export CSV for records
```

## 📋 Resource Categories

- Fire Safety
- Medical
- Rescue
- Communication
- PPE (Personal Protective Equipment)
- Vehicle
- Other

## 🎯 Resource Statuses

| Status | Meaning | Can be used? |
|--------|---------|------------|
| **Available** | Ready to use | ✅ Yes |
| **Reserved** | Assigned to event | ❌ No (locked) |
| **In Use** | Currently deployed | ❌ No |
| **Damaged** | Needs repair | ❌ No |
| **Under Maintenance** | Being serviced | ❌ No |
| **Archived** | Deactivated | ❌ No |

## 🔍 Filter & Search

Resources can be filtered by:
- 🔎 **Search**: Name or Resource ID (R-001, etc.)
- 📂 **Category**: Fire Safety, Medical, Rescue, etc.
- 🏷️ **Status**: Available, Reserved, In Use, etc.

## 📊 Reports Include

1. **Summary Cards**
   - Total assignments
   - Total resources used
   - Total damaged
   
2. **By Category**
   - Count of assignments
   - Usage frequency
   - Damage incidents

3. **By Status**
   - Current distribution
   - Available vs. reserved

4. **Exportable As**
   - CSV (spreadsheet)
   - PDF (soon)

## 🔐 Permissions

| User Type | Can Do |
|-----------|--------|
| **Admin/Trainer** | ✅ Full CRUD, assign, track usage, view reports |
| **Participant** | ❌ Can only see event equipment (future feature) |
| **System** | ✅ Auto-prevent double-booking, track changes |

## 🐛 Audit Trail

Every action is logged:
- Who made it
- When it happened
- What changed
- Why (purpose field)

Example log entry:
```
Action: deployed
Quantity: 9
Recorded by: admin@lgu.gov.ph
Date: 2025-12-20 14:30:00
Notes: "Resources deployed for earthquake drill"
```

## 💡 Key Features

✅ **Auto-increment IDs** - Resources get unique IDs (R-001, R-002, etc.)
✅ **Prevent Double-booking** - Same resource can't be assigned to overlapping events
✅ **Usage Tracking** - See which event used which resource
✅ **Damage Recording** - Document maintenance needs
✅ **Last Used Date** - Track equipment aging
✅ **Soft Delete** - Archive instead of permanently delete
✅ **Real-time Availability** - Check before assigning
✅ **Export Reports** - CSV for LGU records

## 🎓 Data Models

### Resource
```php
{
  "id": 1,
  "resource_id": "R-001",
  "name": "Fire Extinguisher",
  "category": "Fire Safety",
  "quantity": 15,
  "condition": "Good",
  "status": "Available",
  "location": "Equipment Storage A",
  "last_used_event_id": 5,
  "last_used_at": "2025-12-20T14:00:00Z",
  "times_used": 3
}
```

### Event Assignment
```php
{
  "id": 1,
  "simulation_event_id": 5,
  "resource_id": 1,
  "quantity_required": 10,
  "quantity_used": 9,
  "quantity_damaged": 1,
  "purpose": "Primary fire suppression",
  "status": "returned",
  "remarks": "Hose damaged, needs replacement",
  "assigned_at": "2025-12-15T09:00:00Z",
  "returned_at": "2025-12-20T15:30:00Z"
}
```

## 🧪 Testing Scenarios

1. ✅ Add resource → Verify auto-generated ID
2. ✅ Assign to event → Verify status changes to Reserved
3. ✅ Try assign same resource to overlapping event → Should prevent
4. ✅ Mark as deployed → Verify status changes to In Use
5. ✅ Record damage → Verify audit log entry
6. ✅ Return resource → Verify last_used_event updates
7. ✅ Generate report → Verify correct statistics
8. ✅ Archive resource → Verify soft delete (not removed)
9. ✅ Export CSV → Verify file downloads

## 📱 UI Locations

- **Main Inventory**: `/resources`
- **Add/Edit Resource**: Modal within ResourceInventory
- **Assign to Event**: Called from event details page
- **Post-Event Update**: Called after event completion
- **Reports**: Separate Reports section (future: `/reports`)

## 🔗 Connection to Events

The system is **directly integrated** with Simulation Events:
1. Events display assigned resources
2. Resources can't be double-booked
3. Post-event cleanup is automated
4. Usage automatically tracked
5. Damage feeds maintenance pipeline

## ✨ What Makes It Realistic

- ✅ Resources are **reserved** (not permanently allocated)
- ✅ Damage is **documented** (maintenance tracking)
- ✅ **Audit trail** for compliance
- ✅ **Availability checking** prevents conflicts
- ✅ **Post-event assessment** ensures readiness for next drill
- ✅ **Reports** for LGU record-keeping

---

**Quick Reference Guide v1.0**
**System Ready for Production**
**December 20, 2025**
