require('./bootstrap');

$('.sidebar-toggle').on('click',function(){

    var cls =  $('body').hasClass('sidebar-collapse');
    if(cls == true){
        $('body').removeClass('sidebar-collapse');
    } else {
        $('body').addClass('sidebar-collapse');
    }

});
/*expand password show*/
$(".toggle-password").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});


