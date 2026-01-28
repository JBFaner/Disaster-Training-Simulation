# Dashboard Mockup - Visual Layout Guide

## Dashboard Structure

```
┌─────────────────────────────────────────────────────────────────────┐
│                          DASHBOARD                                  │
│  Overview of training modules, simulation events, participants...   │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┬──────────────┐
│  📚 Training │  🎯 Sim      │  👥          │  ✅ System   │
│  Modules     │  Events      │  Participants│  Status      │
│              │              │              │              │
│  Total: 0    │  Total: 0    │  Total: 0    │  Operational│
│  Active: 0   │  Upcoming: 0 │  Active: 0   │  All nominal │
└──────────────┴──────────────┴──────────────┴──────────────┘

┌────────────────────────────────────┬──────────────────┐
│                                    │                  │
│  RECENT TRAINING MODULES           │  QUICK ACTIONS   │
│  ─────────────────────────────     │  ──────────────  │
│                                    │                  │
│  Module 1                          │  ➕ Create       │
│  Earthquake Preparedness           │     Module       │
│  · 5 lessons                       │                  │
│  [Active]                          │  🎯 Create       │
│                                    │     Scenario     │
│  Module 2                          │                  │
│  Flood Response                    │  📅 Schedule     │
│  · 3 lessons                       │     Event        │
│  [Active]                          │                  │
│                                    │  👥 View         │
│  Module 3                          │     Participants │
│  Tsunami Alert System              │                  │
│  · 4 lessons                       │  📊 View         │
│  [Draft]                           │     Results      │
│                                    │                  │
└────────────────────────────────────┴──────────────────┘

┌────────────────────────────────────────────────────────┐
│  UPCOMING SIMULATION EVENTS                            │
│  ────────────────────────────────────────────────────  │
│                                                        │
│  Emergency Response Drill                             │
│  📅 Jan 15, 2025 · ⏰ 09:00 AM · [Published]          │
│  5 registrations                                       │
│                                                        │
│  Community Safety Exercise                            │
│  📅 Jan 20, 2025 · ⏰ 10:00 AM · [Draft]              │
│  2 registrations                                       │
│                                                        │
│  Metro Evacuation Simulation                          │
│  📅 Feb 01, 2025 · ⏰ 08:00 AM · [Published]          │
│  8 registrations                                       │
│                                                        │
└────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────┐
│  COMING SOON 🚀                                        │
│  ────────────────────────────────────────────────────  │
│                                                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ 📱 Mobile    │  │ 🤖 AI        │  │ 📹 Video     │ │
│  │ App Integ.   │  │ Analytics    │  │ Integration  │ │
│  │              │  │              │  │              │ │
│  │ Real-time    │  │ Performance  │  │ Record &     │ │
│  │ check-in     │  │ insights &   │  │ review       │ │
│  │ notifications│  │ recommend.   │  │ sessions     │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ 🌐 API       │  │ 📊 Advanced  │  │ 🔐 Role      │ │
│  │ Access       │  │ Reporting    │  │ Dashboard    │ │
│  │              │  │              │  │              │ │
│  │ Third-party  │  │ Custom       │  │ Customized   │ │
│  │ integrations │  │ reports &    │  │ views per    │ │
│  │ & data sync  │  │ data exports │  │ user role    │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                        │
└────────────────────────────────────────────────────────┘
```

## Color Scheme

### Statistics Cards
- **Blue (Training):** `bg-gradient-to-br from-blue-50 to-blue-100`
- **Purple (Events):** `bg-gradient-to-br from-purple-50 to-purple-100`
- **Green (Participants):** `bg-gradient-to-br from-green-50 to-green-100`
- **Amber (Status):** `bg-gradient-to-br from-amber-50 to-amber-100`

### Status Badges
- **Active:** `bg-emerald-100 text-emerald-800` ✅
- **Draft:** `bg-yellow-100 text-yellow-800` 📝
- **Published:** `text-emerald-600` 🟢
- **Archived:** `bg-slate-100 text-slate-600` 📦

### Action Buttons
- **Create:** Blue background with hover effect
- **View:** Slate background with hover effect
- All have emoji prefixes for quick visual recognition

## Responsive Breakpoints

### Mobile (< 768px)
```
Single column layout
- Full width stat cards (stacked)
- Full width module list
- Full width actions sidebar
- Full width events list
```

### Tablet (768px - 1024px)
```
2-column stats
- 2 cards per row
- Left: Modules (1 col)
- Right: Actions (1 col)
- Full width events
```

### Desktop (> 1024px)
```
4-column stats
- All 4 cards in one row
- Left: Modules (2 cols)
- Right: Actions (1 col)
- Full width events
- Full width roadmap
```

## Empty States

### No Training Modules
```
📚 RECENT TRAINING MODULES

No training modules yet

[Create first module →]
```

### No Simulation Events
```
🎯 UPCOMING SIMULATION EVENTS

[No content - section hidden]
```

## Interactive Elements

### Hover Effects
- Stats cards: Subtle shadow increase
- Recent modules: Light background fade
- Action buttons: Darker background on hover
- Event rows: Light background fade

### Links
- All action buttons and quick links are clickable
- Navigate to: `/training-modules/create`, `/scenarios/create`, etc.
- All links are role-aware (admins see create options)

## Real-Time Updates

Dashboard stats update automatically when:
- New training module is created
- Simulation event is published
- Participant registers
- Event changes status

Just refresh the dashboard or navigate back to see updated numbers!

## Future Enhancements Roadmap

Listed in the bottom section with descriptions:

1. **Mobile App** - Push notifications, biometric check-in
2. **AI Analytics** - ML-based performance predictions
3. **Video Capture** - Session recordings and playback
4. **API Platform** - Webhook integrations, data APIs
5. **Custom Reports** - Scheduled exports, drill-down analytics
6. **Role Views** - ADMIN, TRAINER, PARTICIPANT specialized dashboards

---

**Status:** ✅ Ready to deploy
**Last Updated:** December 20, 2025
**Build Size:** +8KB (compressed)
