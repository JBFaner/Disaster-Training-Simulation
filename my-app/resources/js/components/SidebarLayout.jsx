import React from 'react';
import Swal from 'sweetalert2';
import * as ScrollArea from '@radix-ui/react-scroll-area';
import {
    LayoutDashboard,
    BookOpen,
    Activity,
    CalendarClock,
    Users,
    Box,
    ClipboardList,
    Award,
    ShieldCheck,
    ClipboardCheck,
    LogOut,
    UserCircle,
    ChevronLeft,
    ChevronRight,
    MoreVertical,
    X,
    Settings,
} from 'lucide-react';
import { TopBar } from './TopBar';

export function SidebarLayout({ role, currentSection = 'dashboard', children, moduleName, breadcrumbs, user }) {
    // Load collapsed state from localStorage, default to false
    const [isCollapsed, setIsCollapsed] = React.useState(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('sidebarCollapsed');
            return saved === 'true';
        }
        return false;
    });

    // Save collapsed state to localStorage whenever it changes
    React.useEffect(() => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('sidebarCollapsed', String(isCollapsed));
        }
    }, [isCollapsed]);
    // Mobile drawer states
    const [isLeftDrawerOpen, setIsLeftDrawerOpen] = React.useState(false);
    const [isRightDrawerOpen, setIsRightDrawerOpen] = React.useState(false);
    const leftDrawerRef = React.useRef(null);
    const rightDrawerRef = React.useRef(null);

    const isAdmin = role === 'LGU_ADMIN';
    const isTrainer = role === 'LGU_TRAINER';
    const isParticipant = role === 'PARTICIPANT';

    // Close drawer when clicking outside or on overlay
    React.useEffect(() => {
        const handleClickOutside = (event) => {
            // Close left drawer if clicking outside
            if (isLeftDrawerOpen && leftDrawerRef.current && !leftDrawerRef.current.contains(event.target)) {
                setIsLeftDrawerOpen(false);
            }
            // Close right drawer if clicking outside
            if (isRightDrawerOpen && rightDrawerRef.current && !rightDrawerRef.current.contains(event.target)) {
                setIsRightDrawerOpen(false);
            }
        };

        if (isLeftDrawerOpen || isRightDrawerOpen) {
            // Use setTimeout to avoid immediate closing when opening
            const timeoutId = setTimeout(() => {
                document.addEventListener('mousedown', handleClickOutside);
            }, 100);
            // Prevent body scroll when drawer is open
            document.body.style.overflow = 'hidden';

            return () => {
                clearTimeout(timeoutId);
                document.removeEventListener('mousedown', handleClickOutside);
                document.body.style.overflow = '';
            };
        } else {
            document.body.style.overflow = '';
        }
    }, [isLeftDrawerOpen, isRightDrawerOpen]);

    // Close one drawer when opening the other
    const openLeftDrawer = () => {
        setIsRightDrawerOpen(false);
        setIsLeftDrawerOpen(true);
    };

    const openRightDrawer = () => {
        setIsLeftDrawerOpen(false);
        setIsRightDrawerOpen(true);
    };

    const handleLogoutClick = (event) => {
        event.preventDefault();

        const form = event.currentTarget.form;
        if (!form) return;

        Swal.fire({
            title: 'Logout',
            text: 'Are you sure you want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-md text-sm font-medium',
                cancelButton: 'rounded-md text-sm font-medium',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    };

    return (
        <div className="min-h-screen bg-slate-100 text-slate-900 flex">
            {/* Mobile Top App Bar */}
            <header className="md:hidden fixed top-0 left-0 right-0 h-14 bg-slate-900 text-slate-50 flex items-center justify-between px-4 z-50 shadow-lg">
                {/* Left Navigation Toggle */}
                <button
                    data-left-drawer-toggle
                    onClick={openLeftDrawer}
                    className="p-2 -ml-2 text-slate-300 hover:text-slate-100 transition-colors"
                    aria-label="Open navigation menu"
                >
                    <ChevronRight className="w-5 h-5" />
                </button>

                {/* Center Branding */}
                <div className="flex items-center gap-2">
                    <img src="/logo.svg" alt="LGU Logo" className="h-8 w-auto" />
                    <div className="leading-tight">
                        <div className="text-xs font-semibold tracking-wide">
                            Disaster Preparedness
                        </div>
                        <div className="text-[0.65rem] text-slate-400">
                            Training &amp; Simulation
                        </div>
                    </div>
                </div>

                {/* Right Overflow Menu */}
                <button
                    data-right-drawer-toggle
                    onClick={openRightDrawer}
                    className="p-2 -mr-2 text-slate-300 hover:text-slate-100 transition-colors"
                    aria-label="Open account menu"
                >
                    <MoreVertical className="w-5 h-5" />
                </button>
            </header>

            {/* Mobile Overlay Backdrop */}
            {(isLeftDrawerOpen || isRightDrawerOpen) && (
                <div
                    className="md:hidden fixed inset-0 bg-black/50 z-40 transition-opacity"
                    onClick={() => {
                        setIsLeftDrawerOpen(false);
                        setIsRightDrawerOpen(false);
                    }}
                />
            )}

            {/* Mobile Left Navigation Drawer */}
            <aside
                ref={leftDrawerRef}
                className={`md:hidden fixed top-0 left-0 h-full w-80 bg-slate-900 text-slate-50 flex-col z-50 transform transition-transform duration-300 ease-in-out ${isLeftDrawerOpen ? 'translate-x-0' : '-translate-x-full'
                    }`}
            >
                {/* Drawer Header */}
                <div className="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                    <div className="flex items-center gap-3">
                        <img src="/logo.svg" alt="LGU Logo" className="h-10 w-auto" />
                        <div className="leading-tight">
                            <div className="text-sm font-semibold tracking-wide">
                                Disaster Preparedness
                            </div>
                            <div className="text-[0.7rem] text-slate-400">
                                Training &amp; Simulation
                            </div>
                        </div>
                    </div>
                    <button
                        onClick={() => setIsLeftDrawerOpen(false)}
                        className="p-2 text-slate-400 hover:text-slate-100 transition-colors"
                        aria-label="Close navigation menu"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Drawer Navigation */}
                <ScrollArea.Root className="flex-1 overflow-hidden min-h-0">
                    <ScrollArea.Viewport className="h-full px-3 py-4">
                        <nav className="space-y-5 text-sm">
                            {renderNavigationItems(role, currentSection, false, () => setIsLeftDrawerOpen(false))}
                        </nav>
                    </ScrollArea.Viewport>
                    <ScrollArea.Scrollbar
                        orientation="vertical"
                        className="flex select-none touch-none p-0.5 bg-slate-900/80"
                    >
                        <ScrollArea.Thumb className="flex-1 rounded-full bg-slate-700" />
                    </ScrollArea.Scrollbar>
                </ScrollArea.Root>
            </aside>

            {/* Mobile Right Action Drawer */}
            <aside
                ref={rightDrawerRef}
                className={`md:hidden fixed top-0 right-0 h-full w-72 bg-slate-900 text-slate-50 flex-col z-50 transform transition-transform duration-300 ease-in-out ${isRightDrawerOpen ? 'translate-x-0' : 'translate-x-full'
                    }`}
            >
                {/* Drawer Header */}
                <div className="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                    <div className="flex items-center gap-3">
                        <div className="h-10 w-10 rounded-full bg-slate-800 flex items-center justify-center">
                            <UserCircle className="w-6 h-6 text-slate-400" />
                        </div>
                        <div className="leading-tight">
                            <div className="text-sm font-semibold">
                                {role === 'LGU_ADMIN' && 'LGU Admin'}
                                {role === 'LGU_TRAINER' && 'Trainer'}
                                {role === 'PARTICIPANT' && 'Participant'}
                            </div>
                            <div className="text-xs text-slate-400">
                                Account
                            </div>
                        </div>
                    </div>
                    <button
                        onClick={() => setIsRightDrawerOpen(false)}
                        className="p-2 text-slate-400 hover:text-slate-100 transition-colors"
                        aria-label="Close account menu"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Drawer Actions */}
                <div className="flex-1 px-4 py-4 space-y-2">
                    <a
                        href="/profile"
                        onClick={() => setIsRightDrawerOpen(false)}
                        className="flex items-center gap-3 px-4 py-3 rounded-md text-slate-100 hover:bg-slate-800/80 hover:text-emerald-200 transition-colors"
                    >
                        <UserCircle className="w-5 h-5 text-emerald-400" />
                        <span className="text-sm font-medium">Profile</span>
                    </a>
                    <a
                        href="#"
                        onClick={() => setIsRightDrawerOpen(false)}
                        className="flex items-center gap-3 px-4 py-3 rounded-md text-slate-100 hover:bg-slate-800/80 hover:text-emerald-200 transition-colors"
                    >
                        <Settings className="w-5 h-5 text-emerald-400" />
                        <span className="text-sm font-medium">Account Settings</span>
                    </a>
                    <div className="border-t border-slate-800 my-2"></div>
                    <form method="POST" action="/logout" className="w-full">
                        <input type="hidden" name="_token" value={document.head.querySelector('meta[name="csrf-token"]')?.content || ''} />
                        <button
                            type="submit"
                            onClick={(e) => {
                                setIsRightDrawerOpen(false);
                                handleLogoutClick(e);
                            }}
                            className="w-full flex items-center gap-3 px-4 py-3 rounded-md text-slate-100 hover:bg-slate-800/80 hover:text-rose-400 transition-colors"
                        >
                            <LogOut className="w-5 h-5 text-rose-400" />
                            <span className="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            {/* Sidebar - desktop-only */}
            <aside className={`hidden md:flex md:h-screen md:fixed md:left-0 md:top-0 bg-slate-900 text-slate-50 flex-col z-10 transition-all duration-300 ${isCollapsed ? 'md:w-20' : 'md:w-80'}`}>
                {/* Brand */}
                <div className="flex items-center gap-3 px-6 py-4 border-b border-slate-800 shrink-0 relative">
                    {!isCollapsed && (
                        <>
                            <img src="/logo.svg" alt="LGU Logo" className="h-10 w-auto" />
                            <div className="leading-tight">
                                <div className="text-sm font-semibold tracking-wide">
                                    Disaster Preparedness
                                </div>
                                <div className="text-[0.7rem] text-slate-400">
                                    Training &amp; Simulation
                                </div>
                            </div>
                        </>
                    )}
                    {isCollapsed && (
                        <img src="/logo.svg" alt="LGU Logo" className="h-10 w-auto mx-auto" />
                    )}
                    {/* Toggle Button - Hidden since it's now in TopBar */}
                    {/* <button
                        onClick={() => setIsCollapsed(!isCollapsed)}
                        className="absolute -right-3 top-1/2 -translate-y-1/2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-slate-100 rounded-full p-1.5 border border-slate-700 transition-colors z-20"
                        title={isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'}
                    >
                        {isCollapsed ? (
                            <ChevronRight className="w-4 h-4" />
                        ) : (
                            <ChevronLeft className="w-4 h-4" />
                        )}
                    </button> */}
                </div>

                {/* Nav */}
                <ScrollArea.Root className="flex-1 overflow-hidden min-h-0">
                    <ScrollArea.Viewport className="h-full px-3 py-4">
                        <nav className={`space-y-5 text-sm ${isCollapsed ? 'flex flex-col items-center' : ''}`}>
                            {renderNavigationItems(role, currentSection, isCollapsed)}
                        </nav>
                    </ScrollArea.Viewport>
                    <ScrollArea.Scrollbar
                        orientation="vertical"
                        className="flex select-none touch-none p-0.5 bg-slate-900/80"
                    >
                        <ScrollArea.Thumb className="flex-1 rounded-full bg-slate-700" />
                    </ScrollArea.Scrollbar>
                </ScrollArea.Root>

                {/* User footer */}
                <div className={`border-t border-slate-800 px-4 py-3 flex items-center justify-between gap-2 text-xs shrink-0 ${isCollapsed ? 'flex-col gap-3' : ''}`}>
                    {!isCollapsed && (
                        <div className="flex items-center gap-2 min-w-0">
                            <img src="/logo.svg" alt="LGU Logo" className="h-5 w-auto opacity-80 shrink-0" />
                            <div className="truncate">
                                <span className="text-slate-400">
                                    {role === 'LGU_ADMIN' && 'LGU Admin'}
                                    {role === 'LGU_TRAINER' && 'Trainer'}
                                    {role === 'PARTICIPANT' && 'Participant'}
                                </span>
                            </div>
                        </div>
                    )}
                    {isCollapsed && (
                        <img src="/logo.svg" alt="LGU Logo" className="h-5 w-auto opacity-80 mx-auto" />
                    )}
                    <form method="POST" action="/logout" className={isCollapsed ? 'w-full flex justify-center' : ''}>
                        <input type="hidden" name="_token" value={document.head.querySelector('meta[name="csrf-token"]')?.content || ''} />
                        <button
                            type="submit"
                            onClick={handleLogoutClick}
                            className={`inline-flex items-center gap-1.5 text-slate-400 hover:text-rose-400 transition-colors shrink-0 ${isCollapsed ? 'justify-center' : ''}`}
                            title="Logout"
                        >
                            <LogOut className="w-4 h-4" />
                            {!isCollapsed && <span className="hidden xl:inline">Logout</span>}
                        </button>
                    </form>
                </div>
            </aside>

            {/* Main content */}
            <main className={`flex-1 transition-all duration-300 pt-14 md:pt-0 ${isCollapsed ? 'md:ml-20' : 'md:ml-80'}`}>
                <TopBar
                    moduleName={moduleName}
                    breadcrumbs={breadcrumbs}
                    user={user}
                    onSidebarToggle={() => setIsCollapsed(!isCollapsed)}
                    isSidebarCollapsed={isCollapsed}
                />
                {/* Breadcrumbs - Below TopBar */}
                {breadcrumbs && breadcrumbs.length > 0 && (
                    <div className="bg-white border-b border-slate-200 px-6 py-2">
                        <nav className="flex items-center gap-1.5 text-xs text-slate-600">
                            {breadcrumbs.map((crumb, index) => (
                                <React.Fragment key={index}>
                                    {index > 0 && (
                                        <span className="text-slate-400">/</span>
                                    )}
                                    {crumb.href ? (
                                        <a
                                            href={crumb.href}
                                            className="hover:text-slate-900 hover:underline underline-offset-2 transition-colors"
                                        >
                                            {crumb.label}
                                        </a>
                                    ) : (
                                        <span className="text-slate-900 font-medium">
                                            {crumb.label}
                                        </span>
                                    )}
                                </React.Fragment>
                            ))}
                        </nav>
                    </div>
                )}
                <div className="p-6">
                    {children}
                </div>
            </main>
        </div>
    );
}

