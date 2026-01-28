import React from 'react';
import Swal from 'sweetalert2';
import { Bell, ChevronDown, User, Settings, LogOut, Clock, PanelLeft } from 'lucide-react';

export function TopBar({ moduleName, breadcrumbs, user, onSidebarToggle, isSidebarCollapsed }) {
    const [currentTime, setCurrentTime] = React.useState(new Date());
    const [showProfileMenu, setShowProfileMenu] = React.useState(false);

    // Update time every second
    React.useEffect(() => {
        const timer = setInterval(() => {
            setCurrentTime(new Date());
        }, 1000);
        return () => clearInterval(timer);
    }, []);

    // Format time in 12-hour format with seconds
    const formatTime = (date) => {
        const hours = date.getHours();
        const minutes = date.getMinutes();
        const seconds = date.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const hour12 = hours % 12 || 12;
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(hour12)}:${pad(minutes)}:${pad(seconds)} ${ampm}`;
    };

    // Format date and day
    const formatDate = (date) => {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const day = days[date.getDay()];
        const month = months[date.getMonth()];
        const dayNum = date.getDate();
        const year = date.getFullYear();
        return `${day}, ${month} ${dayNum}, ${year}`;
    };

    // Get role display name
    const getRoleName = (role) => {
        switch (role) {
            case 'LGU_ADMIN': return 'LGU Admin';
            case 'LGU_TRAINER': return 'Trainer';
            case 'PARTICIPANT': return 'Participant';
            default: return role;
        }
    };

    // Get user initials for default avatar
    const getUserInitials = () => {
        if (!user?.name) return 'U';
        const names = user.name.trim().split(' ');
        if (names.length >= 2) {
            return (names[0][0] + names[names.length - 1][0]).toUpperCase();
        }
        return user.name.substring(0, 2).toUpperCase();
    };

    // Get default profile picture path or generate initials avatar
    const getProfilePicture = () => {
        if (user?.profile_picture) {
            return user.profile_picture.startsWith('http')
                ? user.profile_picture
                : `/storage/${user.profile_picture}`;
        }
        // Return null to indicate we should use initials
        return null;
    };

    // Generate initials avatar SVG
    const getInitialsAvatar = () => {
        const initials = getUserInitials();
        const colors = [
            { bg: '#3b82f6', text: '#ffffff' }, // blue
            { bg: '#10b981', text: '#ffffff' }, // emerald
            { bg: '#8b5cf6', text: '#ffffff' }, // purple
            { bg: '#f59e0b', text: '#ffffff' }, // amber
            { bg: '#ef4444', text: '#ffffff' }, // red
            { bg: '#06b6d4', text: '#ffffff' }, // cyan
        ];
        // Pick color based on user name hash for consistency
        const colorIndex = user?.name ? user.name.charCodeAt(0) % colors.length : 0;
        const color = colors[colorIndex];

        return `data:image/svg+xml,${encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                <circle cx="16" cy="16" r="16" fill="${color.bg}"/>
                <text x="16" y="16" font-family="Arial, sans-serif" font-size="12" font-weight="600" fill="${color.text}" text-anchor="middle" dominant-baseline="central">${initials}</text>
            </svg>
        `)}`;
    };

    // Handle logout with SweetAlert
    const handleLogout = (e) => {
        e.preventDefault();
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
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.head.querySelector('meta[name="csrf-token"]')?.content || '';
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    };

    return (
        <div className="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30">
            <div className="px-6 py-2">
                <div className="flex items-center justify-between min-h-[3rem]">
                    {/* Left Section: Sidebar Toggle, Module Name */}
                    <div className="flex items-center gap-2 flex-1 min-w-0">
                        {/* Sidebar Toggle Button - Hidden on mobile */}
                        <button
                            onClick={onSidebarToggle}
                            className="hidden md:flex p-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-md transition-colors items-center justify-center"
                            title={isSidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'}
                        >
                            <PanelLeft className="w-4 h-4" />
                        </button>

                        {/* Module Name */}
                        <h1 className="text-lg font-bold text-slate-900 truncate">
                            {moduleName || 'Dashboard'}
                        </h1>
                    </div>

                    {/* Right Section: Time, Notifications, Profile - Centered */}
                    <div className="flex items-center justify-center gap-2">
                        {/* Time/Date/Day - Hidden on mobile */}
                        <div className="hidden lg:flex flex-col items-center text-xs">
                            <div className="flex items-center gap-1 text-slate-700 font-medium">
                                <Clock className="w-3.5 h-3.5 text-slate-500" />
                                <span>{formatTime(currentTime)}</span>
                            </div>
                            <div className="text-[0.65rem] text-slate-500">
                                {formatDate(currentTime)}
                            </div>
                        </div>

                        {/* Notification Icon - Always visible */}
                        <button
                            className="relative p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-md transition-colors flex items-center justify-center"
                            title="Notifications"
                        >
                            <Bell className="w-4 h-4" />
                            {/* Notification badge - can be added later */}
                            {/* <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span> */}
                        </button>

                        {/* Profile Dropdown - Hidden on mobile */}
                        <div className="relative hidden md:block">
                            <button
                                onClick={() => setShowProfileMenu(!showProfileMenu)}
                                className="flex items-center gap-1.5 px-2 py-1 hover:bg-slate-100 rounded-md transition-colors"
                            >
                                {getProfilePicture() ? (
                                    <img
                                        src={getProfilePicture()}
                                        alt={user?.name || 'User'}
                                        className="w-7 h-7 rounded-full object-cover border-2 border-slate-200"
                                        onError={(e) => {
                                            e.target.src = getInitialsAvatar();
                                        }}
                                    />
                                ) : (
                                    <img
                                        src={getInitialsAvatar()}
                                        alt={user?.name || 'User'}
                                        className="w-7 h-7 rounded-full border-2 border-slate-200"
                                    />
                                )}
                                <div className="hidden md:flex flex-col items-start text-xs">
                                    <span className="font-medium text-slate-900 leading-tight">{user?.name || 'User'}</span>
                                    <span className="text-[0.65rem] text-slate-500 leading-tight">{getRoleName(user?.role)}</span>
                                </div>
                                <ChevronDown className={`w-3.5 h-3.5 text-slate-500 hidden md:block transition-transform ${showProfileMenu ? 'rotate-180' : ''}`} />
                            </button>

                            {showProfileMenu && (
                                <>
                                    <div
                                        className="fixed inset-0 z-40"
                                        onClick={() => setShowProfileMenu(false)}
                                    />
                                    <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                                        <a
                                            href="/profile"
                                            onClick={() => setShowProfileMenu(false)}
                                            className="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors"
                                        >
                                            <User className="w-4 h-4" />
                                            Manage Profile
                                        </a>
                                        <a
                                            href="/settings"
                                            onClick={() => setShowProfileMenu(false)}
                                            className="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors"
                                        >
                                            <Settings className="w-4 h-4" />
                                            Settings
                                        </a>
                                        <div className="h-px bg-slate-200 my-1" />
                                        <button
                                            onClick={handleLogout}
                                            className="w-full flex items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors text-left"
                                        >
                                            <LogOut className="w-4 h-4" />
                                            Logout
                                        </button>
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
