document.addEventListener('DOMContentLoaded', function() {
    console.log("Profile JS loaded");
    
    // Improved toggle function with null checks
    function toggleForm(formId) {
        console.log("Attempting to toggle:", formId);
        const form = document.getElementById(formId);
        const displayId = formId.replace('-form', '-display');
        const display = document.getElementById(displayId);
        
        if (!form) {
            console.error("Form not found:", formId);
            return;
        }
        if (!display) {
            console.error("Display div not found:", displayId);
            return;
        }
        
        console.log("Current form display:", form.style.display);
        console.log("Current display div:", display.style.display);
        
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            display.style.display = 'none';
        } else {
            form.style.display = 'none';
            display.style.display = 'block';
        }
    }

    // Edit Info Button
    const editInfoBtn = document.getElementById('edit-info-btn');
    if (editInfoBtn) {
        editInfoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Edit info button clicked");
            toggleForm('info-form');
        });
    } else {
        console.error("Edit info button not found");
    }

    // Edit Password Button
    const editPasswordBtn = document.getElementById('edit-password-btn');
    if (editPasswordBtn) {
        editPasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Edit password button clicked");
            toggleForm('password-form');
            document.getElementById('password-form').reset();
        });
    } else {
        console.error("Edit password button not found");
    }

    // Cancel Buttons
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            if (form) {
                console.log("Canceling form:", form.id);
                toggleForm(form.id);
                form.reset();
            }
        });
    });
});