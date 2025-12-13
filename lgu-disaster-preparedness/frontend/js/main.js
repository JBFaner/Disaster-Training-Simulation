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