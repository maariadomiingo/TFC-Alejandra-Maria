document.querySelectorAll('.favorite-btn').forEach(button => {
  button.addEventListener('click', e => {
    e.preventDefault();
    const outfit = button.closest('.outfit');
    const productId = outfit.getAttribute('data-product-id');

    fetch('../homeUser/toggle_favorite.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `producto_id=${productId}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'added') {
        button.textContent = '♥';
      } else if (data.status === 'removed') {
        button.textContent = '♡';
      } else if (data.error) {
        alert('Error: ' + data.error);
      }
    })
    .catch(err => {
      alert('Error en la conexión');
      console.error(err);
    });
  });
});
