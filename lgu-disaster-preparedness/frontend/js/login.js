/* Participant Login JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // Add any dynamic form validation or effects here if needed
    const forms = document.querySelectorAll('.login-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Error message will be shown by server
            }
        });
    });
});
