import './bootstrap';
// resources/js/app.js
document.addEventListener('DOMContentLoaded', () => {
  // Si necesitas que al clicar en un item sin recargar se muestre activo:
  document.querySelectorAll('.menu a').forEach(a => {
    a.addEventListener('click', (e) => {
      // Si el enlace realiza navegación servidor-side, esto solo afectará
      // en SPAs; pero sirve para feedback inmediato:
      document.querySelectorAll('.menu li').forEach(li => li.classList.remove('active'));
      const li = a.closest('li');
      if (li) li.classList.add('active');
    });
  });
});
