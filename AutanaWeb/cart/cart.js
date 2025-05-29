let cart = [];
let shippingCost = 5.00; // Costo por defecto

document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    updateCartDisplay();
    document.getElementById('shipping-select').addEventListener('change', updateShipping);
    document.getElementById('checkout-button').addEventListener('click', proceedToCheckout);
});

function loadCart() {
    // Simula cargar el carrito desde el almacenamiento local o una API
    cart = [
        { id: 1, name: "Producto 1", price: 20.00, quantity: 1 },
        { id: 2, name: "Producto 2", price: 30.00, quantity: 2 }
    ];
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cart-items');
    cartItemsDiv.innerHTML = '';
    let subtotal = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        cartItemsDiv.innerHTML += `
            <div>
                ${item.name} - $${item.price.toFixed(2)} x ${item.quantity} = $${itemTotal.toFixed(2)}
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

function updateShipping(event) {
    shippingCost = event.target.value === 'express' ? 10.00 : 5.00;
    updateCartDisplay();
}

function proceedToCheckout() {
    fetch('http://localhost:4242/create-checkout-session', { // <-- URL absoluta
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            items: cart,
            shippingOption: document.getElementById('shipping-select').value
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.clientSecret) {
            // Usar Embedded Checkout
            const stripe = Stripe("pk_test_51RSxu8Gh171OKFHVkMNBipKFyx92rGe7AUBVdYZBss9qNFE9TDONdTCjVJL1LWGm6mz3S7tosSuuB0MKAq8AWHHZ00qLVGD4l6");
            stripe.initEmbeddedCheckout({
                clientSecret: data.clientSecret
            }).then(checkout => {
                checkout.mount('#checkout');
            });
        } else {
            alert('No se recibió clientSecret del servidor');
        }
    })
    .catch(error => {
        alert('Error: ' + error);
        console.error('Error:', error);
    });
}