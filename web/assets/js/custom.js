$(window).load(function () {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
        $('body').addClass('ios');
    } else {
        $('body').addClass('web');
    }

    $('body').removeClass('loaded');

    let invalidFeedback = document.body.querySelector('.invalid-feedback');
    if (invalidFeedback) {
        invalidFeedback.scrollIntoView();
    }

    let successMessages = document.body.querySelector('.alert.alert-success');
    if (successMessages) {
        successMessages.scrollIntoView();
    }
});

$('.custom-placeholder-js, .custom-placeholder').on('click', function (e) {
    let $prev = $(this).prev('input');
    if ($prev.next) {
        $prev.focus();
    }
});

$('.auth-list').on('click', function (e) {
    if (!$(this).hasClass('active')) {
        e.preventDefault();
    }
    e.stopPropagation();
    $(this).toggleClass('active').next().toggleClass('active');
});

$('.burger-js').on('click', function (e) {
    e.stopPropagation();
    $(this).toggleClass('active').next().toggleClass('is-open');
});

$('.header-menu__content').on('click', function (e) {
    e.stopPropagation();
});

$(document).on('click', function (e) {
    $('.burger-js').removeClass('active').next().removeClass('is-open');
    $('.auth-list').removeClass('active').next().removeClass('active');
})

let slidesToScroll = 1;

$(function () {

    /*$('.progress-slider').slick({
        infinity: false,
        slidesToShow: 7,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 5
                }
            },
            {
                breakpoint: 769,
                settings: {
                    arrows: false,
                    slidesToShow: 5
                }
            },
            {
                breakpoint: 601,
                settings: {
                    arrows: false,
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 481,
                settings: {
                    arrows: false,
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 481,
                settings: {
                    arrows: false,
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 376,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            }
        ]
    });*/

    $(window).on('load scroll', function () {

        if ($(document).scrollTop() >= 1) {
            $('.header__top').addClass('sticky');
        } else {
            $('.header__top').removeClass('sticky');
        }
    });

    if ($('.slick-slider').length) {
        $('.slick-slider').each(function () {
            $(this).slick({
                appendArrows: $(this).closest('section').find('.slider-nav'),
                dots: false,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: slidesToScroll,
                variableWidth: true,
                draggable: true,
                responsive: [
                    {
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: slidesToScroll
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
                            // centerMode: true
                        }
                    }
                ]
            });
        });

        if ($('.faq__content').length) {
            $('.faq__content').slick('slickSetOption', 'responsive', [{
                breakpoint: 426,
                settings: {
                    dots: true,
                    centerMode: true,
                }
            }], true);
        }
    }

    $(window).on('load resize', function () {
        if ($('.sticky').length) {
            // $('.header.sticky + .content').css('padding-top', $('.header.sticky').height());
            $('.sticky').next('.content').css('padding-top', $('.sticky').height());
        }

        if ($(window).innerWidth() > 991) {
            $('.header:not(".main")').addClass('border-radius-b-r');
        } else {
            $('.header:not(".main")').removeClass('border-radius-b-r');
        }

    });




    if ($('.how-works__slider').length) {
        $('.how-works__slider').slick('slickSetOption', 'responsive', [
            {
                breakpoint: 481,
                settings: 'unslick'
            }
        ], true);

        $(window).on('load resize', function () {
            if ($(window).innerWidth() > 480) {
                $('.how-works__slider:not(".slick-slider")').slick({
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
                            settings: 'unslick'
                        }
                    ]
                });
            }
        });
    }


});

$('.close').click(function (e) {
    e.preventDefault();
    $(this).parent('.alert').slideUp();
});

$('.payment__input').on('input', function (e) {
    let val = $(this).val();

    if (!val.length) {
        $(this).removeClass('filled');
    } else {
        $(this).addClass('filled');
    }
});

$(document).on('load scroll', function (e) {
    if ($('.results__indicators-val:not(.active)').length) {
        if ($(document).scrollTop() >= 300) {
            if ($.fn.spincrement) {
                $('.results__indicators-val:not(.active)').spincrement({
                    from: 0,
                    to: false,
                    decimalPlaces: 0,
                    decimalPoint: '.',
                    thousandSeparator: ',',
                    duration: 4000, // ms; TOTAL length animation
                    leeway: 100, // percent of duraion
                    easing: 'spincrementEasing',
                    fade: true,
                    complete: function (e) {
                        e.addClass('active');
                    }
                });
            }
        }
    }
});
