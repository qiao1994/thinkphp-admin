$(document).ready(function() {
	$('a').each(function(){
		$(this).attr('external', '');
	});
    //返回按钮
    $('.return').click(function() {
        // console.log($('#post').val());
        history.back(-1);
    });
    // 选择购买数量
    $('.buy-num').change(function() {
    	var total = $('.buy-price').html()*$(this).val();
    	$('.buy-total').html(total);
	});
	$('.button-left').click(function(){
		$(this).parents('form').submit();
	});
});
