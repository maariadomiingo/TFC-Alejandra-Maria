require('dotenv').config();
const express = require('express');
const cors = require('cors');
const app = express();
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);

app.use(cors());
app.use(express.static('public'));
app.use(express.static(__dirname)); // <-- Añade esto para servir toda la carpeta stripeCart
app.use(express.json());

app.post('/create-checkout-session', async (req, res) => {
  try {

    const { items, shippingOption } = req.body;

    const lineItems = items.map(item => ({
      price_data: {
        currency: 'usd',
        product_data: {
          name: item.name,
        },
        unit_amount: Math.round(parseFloat(item.price) * 100), // Ensure price is a number
      },
      quantity: parseInt(item.quantity, 10), // Ensure quantity is an integer
    }));

    // Añadir el costo de envío
    lineItems.push({
      price_data: {
        currency: 'usd',
        product_data: {
          name: shippingOption === 'express' ? 'Envío Express' : 'Envío Estándar',
        },
        unit_amount: shippingOption === 'express' ? 1000 : 500,
      },
      quantity: 1,
    });

    const session = await stripe.checkout.sessions.create({
      ui_mode: 'embedded',
      mode: 'payment',
      line_items: lineItems,
      return_url: `${process.env.YOUR_DOMAIN}/checkout/return.html?session_id={CHECKOUT_SESSION_ID}`,
    });

    res.json({ clientSecret: session.client_secret });
  } catch (error) {
    console.error('Detailed error:', error);
    res.status(500).json({ error: error.message });
  }
});

const PORT = 4242;
app.listen(PORT, () => console.log(`Stripe server running on http://localhost:${PORT}`));