// Reusable navigation items renderer
function renderNavigationItems(role, currentSection, isCollapsed, onNavigate) {
    const isAdmin = role === 'LGU_ADMIN';
    const isTrainer = role === 'LGU_TRAINER';
    const isParticipant = role === 'PARTICIPANT';

    if (isParticipant) {
        return (
            <>
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Overview</NavSectionTitle>}
                    <NavItem
                        icon={LayoutDashboard}
                        label="Dashboard"
                        href="/dashboard"
                        active={currentSection === 'dashboard'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Training</NavSectionTitle>}
                    <NavItem
                        icon={BookOpen}
                        label="Training Modules"
                        href="/training-modules"
                        active={currentSection === 'training'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Events</NavSectionTitle>}
                    <NavItem
                        icon={CalendarClock}
                        label="Simulation Events"
                        href="/simulation-events"
                        active={currentSection === 'simulation'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Participation</NavSectionTitle>}
                    <NavItem
                        icon={ClipboardCheck}
                        label="My Attendance"
                        href="/my-attendance"
                        active={currentSection === 'participants'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Performance</NavSectionTitle>}
                    <NavItem
                        icon={ClipboardList}
                        label="Evaluation Results"
                        href="/evaluations"
                        active={currentSection === 'evaluation'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Certification</NavSectionTitle>}
                    <NavItem
                        icon={Award}
                        label="My Certificates"
                        href="/certification"
                        active={currentSection === 'certification'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
            </>
        );
    }

    return (
        <>
            <div className={isCollapsed ? 'w-full' : ''}>
                {!isCollapsed && <NavSectionTitle>Overview</NavSectionTitle>}
                <NavItem
                    icon={LayoutDashboard}
                    label="Dashboard"
                    href="/dashboard"
                    active={currentSection === 'dashboard'}
                    isCollapsed={isCollapsed}
                />
            </div>
            {(isAdmin || isTrainer) && (
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Operations</NavSectionTitle>}
                    <NavItem
                        icon={BookOpen}
                        label="Training Module Management"
                        href="/training-modules"
                        active={currentSection === 'training'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={Activity}
                        label="Scenario-based Exercise Design"
                        href="/scenarios"
                        active={currentSection === 'scenario'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={CalendarClock}
                        label="Simulation Event Planning"
                        href="/simulation-events"
                        active={currentSection === 'simulation'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={Box}
                        label="Resource & Equipment Inventory"
                        href="/resources"
                        active={currentSection === 'resources'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={Users}
                        label="Participant Registration & Attendance"
                        href="/participants"
                        active={currentSection === 'participants'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={ClipboardList}
                        label="Evaluation & Scoring System"
                        href="/evaluations"
                        active={currentSection === 'evaluation'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={Award}
                        label="Certification Issuance"
                        href="/certification"
                        active={currentSection === 'certification'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
            )}
            {isAdmin && (
                <div className={isCollapsed ? 'w-full' : ''}>
                    {!isCollapsed && <NavSectionTitle>Administration</NavSectionTitle>}
                    <NavItem
                        icon={Settings}
                        label="Barangay Profile"
                        href="/barangay-profile"
                        active={currentSection === 'barangay_profile'}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={ShieldCheck}
                        label="Users & Roles"
                        href="#"
                        active={false}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                    <NavItem
                        icon={ClipboardCheck}
                        label="Audit Logs"
                        href="#"
                        active={false}
                        isCollapsed={isCollapsed}
                        onNavigate={onNavigate}
                    />
                </div>
            )}
        </>
    );
}

function NavItem({ icon: Icon, label, href, active, isCollapsed, onNavigate }) {
    const handleClick = () => {
        if (onNavigate) {
            onNavigate();
        }
    };

    return (
        <a
            href={href}
            onClick={handleClick}
            className={[
                'w-full flex items-center gap-3 px-3 py-2 rounded-md transition-colors',
                isCollapsed ? 'justify-center' : 'text-left',
                active
                    ? 'bg-slate-800 text-emerald-300'
                    : 'text-slate-100 hover:bg-slate-800/80 hover:text-emerald-200',
            ].join(' ')}
            title={isCollapsed ? label : undefined}
        >
            <Icon className="w-4 h-4 text-emerald-400 shrink-0" />
            {!isCollapsed && <span className="text-[0.86rem] font-medium">{label}</span>}
        </a>
    );
}

function NavSectionTitle({ children }) {
    return (
        <div className="px-3 pb-1 text-[0.7rem] font-semibold tracking-[0.12em] text-slate-500 uppercase">
            {children}
        </div>
    );
}


