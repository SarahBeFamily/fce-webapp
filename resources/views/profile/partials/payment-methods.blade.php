<h2>{{ __('Metodi di Pagamento') }}</h2>

@php(Auth::user()->createOrGetStripeCustomer())
@php($user = Auth::user())

@if (count($user->paymentMethods()) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Metodo') }}</th>
                <th>{{ __('Ultimo 4 cifre') }}</th>
                <th>{{ __('Scadenza') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user->paymentMethods() as $paymentMethod)
                <tr>
                    <td>{{ $paymentMethod->card->brand }}</td>
                    <td>{{ $paymentMethod->card->last4 }}</td>
                    <td>{{ $paymentMethod->card->exp_month }} / {{ $paymentMethod->card->exp_year }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>{{ __('Nessun metodo di pagamento salvato') }}</p>

    <h3>{{ __('Aggiungi nuovo metodo di pagamento') }}</h3>

    <form method="POST" class="card-form mt-3 mb-3">
        @csrf
        <input type="hidden" name="payment_method" class="payment-method">
        <input type="hidden" name="stripe_intent" class="stripe-intent">
        <label for="card-holder-name">
            {{ __('Titolare Carta') }}
            <input class="StripeElement mb-3" id="card-holder-name" name="card_holder_name" placeholder="{{ __('Titolare Carta') }}" required>
        </label>
        <div class="col-lg-4 col-md-6">
            <!-- Stripe Elements Placeholder -->
            <label>{{ __('Carta di Credito / Debito') }}</label>
            <div id="card-element"></div>
        </div>
        <div id="card-errors" role="alert"></div>
        <button type="submit" class="btn btn-primary submit" hidden></button>
    </form>

    <button id="card-button" class="btn btn-primary add-card" data-secret="{{ $intent->client_secret }}">
        {{ __('Aggiorna metodi di pagamento') }}
    </button>
    
@endif

@section('styles')
<style>
    .StripeElement {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: white;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
// add script after stripe is loaded
document.addEventListener("DOMContentLoaded", function() {

    let stripe = Stripe("{{ env('STRIPE_KEY') }}")
    let elements = stripe.elements()
    let style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    }
    const card = elements.create('card', {style: style})
    card.mount('#card-element')
    let paymentMethod = null
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;

    card.on('change', function (event) {
        let displayError = document.getElementById('card-errors')
        if (event.error) {
            displayError.textContent = event.error.message
        } else {
            displayError.textContent = ''
        }
    })
    
    document.querySelector('.card-form').addEventListener('submit', function (e) {
        e.preventDefault()
        const thisButton = document.querySelector('.add-card')
        const clientSecret = thisButton.dataset.secret;
        const cardHolderName = document.getElementById('card-holder-name').value;
        stripe.confirmCardSetup(
            clientSecret,
            {
                payment_method: {
                    card: card,
                    billing_details: {name: cardHolderName.value}
                }
            }
        ).then(function (result) {
            if (result.error) {
                console.log(result.error.message)
            } else {
                let stripeIntent = result.setupIntent
                paymentMethod = result.setupIntent.payment_method
                document.querySelector('.payment-method').value = paymentMethod
                console.log('intent ', stripeIntent)
                // document.querySelector('.submit').click()
                // document.querySelector('.card-form').submit()
            }
        })
    })

    document.querySelector('.add-card').addEventListener('click', function (e) {
        e.preventDefault()
        console.log('clicked')

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                console.log(result.error.message)
            } else {
                stripeTokenHandler(result.token);
            }
        });
    });

    function stripeTokenHandler(token) {
        let form = document.querySelector('.card-form')
        let hiddenInput = document.createElement('input')
        hiddenInput.setAttribute('type', 'hidden')
        hiddenInput.setAttribute('name', 'stripeToken')
        hiddenInput.setAttribute('value', token.id)
        form.appendChild(hiddenInput)
        // form.submit()
        const cardHolderName = document.getElementById('card-holder-name').value;

        if (!cardHolderName) {
            alert('{{ __("Inserisci il nome del titolare della carta") }}')
            return
        }

        stripe.confirmCardSetup(
            clientSecret,
            {
                payment_method: {
                    card: card,
                    billing_details: {name: cardHolderName.value}
                }
            }
        ).then(function (result) {
            if (result.error) {
                console.log(result.error.message)
            } else {
                let stripeIntent = result.setupIntent
                paymentMethod = result.setupIntent.payment_method
                document.querySelector('.payment-method').value = paymentMethod
                console.log('intent ', stripeIntent)
                // document.querySelector('.submit').click()
                document.querySelector('.card-form').submit()
            }
        })
    }
});
</script>
@endsection