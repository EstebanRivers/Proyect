import './bootstrap';
import { Turbo } from '@hotwired/turbo';

// Configurar Turbo para navegación instantánea
Turbo.start();

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

// Eventos de Turbo para feedback visual
document.addEventListener('turbo:visit', () => {
  // Mostrar indicador de carga si quieres
  document.body.style.opacity = '0.8';
});

document.addEventListener('turbo:load', () => {
  // Restaurar opacidad cuando carga
  document.body.style.opacity = '1';
  
  // Actualizar estado activo del menú
  updateActiveMenuItem();
});

function updateActiveMenuItem() {
  const currentPath = window.location.pathname;
  document.querySelectorAll('.menu li').forEach(li => {
    li.classList.remove('active');
    const link = li.querySelector('a');
    if (link && link.getAttribute('href') === currentPath) {
      li.classList.add('active');
    }
  });
}
