// JavaScript pour gérer le menu burger
const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active'); // Ajouter/retirer la classe 'active' pour ouvrir/fermer le menu
});
document.getElementById("menu-toggle").addEventListener("click", function () {
    document.getElementById("nav-links").classList.toggle("show");
});

document.getElementById('contact-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const form = this; // Référence au formulaire
    const formData = new FormData(form);

    fetch('../scripts/submit_form.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                form.reset(); // Réinitialiser le formulaire après le succès
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
            console.error('Erreur:', error);
        });
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.display = 'flex';
    notification.innerHTML = `
        <div class="icon__wrapper">
            <span>${type === 'success' ? '&#10004;' : '&#10060;'}</span>
        </div>
        <p>${message}</p>
        <span class="close" onclick="this.parentElement.style.display='none';">✖</span>
    `;
    document.body.appendChild(notification);

    // Supprime la notification après 3 secondes
    setTimeout(() => {
        notification.style.display = 'none';
        notification.remove();
    }, 3000);
}
form.classList.add('form-cleared');
setTimeout(() => form.classList.remove('form-cleared'), 1000); // Enlever l'effet après 1 seconde