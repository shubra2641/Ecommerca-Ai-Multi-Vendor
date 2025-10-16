<script src="https://js.stripe.com/v3/"></script>
<div class="stripe-container">
    <form id="payment-form">
        <div id="payment-element">
        </div>
    <div id="btn-stripe-container" class="btn-center">
      <button id="submit-button-stripe"><div class="spinner" id="spinner"></div><span id="button-text">شحن {{$data['amount']}}$</span></button>
    </div>
        <div id="payment-message" class="hidden"></div>
    </form>
</div>
<script id="stripe-appended-script">
(function(){
        var element = document.getElementById("button-text");
        element.classList.add("hidden");

        

        const stripe = Stripe("{{$data['public_key']}}");
        let elements;
        const appearance = {
            clientSecret: '{{$data['client_secret']}}',
            locale: 'ar',
            theme: 'night',
            variables: { colorPrimaryText: '#2196f3' }

        };
        const options = {
            layout: {
                type: 'tabs',
                defaultCollapsed: false,
            },
            fields: {
                billingDetails: {
                    address: {
                        country: 'never'
                    }
                }
            }
        };
        initialize();
        checkStatus();
        document.querySelector("#payment-form").addEventListener("submit", handleSubmit);
        async function initialize() {
            elements = stripe.elements(appearance);
            const paymentElement = elements.create("payment", options);
            paymentElement.mount("#payment-element");
        }
        async function handleSubmit(e) {
            e.preventDefault();
            setLoading(true);

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: "{{$data['return_url']}}",
                    payment_method_data: {
                        billing_details: {
                            address: {
                                country: null
                            }
                        }
                    }
                },
            });
            if (error.type === "card_error" || error.type === "validation_error") {
                showMessage(error.message);
            } else {
                showMessage("خطأ غير متوقع.");
            }
            setLoading(false);
        }

        async function checkStatus() {
            const clientSecret = new URLSearchParams(window.location.search).get(
                "payment_intent_client_secret"
            );
            if (!clientSecret) {
                return;
            }
            const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);
            switch (paymentIntent.status) {
                case "succeeded":
                    showMessage("تم الدفع بنجاح!");
                    break;
                case "processing":
                    showMessage("جار تنفيذ العملية.");
                    break;
                case "requires_payment_method":
                    showMessage("لم تتم عملية الدفع بنجاح، برجاء المحاولة مرة أخرى.");
                    break;
                default:
                    showMessage("حدث خطأ ما.");
                    break;
            }
        }

        function showMessage(messageText) {
            const messageContainer = document.querySelector("#payment-message");
            messageContainer.classList.remove("hidden");
            messageContainer.textContent = messageText;

            setTimeout(function() {
                messageContainer.classList.add("hidden");
                messageText.textContent = "";
            }, 4000);
        }

        function setLoading(isLoading) {
            if (isLoading) {
                document.querySelector("#submit-button-stripe").disabled = true;
                document.querySelector("#spinner").classList.remove("hidden");
                document.querySelector("#button-text").classList.add("hidden");
            } else {
                document.querySelector("#submit-button-stripe").disabled = false;
                document.querySelector("#spinner").classList.add("hidden");
                document.querySelector("#button-text").classList.remove("hidden");
            }
        }

        setTimeout(function(){
            document.querySelector("#submit-button-stripe").disabled = false;
            var element = document.getElementById("spinner");
            element.classList.add("hidden");
            var element = document.getElementById("button-text");
            element.classList.remove("hidden");
        },1500);

})();
</script>
