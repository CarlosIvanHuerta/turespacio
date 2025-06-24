$(function () {
    'use strict';

    let header = $('.start-style');
    $(window).scroll(function(){
        let scroll = $(window).scrollTop();

        if(scroll >= 10){
            header.removeClass('start-style').addClass('scroll-on');
        }else{
            header.removeClass('scroll-on').addClass('start-style');
        }
    });

    /* Menú on Hover */
    $('body').on('mouseenter mouseleave', '.nav-item', function(e){
        if($(window).width() > 750){
            var _d = $(e.target).closest('.nav-item');
            _d.addClass('show');
            setTimeout(function(){
                _d[_d.is(':hover') ? 'addClass' : 'removeClass']('show');
            }, 1);
        }
    });
    /* Fin Menú on Hover */
});