/**
 * TO use need set 2 variables in code upper
 * paymentButtonSelector, defaultPaymentUrl
 */

let productId = null;
let $paymentSection = $('.section-pay-modal');
let $paymentForm = $paymentSection.find('form.payment');
let $paymentButton = $('.payment__submit.button-full');

$(paymentButtonSelector).on('click', function(e) {
    e.preventDefault();
    // if (userId != 0) return;
    productId = + this.dataset.id;
    $paymentSection.removeClass('hidden');
    $paymentForm.attr('action',
        defaultPaymentUrl.slice(0, -1) + productId
    );
    $('#payment-form-title').text(this.dataset.title);
});


$paymentForm.on('submit', function(e) {
    e.preventDefault();
    
    let data = $(this).serializeArray();
    let checkAgr = $('#check_agreement').prop('checked');
    let paymentEmail = $('#payment_email').val();
    let errors = false;

    $('#email-span, #accept-span').addClass('hidden');
    $('#email-message, #accept-message').text('');

    // Modal from existing users doesn't need email
    // if (!$(this).is('.payment-from-existing-user') &&
    if (userEmail) {
        data.push({name: "order[email]", value: userEmail});
    } else {
        if (!paymentEmail.length) {
            $('#email-span').removeClass('hidden');
            $('#email-message').text("E-mail обязателеное поле");
            errors = true;
        }
    }

    if (!checkAgr) {
        $('#accept-span').removeClass('hidden');
        $('#accept-message').text("Вы должны согласиться с условиями");
        errors = true;
    }

    if (!errors) {
        $paymentButton.addClass('loading').prop('disabled', true);
        $.ajax({
            method: 'post',
            data: data,
            url: $(this).attr('action'),
            success(resp) {
                $paymentButton.removeClass('loading').prop('disabled', false);

                if (resp.success) {
                    let aData = analiticsData[productId];
                    ym(yId, 'reachGoal', aData['y_label']);
                    if (window.gtag) {
                        ga('send', 'event', aData['g_label'], 'pay', aData['g_price']);
                    }
                    if (resp.redirectUrl) {
                        window.location.href = resp.redirectUrl;
                    }
                } else if (resp.errors) {
                    for (key in resp.errors) {
                        $('#' + key +'-span').removeClass('hidden');
                        $('#' + key +'-message').text(resp.errors[key]);
                    }
                    if ('payment' in resp.errors) {
                        $('#error-payment-span').removeClass('hidden');
                        $('#error-payment-message').text(resp.errors['payment']);
                    }
                }
            }
        });
    }
});

$('.close-button').click(function(e) {
    $paymentSection.addClass('hidden');
    $paymentForm.find('input[type="email"]').val('').removeClass('filled');
    $paymentForm.find('input[type="checkbox"]').prop('checked', false);
    $paymentForm.attr('action', '');
    productId = null;
    $('#payment-form-title').text('');
});
