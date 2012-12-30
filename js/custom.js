function update_attr(attribute, parent, suffix){
    $('[' + attribute + '*="_"]', parent).each(function(){
        var n = $(this).attr(attribute).split('_')[0] + '_' + suffix;
        $(this).attr(attribute, n);
    });
}

$('#add_trip #add_another').on('click', function(e){
    e.preventDefault();
    if($('.row-fluid.trip').length == 1){
        $('.row-fluid.trip .span6').removeClass('span6').addClass('span5');
        $('.row-fluid.trip').append('<p class="span1"><button class="btn remove_trip" title="Remove this trip"><i class="icon-remove"></i></button></p>');
    }
    var $p = $('.row-fluid.trip').last();
    var $newp = $p.clone().hide();
    var t = Math.round(new Date().getTime() / 1000);
    update_attr('id', $newp, t);
    update_attr('for', $newp, t);
    $('input', $newp).val('');
    $newp.insertAfter($p).slideDown();
});

$('#add_trip').on('click', '.remove_trip', function(e){
    e.preventDefault();
    $(this).closest('.trip').slideUp(function(){
        $(this).remove();
        if($('.row-fluid.trip').length == 1){
            $('.row-fluid.trip .span5').removeClass('span5').addClass('span6');
            $('.row-fluid.trip .span1').remove();
        }
    });
});