function formatPhoneInput(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.addEventListener('input', function(e) {
            // Supprime tout ce qui n'est pas chiffre
            let value = e.target.value.replace(/\D/g, '');
            
            // Limite à 8 chiffres maximum
            e.target.value = value.substr(0, 8);
        });
    }
}

// Initialisation pour les pages qui ont un champ telephone
document.addEventListener('DOMContentLoaded', function() {
    formatPhoneInput('telephone');
});