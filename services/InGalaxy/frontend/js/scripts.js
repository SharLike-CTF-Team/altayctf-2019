var timeToScroll=750;
var top_show = 150; // В каком положении полосы прокрутки начинать показ кнопки "Наверх"
$(document).ready(function() {
    $(window).scroll(function () { // При прокрутке попадаем в эту функцию
        /* В зависимости от положения полосы прокрукти и значения top_show, скрываем или открываем кнопку "Наверх" */
        if ($(this).scrollTop() > top_show) $('#top').fadeIn();
        else $('#top').fadeOut();
    });
    $('#top').click(function () { // При клике по кнопке "Наверх" попадаем в эту функцию
        /* Плавная прокрутка наверх */
        $('body, html').animate({
            scrollTop: 0
        }, timeToScroll);
    });

    //смена чата
    $(".message").click(function(){
        $(".message").removeClass("button-active");
        $(this).addClass("button-active");

        var dialog_id=$(this).attr("dialog-open");
        $(".dialog").css("display","none");
        $("#dialog-"+dialog_id).css("display","block");
    });


    //друзья\заявки
    $("#btn_requests").click(function(){
        $("#btn_friends").removeClass("button-active");
        $(this).addClass("button-active");

        $("#friends").css("display","none");
        $("#requests").css("display","block");
    });
    $("#btn_friends").click(function(){
        $("#btn_requests").removeClass("button-active");
        $(this).addClass("button-active");

        $("#friends").css("display","block");
        $("#requests").css("display","none");
    });


});