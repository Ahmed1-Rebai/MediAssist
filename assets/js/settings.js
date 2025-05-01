document.addEventListener('DOMContentLoaded', function() {
    // Handle theme form submission
    const themeForm = document.querySelector('form[method="POST"]');
    if (themeForm) {
        themeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Apply theme immediately without reload
                    const selectedTheme = formData.get('theme');
                    document.documentElement.setAttribute('data-theme', selectedTheme);
                    
                    // Show success message
                    if (data.message) {
                        alert(data.message);
                    }
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving settings: ' + error.message);
            });
        });
    }
    
    // Watch for system theme changes
    const themeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    themeMediaQuery.addEventListener('change', (e) => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        if (currentTheme === 'system') {
            // Apply system preference
            const newTheme = e.matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
        }
    });
});