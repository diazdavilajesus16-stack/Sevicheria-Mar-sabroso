// Seleccionamos todos los links del navbar
const links = document.querySelectorAll('.navbar nav a');
const sections = document.querySelectorAll('section');

links.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault(); // Evita que el navegador haga scroll automático
        const targetId = link.getAttribute('href').substring(1); // 'home', 'productos', etc.

        sections.forEach(section => {
            if (section.id === targetId) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    });
});
