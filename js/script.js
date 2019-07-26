$(function(){
	var a=$(this);
	var card=a.find('.card .border');
	card.css('cursor','pointer');	
	
	card.mouseover(function(){
		$('.card-category',this).css('fontSize','16px');
		$('.card-title',this).css('fontSize','17px');
		$('.card-btn>a',this).css({
			backgroundColor:"#006978",fontSize:"17px", padding:"6px 20px 7px"
			});
	});
	$('.card').mouseout(function(){
		$('.card-category',this).css('fontSize','15px');
		$('.card-title',this).css('fontSize','16px');
		$('.card-btn>a',this).css({
			backgroundColor:"#008a82",fontSize:"16px", padding:"6px 20px"
			});
	})
});


