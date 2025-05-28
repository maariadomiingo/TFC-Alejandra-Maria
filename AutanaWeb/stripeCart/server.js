require('dotenv').config();
const express = require('express');
const cors = require('cors');
const app = express();
const stripe = require('stripe')(process.env.STRIPE_SECRET_KEY);

app.use(cors());
app.use(express.static('public')); // carpeta que contiene tu HTML
app.use(express.json());

app.post('/create-checkout-session', async (req, res) => {
  try {
    const session = await stripe.checkout.sessions.create({
      ui_mode: 'embedded',
      mode: 'payment',
      line_items: [
        {
          price: 'price_1RTgpqGh171OKFHVAbr3OP5x',
          quantity: 1,
        },
      ],
      return_url: `${process.env.YOUR_DOMAIN}/return.html?session_id={CHECKOUT_SESSION_ID}`,
    });

    res.send({ clientSecret: session.client_secret });
  } catch (error) {
    console.error('Error creating session:', error);
    res.status(500).json({ error: error.message });
  }
});

app.get('/session-status', async (req, res) => {
  try {
    const session = await stripe.checkout.sessions.retrieve(req.query.session_id);

    res.send({
      status: session.status,
      customer_email: session.customer_details.email,
    });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

const PORT = 4242;
app.listen(PORT, () => console.log(`Stripe server running on http://localhost:${PORT}`));
