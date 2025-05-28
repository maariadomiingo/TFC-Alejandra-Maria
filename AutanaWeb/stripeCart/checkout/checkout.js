const stripe = Stripe("pk_test_51RSxu8Gh171OKFHVkMNBipKFyx92rGe7AUBVdYZBss9qNFE9TDONdTCjVJL1LWGm6mz3S7tosSuuB0MKAq8AWHHZ00qLVGD4l6");

initialize();

async function initialize() {
  const fetchClientSecret = async () => {
    const response = await fetch("http://localhost:4242/create-checkout-session", {
      method: "POST",
    });
    const { clientSecret } = await response.json();
    return clientSecret;
  };

  const checkout = await stripe.initEmbeddedCheckout({
    fetchClientSecret,
  });

  checkout.mount('#checkout');
}
