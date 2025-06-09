require('dotenv').config();
const express = require('express');
const cors = require('cors');
const app = express();
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);

app.use(cors());
app.use(express.static('public'));
app.use(express.static(__dirname));
app.use(express.json());

// PON AQUÍ TUS price_id REALES DE STRIPE
const SHIPPING_PRICE_IDS = {
  standard: 'price_1RURyWGh171OKFHV61Dv2THH', // Europa 
  express: 'price_1RVDiQGh171OKFHVpO4kjb26',      // Fuera de Europa 
};

app.post('/create-checkout-session', async (req, res) => {
  try {
    const { items, shippingOption } = req.body;

    // Los productos deben venir con stripe_price_id
    const lineItems = items.map(item => ({
      price: item.stripe_price_id, // Usar el price_id de Stripe del producto
      quantity: parseInt(item.quantity, 10),
    }));

    // Añadir el envío como otro producto usando su price_id
    lineItems.push({
      price: SHIPPING_PRICE_IDS[shippingOption === 'express' ? 'express' : 'standard'],
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

app.get('/stripe-purchases', async (req, res) => {
  try {
    const sessions = await stripe.checkout.sessions.list({
      limit: 20
      // Quita payment_status
    });
    res.json({ purchases: sessions.data });
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: error.message });
  }
});

const PORT = 4242;
app.listen(PORT, () => console.log(`Stripe server running on http://localhost:${PORT}`));
