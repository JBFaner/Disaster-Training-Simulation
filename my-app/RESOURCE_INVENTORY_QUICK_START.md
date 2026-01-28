# Resource & Equipment Inventory - Quick Start Guide

## 🚀 Getting Started

Navigate to `/resources` in your LGU Disaster Preparedness system to access the Resource & Equipment Inventory module.

---

## 📌 Dashboard Tab

**What you see**: Overview of all resources with statistics

### Actions:
1. **Add New Resource** - Click "Add New Resource" button
   - Fill in name, category, quantity, condition, location
   - System generates serial number automatically

2. **Search** - Type resource name or serial number
   - Results filter in real-time

3. **Filter** - Use dropdowns to filter by:
   - Resource Type (PPE, Medical, Fire Equipment, etc.)
   - Status (Available, Reserved, In Use, etc.)
   - Condition (Good, Needs Repair, Damaged)

4. **Quick Actions** on each resource:
   - **Link Icon** - Assign to event (only if Available)
   - **Eye Icon** - View full usage history
   - **Edit Icon** - Update resource details
   - **Trash Icon** - Delete resource

---

## 🔗 Resources Tab - EVENT ASSIGNMENT & TRACKING

### What you see:
- Assignment workflow guide
- During-event usage tracking
- Currently assigned resources
- Post-event resource returns

### How to Assign Resources to an Event:

1. Go to **Dashboard** tab
2. Find the resource you want to assign
3. Click the **Link Icon** (only shows if status is "Available")
4. **Modal appears**:
   - Select simulation event
   - Enter quantity to assign
   - Choose resource handler (staff member)
5. Click **"Assign Resource"**
6. Status automatically changes: **Available → Reserved**

### During Event:

Resources marked as "Reserved" stay locked to that event. You can:
- View them in the "Currently Assigned Resources" section
- Check which event they're assigned to
- See who's responsible (handler)

### Return Resources After Event:

1. Go to **Resources** tab
2. Find resource in "Post-Event Resource Return" section
3. Click **"Return"** button
4. **Return dialog appears**:
   - Select condition after use (Good / Needs Repair / Damaged)
   - Add remarks if damaged (e.g., "Hose torn during drill")
5. Click **"Return Resource"**
6. System automatically:
   - Returns to inventory
   - Updates status: In Use → Available (or Maintenance if damaged)
   - Logs the return with condition notes

---

## 🔧 Maintenance Tab

### What you see:
- Count of items needing repair
- Count under maintenance
- List of resources requiring attention

### How to Schedule Maintenance:

1. View resource needing repair
2. Click **"Schedule Maintenance"**
3. **Dialog appears**:
   - Add maintenance notes (what's wrong)
   - Enter technician name (optional)
4. Click **"Schedule"**
5. Resource status changes: **Good → Under Maintenance**

### Track Maintenance:

- View maintenance logs in the "View History" modal
- See all work performed on each resource
- Track when maintenance was completed
- Keep record of who did the work

---

## 📊 Reports Tab

### What you can do:

1. **Export to CSV** - Download inventory as spreadsheet
   - All resources with current status
   - Import into Excel for analysis
   - Share with stakeholders

2. **View Inventory Summary**:
   - Total items in inventory
   - Unique resource types
   - Damaged items count
   - Items under maintenance

3. **See Resource Distribution**:
   - How many PPE items
   - How many Medical supplies
   - How many Vehicles, etc.

---

## 📋 Common Workflows

### Workflow 1: Preparing for a Simulation Event

```
1. Admin creates resources (if not already in system)
2. Admin views "Resources" tab
3. Admin clicks link icon on each needed resource
4. Admin assigns resources to the event
5. Resources marked "Reserved"
6. Handlers assigned to each resource
7. Event begins with locked-in equipment
```

### Workflow 2: During Simulation Event

```
1. Trainer can see assigned resources
2. Resources automatically marked "In Use" during event
3. Admin logs any damage/issues using "Report Damage" button
4. If resource breaks: Status changes to "Damaged"
5. Continue drilling with other equipment
```

### Workflow 3: After Simulation Event

```
1. Admin goes to "Resources" tab
2. Clicks "Return" on each assigned resource
3. Selects condition (Good or Damaged)
4. Adds remarks if needed
5. System returns all resources to inventory
6. If damaged: Automatically goes to "Under Maintenance"
7. If good: Back to "Available" for next event
```

### Workflow 4: Maintaining Equipment

```
1. Go to "Maintenance" tab
2. See all items needing repair
3. Click "Schedule Maintenance"
4. Add notes about what needs fixing
5. Assign technician
6. Track when maintenance is completed
7. View full history in resource's usage log
```

---

## 🎯 Tips & Best Practices

### Resource Categories (suggested)
- **PPE**: Safety helmets, vests, gloves, respirators
- **Fire Equipment**: Fire extinguishers, hoses, nozzles
- **Medical**: First aid kits, stretchers, defibrillators
- **Communication**: Megaphones, walkie-talkies, radios
- **Vehicles**: Ambulances, fire trucks, rescue vehicles
- **Tools**: Crowbars, axes, cutting tools
- **Other**: Miscellaneous items

### Serial Number Tips
- Use manufacturer serial numbers if available
- If none exists, system auto-generates one like "PPE-001-2025"
- Helps track specific items for damage/maintenance history

### Location Best Practices
- Use specific addresses: "Warehouse A, Shelf 3" (not just "Warehouse")
- Include room numbers if indoors
- Include depot/building name if multiple locations
- Helps find equipment quickly

### Status Meanings
- **Available** - Ready to use, not assigned
- **Reserved** - Assigned to upcoming event, waiting to be used
- **In Use** - Currently deployed during event
- **Under Maintenance** - Being repaired/serviced
- **Damaged** - Beyond quick repair, unavailable
- **Missing** - Can't locate, investigate

### Condition Guide
- **New** - Just purchased/added
- **Good** - Fully operational, no issues
- **Needs Repair** - Minor issues, can be fixed
- **Damaged** - Major damage, requires maintenance

---

## 🔍 How to Find Resources

### Using Search:
- Click in search box
- Type resource name or serial number
- Results appear instantly

### Using Filters:
- **Resource Type**: Select category (PPE, Medical, etc.)
- **Status**: Show only Available, In Use, Damaged, etc.
- **Condition**: Show only Good, Needs Repair, etc.
- Combine multiple filters for precise results

### Using History:
- Click eye icon on any resource
- See complete usage timeline
- Shows when assigned, deployed, returned
- Shows all maintenance work performed

---

## 📞 Need Help?

If a resource assignment fails:
- Check if resource has "Available" status
- Check if quantity is sufficient
- Verify event exists and is published
- Check if handler user exists

If maintenance won't schedule:
- Ensure resource has a name
- Ensure you're logged in
- Check that resource exists

If export doesn't work:
- Try different browser
- Check pop-up blocker settings
- Ensure you have CSV permissions

---

## ✅ System Ready!

Your Resource & Equipment Inventory system is fully operational. Start managing your disaster training resources today!

**Key URL**: `/resources`
