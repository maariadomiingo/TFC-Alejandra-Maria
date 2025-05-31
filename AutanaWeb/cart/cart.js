let cart = [];
let shippingCost = 5.00; // Costo por defecto

document.addEventListener('DOMContentLoaded', () => {
    loadCart();
    document.getElementById('shipping-select').addEventListener('change', updateShipping);
    document.getElementById('checkout-button').addEventListener('click', proceedToCheckout);
});

function loadCart() {
    fetch('get_cart.php')
        .then(res => res.json())
        .then(products => {
            cart = products.map(p => ({
                id: p.id,
                name: p.nombre, // <-- nombre de la BBDD
                price: parseFloat(p.precio_stripe), // <-- precio_stripe de Stripe
                currency: p.moneda_stripe, // <-- moneda_stripe
                quantity: 1,
                stripe_price_id: p.stripe_price_id
            }));
            updateCartDisplay();
        });
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
                ${item.name} - $${item.price.toFixed(2)} ${item.currency} x ${item.quantity} = $${itemTotal.toFixed(2)} ${item.currency}
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

async function proceedToCheckout() {
    const shippingOption = document.getElementById('shipping-select').value;
    const response = await fetch('http://localhost:4242/create-checkout-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            items: cart.map(item => ({
                name: item.name,
                price: parseFloat(item.price),
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