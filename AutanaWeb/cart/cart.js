let cart = [];
let shippingCost = 70.00; // Por defecto Europa

document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    document.getElementById('shipping-select').addEventListener('change', updateShipping);
    document.getElementById('checkout-button').addEventListener('click', proceedToCheckout);
});

function loadCart() {
    fetch('get_cart.php')
        .then(res => res.json())
        .then(products => {
            console.log(products); // <-- Añade esto para depurar
            cart = products.map(p => ({
                id: p.id,
                name: p.nombre,
                price: parseFloat(p.precio_stripe),
                currency: p.moneda_stripe,
                quantity: 1,
                stripe_price_id: p.stripe_price_id,
                imagen_url: p.imagen_url
            }));
            updateCartDisplay();
        });
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cart-items');
    cartItemsDiv.innerHTML = '';
    let subtotal = 0;

    cart.forEach(item => {
        // Separar título y subtítulo por el primer guion
        const [titulo, subtitulo] = item.name.split('-').map(s => s.trim());
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        cartItemsDiv.innerHTML += `
            <div class="cart-item">
                <img src="${item.imagen_url}" alt="${item.name}" class="cart-item-img">
                <div class="cart-item-info">
                    <div>
                        <h3 style="margin-bottom:0.3rem;">${titulo || item.name}</h3>
                        ${subtitulo ? `<div class="cart-item-subtitle">${subtitulo}</div>` : ''}
                    </div>
                </div>
                <div class="cart-item-price">
                    $${item.price.toFixed(2)} ${item.currency} x ${item.quantity} = $${itemTotal.toFixed(2)} ${item.currency}
                </div>
                <span class="delete-icon" style="cursor:pointer;margin-left:10px;" onclick="removeFromCart(${item.id})" title="Eliminar">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </span>
            </div>
        `;
    });

    const total = subtotal + shippingCost;
    document.getElementById('cart-total').innerHTML = `
        <p>Subtotal: $${subtotal.toFixed(2)}</p>
        <p>Envío: $${shippingCost.toFixed(2)}</p>
        <p>Total: $${total.toFixed(2)}</p>
    `;
}

// Función para eliminar producto del carrito
function removeFromCart(productId) {
    // Elimina el producto del array cart en el frontend
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay(); // Actualiza la vista inmediatamente

    // Luego informa al backend para mantener la sesión sincronizada
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            // Si hubo un error, recarga el carrito desde el backend
            loadCart();
        }
    })
    .catch(() => {
        // Si hay error de red, recarga el carrito desde el backend
        loadCart();
    });
}

function updateShipping() {
    const shippingSelect = document.getElementById('shipping-select');
    shippingCost = shippingSelect.value === 'express' ? 200.00 : 70.00;
    updateCartDisplay();
}

async function proceedToCheckout() {
    const shippingOption = document.getElementById('shipping-select').value;
    const response = await fetch('http://localhost:4242/create-checkout-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            items: cart.map(item => ({
                stripe_price_id: item.stripe_price_id, // <-- price_id de Stripe
                quantity: parseInt(item.quantity, 10),
            })),
            shippingOption: shippingOption
        }),
    });

    const data = await response.json();

    if (data.clientSecret) {
        const stripe = Stripe("pk_test_51RSxu8Gh171OKFHVkMNBipKFyx92rGe7AUBVdYZBss9qNFE9TDONdTCjVJL1LWGm6mz3S7tosSuuB0MKAq8AWHHZ00qLVGD4l6");
        stripe.initEmbeddedCheckout({
            clientSecret: data.clientSecret
        }).then(checkout => {
            checkout.mount('#checkout');
        });
    } else {
        alert('No se recibió clientSecret del servidor');
    }
}