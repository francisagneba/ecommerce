document.addEventListener("DOMContentLoaded", function () {

    const stripe = Stripe(stripePublicKey);

    const elements = stripe.elements();
    const card = elements.create("card");

    // Vérifiez que l'élément #card-element existe
    const cardElement = document.getElementById("card-element");
    if (cardElement) {
        card.mount("#card-element");

        card.on("change", function (event) {
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
        });
    } else {
        console.error("#card-element introuvable dans le DOM");
    }

    const form = document.getElementById("payment-form");
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card
            }
        }).then(function (result) {
            if (result.error) {
                console.log(result.error.message);
            } else { // The payment success
                window.location.href = redirectAfterSuccessUrl;
            }
        });
    });

});