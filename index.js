function register(){
	var uname = $('#login-name').val();
	var uroom = $('#login-room').val();
	var patt = /^[1-9][0-9]+$/;
	var patt3 = /^[0]+$/;
	var patt2 = /^(dorm1|dorm2|dorm3)$/;
	if(patt3.test(uroom))
		return false;
	if(!patt.test(uroom) && !patt2.test(uroom))
		return false;
	if(uname == '' || uroom == '') return false;
	var data = {'name': uname, 'room': uroom, 'ip': window.ip_addr};
	$.ajax({
		url: 'handler.php',
		type: 'POST',
		data: data,
		success: function(){
			window.location.reload();
		}
	});
	return false;
}

function reregister(){

	var uname = window.user_id;
	var uroom = $('#relogin-room').val();
	var patt = /^[1-9][0-9]+$/;
	var patt2 = /^(dorm1|dorm2|dorm3)$/;
	var patt3 = /^[0]+$/;
	if(patt3.test(uroom))
		return false;
	if(!patt.test(uroom) && !patt2.test(uroom)){
		return false;
	}
	if(uname == '' || uroom == '') return false;
	var data = {'name': uname, 'room': uroom};
	$.ajax({
		url: 'handler.php',
		type: 'POST',
		data: data,
		success: function(){
			window.location.reload();
		}
	});
	return false;
}

function recommend(){
	var data = {'user_id': window.user_id};
	$.ajax({
		url: 'handler.php',
		type: 'GET',
		data: data,
		success: function(data){
			$('.recommendation').html(data);
		}
	});
}

function adjustMessagebox(){
	$('#messagebox').show();
	var top = (window.innerHeight/2) - (window.document.getElementById('messagebox').offsetHeight/2);
	var left = (window.innerWidth/2) - 175 - 8;
	$('.messagebox').css({'top': top, 'left':left});
	$('#messagebox-button-ok').focus();
}

$('#register').click(function(){
	register();
});

$('#reregister').click(function(){
	reregister();
});

$('.rating').live('change', function(){
	var movie = $(this).data('movie');
	var user_id = window.user_id;
	var rating = $(this).val();
	var data = {'user_id': user_id, 'movie_id': movie, 'rating': rating};
	$.ajax({
		url: 'handler.php',
		type: 'POST',
		data: data,
		success: function(){
			recommend();
		}
	});
});
	
$('.have_btn').live('click', function(){
	var movie = $(this).data('movie');
	window.movie = movie;
	
	var user = window.user_id;
	if($(this).hasClass('btn-default'))
		var action = 'add';
	else
		var action = 'delete';
	window.action = action;	
	var data = {'user_id': user_id, 'movie_id': movie, 'action': action};
	$.ajax({
		url: 'handler.php',
		type: 'POST',
		data: data,
		success: function(){
			var btn = $('.have_btn[data-movie="'+window.movie+'"]');
			if(action == 'add'){
				btn.removeClass('btn-default').addClass('btn-success').val('You have it!');
			}
			else if(action == 'delete'){
				btn.removeClass('btn-success').addClass('btn-default').val('You don\'t have it!');
			}
		}
	});
		
});

$('#messagebox-button-ok').live('click', function(){
	$(this).parent().parent().hide();
});
	
$('.has-movie').live('click', function(){
	var movie = $(this).data('id');
	window.movie = movie;
	
	var data = {'movie_id': movie};
	$.ajax({
		url: 'handler.php',
		type: 'GET',
		data: data,
		success: function(data){
			if(data != ''){
				$('#messagebox p:first').html(data);
				adjustMessagebox();
			}
		}
	});
		
});

$(document).ready(function(){
	var user_id = window.user_id;
	var ratings = 'ratings';
	var data = {'user_id': user_id, 'ratings': ratings};
	$.ajax({
		url: 'handler.php',
		type: 'GET',
		data: data,
		dataType: 'JSON',
		success: function(data){
			var i = 0;
			while(data['data'][i]){
				var movie = data['data'][i];
				$('select[data-movie="'+movie['id']+'"]').val(movie['rating']);
				i++;
			}
		}
	});
	

});

