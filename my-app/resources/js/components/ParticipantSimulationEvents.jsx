import React from 'react';
import Swal from 'sweetalert2';
import { Search } from 'lucide-react';

// Date formatting utilities
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function formatTime(timeString) {
    if (!timeString) return '';
    // If already in HH:MM format
    if (timeString.match(/^\d{2}:\d{2}(:\d{2})?$/)) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
    return timeString;
}

export function ParticipantSimulationEventsList({ events }) {
    const [searchQuery, setSearchQuery] = React.useState('');
    const [registrationFilter, setRegistrationFilter] = React.useState('all');

    // Normalize and filter events
    const normalizedEvents = (events || []).map((event) => ({
        ...event,
        _title: (event.title || '').toLowerCase(),
        _disaster: (event.disaster_type || '').toLowerCase(),
    }));

    const filteredEvents = normalizedEvents.filter((event) => {
        const query = searchQuery.toLowerCase();
        const matchesSearch =
            query === '' ||
            event._title.includes(query) ||
            event._disaster.includes(query);

        const userRegistration = event.user_registration;
        const registrationStatus = userRegistration?.status || 'not_registered';
        
        let matchesFilter = true;
        if (registrationFilter === 'registered') {
            matchesFilter = registrationStatus === 'approved' || registrationStatus === 'pending';
        } else if (registrationFilter === 'not_registered') {
            matchesFilter = !userRegistration || registrationStatus === 'cancelled' || registrationStatus === 'rejected';
        }

        return matchesSearch && matchesFilter;
    });

    const searchSuggestions = searchQuery
        ? normalizedEvents
              .filter((event) =>
                  event._title.startsWith(searchQuery.toLowerCase()) ||
                  event._disaster.startsWith(searchQuery.toLowerCase())
              )
              .slice(0, 6)
        : [];

    return (
        <div className="space-y-4">
            <h2 className="text-lg font-semibold text-slate-800">Simulation Events</h2>
            <p className="text-sm text-slate-600 mb-2">
                Browse upcoming disaster preparedness drills and simulations. Register to participate.
            </p>

            {/* Search and Filter */}
            <div className="bg-white rounded-lg border border-slate-200 p-4">
                <div className="space-y-3">
                    {/* Search Bar with Dropdown */}
                    <div className="relative">
                        <div className="relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Search events by title or disaster type..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            />
                        </div>
                        {/* Search Suggestions Dropdown */}
                        {searchSuggestions.length > 0 && (
                            <div className="absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto">
                                {searchSuggestions.map((event) => (
                                    <button
                                        key={event.id}
                                        type="button"
                                        className="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50 border-b border-slate-100 last:border-0"
                                        onClick={() => setSearchQuery(event.title)}
                                    >
                                        <div className="font-medium text-slate-900">{event.title}</div>
                                        <div className="text-xs text-slate-500">
                                            {event.disaster_type}
                                        </div>
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Filter Tabs */}
                    <div className="flex gap-2">
                        <button
                            onClick={() => setRegistrationFilter('all')}
                            className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                registrationFilter === 'all'
                                    ? 'bg-slate-800 text-white'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                        >
                            All
                        </button>
                        <button
                            onClick={() => setRegistrationFilter('registered')}
                            className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                registrationFilter === 'registered'
                                    ? 'bg-slate-800 text-white'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                        >
                            Registered
                        </button>
                        <button
                            onClick={() => setRegistrationFilter('not_registered')}
                            className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                registrationFilter === 'not_registered'
                                    ? 'bg-slate-800 text-white'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                        >
                            Not registered
                        </button>
                    </div>
                </div>
            </div>

            {filteredEvents.length === 0 ? (
                <div className="rounded-xl bg-white border border-slate-200 px-4 py-6 text-sm text-slate-500 text-center shadow-sm">
                    No simulation events are available at this time. Please check back later.
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {filteredEvents.map((event) => {
                        const userRegistration = event.user_registration;
                        const isRegistered = !!userRegistration;
                        const registrationStatus = userRegistration?.status || null;
                        const isPending = registrationStatus === 'pending';
                        const isApproved = registrationStatus === 'approved';
                        const isCancelled = registrationStatus === 'cancelled';
                        const isRejected = registrationStatus === 'rejected';

                        const now = new Date();
                        
                        // Time-based status calculation (matching admin logic)
                        const hasEventEnded = () => {
                            if (!event.event_date || !event.end_time) return false;
                            const [h, m] = (event.end_time || '23:59').split(':').map((v) => Number(v) || 0);
                            const eventEnd = new Date(event.event_date);
                            eventEnd.setHours(h, m, 0, 0);
                            return now >= eventEnd;
                        };

                        const getDateTime = (dateStr, timeStr) => {
                            const dt = new Date(dateStr);
                            if (timeStr && timeStr.match(/^\d{2}:\d{2}/)) {
                                const [h, m] = timeStr.split(':').map(Number);
                                dt.setHours(h, m || 0, 0, 0);
                            }
                            return dt;
                        };

                        const eventStartTime = getDateTime(event.event_date, event.start_time);
                        
                        const serverStatus = event.status;
                        
                        // Match admin logic: time-based status takes precedence
                        const hasEnded = hasEventEnded() && (serverStatus === 'published' || serverStatus === 'ongoing');
                        const isOngoing = serverStatus === 'ongoing' && !hasEnded;
                        const isUpcoming = serverStatus === 'published' && !hasEnded && now < eventStartTime;

                        const statusBadge = isOngoing
                            ? 'Ongoing'
                            : hasEnded
                                ? 'Ended'
                                : 'Upcoming';

                        const cardBgClass = isOngoing
                            ? 'bg-emerald-700'
                            : hasEnded
                                ? 'bg-slate-600'
                                : 'bg-slate-700';
                        
                        return (
                            <div
                                key={event.id}
                                className={`rounded-lg ${cardBgClass} shadow-md p-5 flex flex-col gap-4 text-white h-[320px] overflow-hidden transition-colors`}
                            >
                                <div>
                                    <h3 className="text-base font-semibold mb-3 line-clamp-2">
                                        {event.title}
                                    </h3>
                                    <div className="inline-flex items-center rounded-full bg-black/20 px-3 py-1 text-xs font-medium mb-2">
                                        {statusBadge}
                                    </div>
                                    {event.description && (
                                        <p className="text-sm text-slate-300 leading-relaxed line-clamp-3">
                                            {event.description}
                                        </p>
                                    )}
                                </div>

                                <div className="flex flex-col gap-2 mt-auto">
                                    {event.disaster_type && (
                                        <div className="text-[0.75rem] text-slate-300 mb-1">
                                            <span>Disaster type: {event.disaster_type}</span>
                                        </div>
                                    )}
                                    {isRegistered && (isPending || isApproved) && (
                                        <button
                                            className="w-full py-2 rounded-md bg-slate-600 text-white text-sm font-medium hover:bg-slate-500 transition-colors"
                                        >
                                            Registered
                                        </button>
                                    )}
                                    <a
                                        href={`/simulation-events/${event.id}`}
                                        className="w-full py-2 rounded-md bg-white text-slate-800 text-sm font-medium text-center hover:bg-slate-100 transition-colors"
                                    >
                                        Details
                                    </a>
                                    {isUpcoming && event.self_registration_enabled && (!isRegistered || isCancelled || isRejected) && (
                                        <a
                                            href={`/simulation-events/${event.id}`}
                                            className="w-full py-2 rounded-md bg-emerald-600 text-white text-sm font-medium text-center hover:bg-emerald-700 transition-colors"
                                        >
                                            Register
                                        </a>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}

export function ParticipantSimulationEventDetail({ event, role }) {
    const csrf = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
    const userRegistration = event.registrations?.[0] || null;
    const isRegistered = !!userRegistration;
    const registrationStatus = userRegistration?.status || null;
    const isPending = registrationStatus === 'pending';
    const isApproved = registrationStatus === 'approved';
    const isCancelled = registrationStatus === 'cancelled';
    const isRejected = registrationStatus === 'rejected';
    
    // Check if event hasn't started yet (event date + start time)
    const now = new Date();
    const eventDateOnly = new Date(event.event_date);
    eventDateOnly.setHours(0, 0, 0, 0);
    
    // Parse start time (format: HH:MM)
    const [startHour, startMinute] = (event.start_time || '00:00').split(':').map(Number);
    const eventStartDateTime = new Date(event.event_date);
    eventStartDateTime.setHours(startHour, startMinute, 0, 0);
    
    // Event is upcoming if it hasn't started yet
    const isUpcoming = now < eventStartDateTime;
    const canRegister = isUpcoming && event.self_registration_enabled && (!isRegistered || isCancelled || isRejected);
    const canCancelRegistration = isRegistered && (isPending || isApproved);

    // Admin/Trainer: Calculate if Start Event button should be visible
    const todayOnly = new Date();
    todayOnly.setHours(0, 0, 0, 0);
    
    const eventStartTime = new Date(event.event_date);
    eventStartTime.setHours(startHour, startMinute, 0, 0);
    
    const isEventDateToday = eventDateOnly.getTime() === todayOnly.getTime();
    const isEventTimeReached = now >= eventStartTime;
    const canStartEvent = role !== 'PARTICIPANT' && event.status === 'published' && isEventDateToday && isEventTimeReached;

    const scenario = event.scenario;
    const trainingModule = scenario?.training_module;
    const lessons = trainingModule?.lessons || [];

    const handleRegisterSubmit = async (e) => {
        e.preventDefault();
        const result = await Swal.fire({
            title: 'Confirm Registration',
            text: 'Do you want to register for this simulation event?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, register me',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
        });
        if (result.isConfirmed) e.target.submit();
    };

    const handleCancelRegistrationSubmit = async (e) => {
        e.preventDefault();
        const result = await Swal.fire({
            title: 'Cancel Registration',
            text: 'Are you sure you want to cancel your registration?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel registration',
            cancelButtonText: 'Keep registration',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
        });
        if (result.isConfirmed) e.target.submit();
    };

    const handleStartEventSubmit = async (e) => {
        e.preventDefault();
        const result = await Swal.fire({
            title: 'Start Event',
            text: 'Start this simulation event? The status will change to Ongoing.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, start event',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#64748b',
        });
        if (result.isConfirmed) e.target.submit();
    };

    return (
        <div className="space-y-6">
            {/* Admin: Start Event Banner */}
            {role !== 'PARTICIPANT' && event.status === 'published' && (
                <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm font-semibold text-slate-800">
                                Event Status: Published
                            </p>
                            <p className="text-xs text-slate-600 mt-1">
                                {canStartEvent 
                                    ? 'The event is ready to start. Click the button to begin.' 
                                    : isEventDateToday && !isEventTimeReached
                                    ? `Event starts at ${event.start_time}. Button will appear when start time is reached.`
                                    : !isEventDateToday
                                    ? 'Event is scheduled for a future date. Button will appear on the event date.'
                                    : 'Event is ready to proceed.'}
                            </p>
                        </div>
                        {canStartEvent && (
                            <form method="POST" action={`/simulation-events/${event.id}/start`} onSubmit={handleStartEventSubmit}>
                                <input type="hidden" name="_token" value={csrf} />
                                <button
                                    type="submit"
                                    className="inline-flex items-center rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium px-4 py-2 transition-colors"
                                >
                                    Start Event
                                </button>
                            </form>
                        )}
                    </div>
                </div>
            )}

            {/* Admin: Complete Event Banner */}
            {role !== 'PARTICIPANT' && event.status === 'ongoing' && (
                <div className="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm font-semibold text-slate-800">
                                Event Status: Ongoing
                            </p>
                            <p className="text-xs text-slate-600 mt-1">
                                The event is currently in progress. Mark it as completed when the event has ended and attendance is finalized.
                            </p>
                        </div>
                        <form 
                            method="POST" 
                            action={`/simulation-events/${event.id}/complete`}
                            onSubmit={(e) => {
                                if (!confirm('Are you sure you want to mark this event as completed? This will allow evaluation to begin.')) {
                                    e.preventDefault();
                                }
                            }}
                        >
                            <input type="hidden" name="_token" value={csrf} />
                            <button
                                type="submit"
                                className="inline-flex items-center rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 transition-colors"
                            >
                                Complete Event
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {/* Registration Status Banner - only for participants */}
            {role === 'PARTICIPANT' && isRegistered && !isCancelled && (
                <div className={`rounded-xl border p-4 ${
                    isPending ? 'bg-amber-50 border-amber-200' :
                    isApproved ? 'bg-emerald-50 border-emerald-200' :
                    isRejected ? 'bg-rose-50 border-rose-200' :
                    'bg-slate-50 border-slate-200'
                }`}>
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-sm font-semibold text-slate-800">
                                {isPending ? '⏳ Registration Pending' :
                                 isApproved ? '✅ Registration Approved' :
                                 isRejected ? '❌ Registration Rejected' :
                                 'Registration Status'}
                            </p>
                            <p className="text-xs text-slate-600 mt-1">
                                {isPending ? 'Your registration is awaiting approval from the organizer.' :
                                 isApproved ? 'You are registered for this event. See you there!' :
                                 isRejected && userRegistration?.rejection_reason ? `Reason: ${userRegistration.rejection_reason}` :
                                 'Check your email for updates.'}
                            </p>
                        </div>
                        {canCancelRegistration && (
                            <form method="POST" action={`/simulation-events/${event.id}/cancel-registration`} onSubmit={handleCancelRegistrationSubmit}>
                                <input type="hidden" name="_token" value={csrf} />
                                <button
                                    type="submit"
                                    className="inline-flex items-center rounded-md border border-rose-300 bg-white hover:bg-rose-50 text-rose-700 text-xs font-medium px-3 py-1.5"
                                >
                                    Cancel Registration
                                </button>
                            </form>
                        )}
                    </div>
                </div>
            )}

            {/* Event Details */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 className="text-xl font-bold text-slate-800 mb-4">{event.title}</h2>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase mb-1">Disaster Type</p>
                        <p className="text-sm text-slate-800">{event.disaster_type}</p>
                    </div>
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase mb-1">Event Category</p>
                        <p className="text-sm text-slate-800">{event.event_category}</p>
                    </div>
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase mb-1">Date & Time</p>
                        <p className="text-sm text-slate-800">{formatDate(event.event_date)} — {formatTime(event.start_time)} to {formatTime(event.end_time)}</p>
                    </div>
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase mb-1">Duration</p>
                        <p className="text-sm text-slate-800">
                            {(() => {
                                const start = new Date(`2000-01-01 ${event.start_time}`);
                                const end = new Date(`2000-01-01 ${event.end_time}`);
                                const diffHours = Math.abs(end - start) / 36e5;
                                return `${diffHours} hour${diffHours !== 1 ? 's' : ''}`;
                            })()}
                        </p>
                    </div>
                </div>

                <div className="space-y-3">
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase mb-1">Location</p>
                        <p className="text-sm text-slate-800">{event.location || 'To be announced'}</p>
                        {event.building && <p className="text-xs text-slate-600">Building: {event.building}</p>}
                        {event.room_zone && <p className="text-xs text-slate-600">Room/Zone: {event.room_zone}</p>}
                    </div>
                    
                    {event.description && (
                        <div>
                            <p className="text-xs font-semibold text-slate-500 uppercase mb-1">Description</p>
                            <p className="text-sm text-slate-700 whitespace-pre-line">{event.description}</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Scenario Overview (Participant-Safe Version) */}
            {scenario && (
                <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 className="text-lg font-semibold text-slate-800 mb-3">⚠️ Scenario Overview</h3>
                    <p className="text-xs text-slate-500 mb-3 italic">
                        This is a brief overview to help you prepare. Full scenario details will be revealed during the drill.
                    </p>
                    
                    {scenario.short_description && (
                        <div className="mb-4">
                            <p className="text-sm text-slate-700 whitespace-pre-line">{scenario.short_description}</p>
                        </div>
                    )}

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        {scenario.severity_level && (
                            <div>
                                <span className="font-semibold text-slate-600">Severity Level:</span>
                                <span className="text-slate-700 ml-2">{scenario.severity_level}</span>
                            </div>
                        )}
                        {scenario.difficulty && (
                            <div>
                                <span className="font-semibold text-slate-600">Difficulty:</span>
                                <span className="text-slate-700 ml-2">{scenario.difficulty}</span>
                            </div>
                        )}
                    </div>
                </div>
            )}

            {/* Training Module Recommendations */}
            {trainingModule && lessons.length > 0 && (
                <div className="bg-blue-50 rounded-xl border border-blue-200 p-6">
                    <h3 className="text-lg font-semibold text-slate-800 mb-2">📘 Recommended Training Before This Drill</h3>
                    <p className="text-xs text-slate-600 mb-4">
                        Review these lessons to prepare for the simulation. This will help you understand what to expect and how to respond effectively.
                    </p>
                    
                    <div className="bg-white rounded-lg border border-blue-100 p-4">
                        <p className="text-sm font-semibold text-slate-800 mb-3">
                            <a href={`/training-modules/${trainingModule.id}`} className="text-blue-700 hover:text-blue-900 hover:underline">
                                ✔ Training Module: {trainingModule.title}
                            </a>
                        </p>
                        <ul className="space-y-2">
                            {lessons.slice(0, 5).map((lesson) => (
                                <li key={lesson.id} className="flex items-start gap-2 text-xs text-slate-700">
                                    <span className="text-blue-600 mt-0.5">→</span>
                                    <a href={`/training-modules/${trainingModule.id}#lesson-${lesson.id}`} className="hover:text-blue-700 hover:underline">
                                        Lesson {lesson.order}: {lesson.title}
                                    </a>
                                </li>
                            ))}
                            {lessons.length > 5 && (
                                <li className="text-xs text-slate-500 ml-4">+ {lessons.length - 5} more lessons</li>
                            )}
                        </ul>
                        <div className="mt-3 pt-3 border-t border-slate-100">
                            <a
                                href={`/training-modules/${trainingModule.id}`}
                                className="inline-flex items-center rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 transition-colors"
                            >
                                View Full Training Module
                            </a>
                        </div>
                    </div>
                </div>
            )}

            {/* Instructions & Reminders */}
            <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 className="text-lg font-semibold text-slate-800 mb-3">📋 Instructions & Reminders</h3>
                
                <div className="space-y-3 text-sm">
                    {event.safety_guidelines && (
                        <div>
                            <p className="font-semibold text-slate-700 mb-1">Safety Guidelines:</p>
                            <p className="text-slate-600 whitespace-pre-line">{event.safety_guidelines}</p>
                        </div>
                    )}
                    
                    {event.required_ppe && (
                        <div>
                            <p className="font-semibold text-slate-700 mb-1">Required PPE/Equipment:</p>
                            <p className="text-slate-600 whitespace-pre-line">{event.required_ppe}</p>
                        </div>
                    )}
                    
                    {event.hazard_warnings && (
                        <div>
                            <p className="font-semibold text-rose-700 mb-1">⚠️ Hazard Warnings:</p>
                            <p className="text-slate-600 whitespace-pre-line">{event.hazard_warnings}</p>
                        </div>
                    )}
                    
                    {event.assembly_points && (
                        <div>
                            <p className="font-semibold text-slate-700 mb-1">Assembly Points:</p>
                            <p className="text-slate-600 whitespace-pre-line">{event.assembly_points}</p>
                        </div>
                    )}
                    
                    {event.accessibility_notes && (
                        <div>
                            <p className="font-semibold text-slate-700 mb-1">Accessibility Notes:</p>
                            <p className="text-slate-600 whitespace-pre-line">{event.accessibility_notes}</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Register / Cancel Registration - only shown to participants */}
            {role === 'PARTICIPANT' && (
                <div className="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 className="text-lg font-semibold text-slate-800 mb-3">Registration</h3>
                    {canRegister ? (
                        <div>
                            <p className="text-sm text-slate-600 mb-4">Registration is open for this event. Click below to register.</p>
                            <form method="POST" action={`/simulation-events/${event.id}/register`} onSubmit={handleRegisterSubmit}>
                                <input type="hidden" name="_token" value={csrf} />
                                <button type="submit" className="inline-flex items-center rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 transition-colors">Register for This Event</button>
                            </form>
                        </div>
                    ) : !event.self_registration_enabled ? (
                        <p className="text-sm text-slate-600">Self-registration is not enabled for this event. Please contact the organizer for registration.</p>
                    ) : !isUpcoming ? (
                        <p className="text-sm text-slate-600">Registration is closed. This event has already occurred or is in progress.</p>
                    ) : isRegistered && !isCancelled ? (
                        <p className="text-sm text-slate-600">{isPending && 'Your registration is pending approval.'}{isApproved && 'You are registered for this event.'}{isRejected && 'Your registration was not approved.'}</p>
                    ) : (
                        <p className="text-sm text-slate-600">Registration is not available at this time.</p>
                    )}
                    {event.max_participants && (
                        <div className="mt-4 pt-4 border-t border-slate-100">
                            <p className="text-xs text-slate-500">Capacity: {event.registrations?.length || 0} / {event.max_participants} registered</p>
                        </div>
                    )}
                </div>
            )}

            {/* Back to Events */}
            <div>
                <a
                    href="/simulation-events"
                    className="inline-flex items-center text-sm text-slate-600 hover:text-slate-800 transition-colors"
                >
                    ← Back to Simulation Events
                </a>
            </div>
        </div>
    );
}
