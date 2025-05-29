document.addEventListener('DOMContentLoaded', () => {
  fetch('/api/load_favorites.php')
    .then(response => response.json())
    .then(favoritos => {
      if (!Array.isArray(favoritos)) return;

      document.querySelectorAll('.outfit').forEach(div => {
        const id = Number(div.getAttribute('data-product-id'));
        const btn = div.querySelector('.favorite-btn');
        if (favoritos.includes(id)) {
          btn.textContent = '❤️';
        }
      });
    })
    .catch(console.error);
});
