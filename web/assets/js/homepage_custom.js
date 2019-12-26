/* viewport width */
$(function () {
    /* components */
    if ($('.slick-slider').length) {
        $('.slick-slider').each(function () {
            $(this).slick({
                appendArrows: $(this).closest('section').find('.slider-nav'),
                dots: false,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 1,
                variableWidth: true,
                responsive: [
                    {
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 769,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 481,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            centerMode: true
                        }
                    }
                ]
            });
        });
    }
    /* components */


});


$('.disabled-button.tariff__button').on('click', function(e) {
    e.preventDefault();
    return false;
});

$('.more-button').on('click', function(e) {
    let text = $(this).parent().prev().text().trim();
    $(this).parent().prev().text(text.slice(0, -3));
    $(this).parent().prev().append($(this).next().removeClass('hidden'));
    $(this).remove();
});


$('.close').click(function(e) {
    e.preventDefault();
    $(this).parent('.alert').slideUp();
});

$('.preview').fancybox({
    'transitionIn': 'elastic',
    'transitionOut': 'elastic',
    'speedIn': 400,
    'speedOut': 200,
    'overlayShow': false
});