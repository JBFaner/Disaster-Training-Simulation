/* Participant Main JavaScript */

document.getElementById('menuToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('inactive');
    mainContent.classList.toggle('sidebar-closed');
});

// Close sidebar when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const menuToggle = document.getElementById('menuToggle');
    
    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
        sidebar.classList.add('inactive');
        mainContent.classList.add('sidebar-closed');
    }
});

// Logout confirmation
function confirmLogout() {
    Swal.fire({
        title: 'Confirm Logout',
        text: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c33',
        cancelButtonColor: '#999',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = window.location.pathname + '?logout=1';
        }
    });
}

