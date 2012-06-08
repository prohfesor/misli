var api = "/api.php";
var token;
var userid;

var tpl_idea = ' '+
'	<div id="{id}" class="idea well"> '+
'		<div class="pull-right">'+
'			<i class="icon-user"></i> <span class="grey">{usr}</span>'+ 
'			<i class="icon-calendar"></i> <span class="grey">{time}</span>'+
'		</div>'+
'		{txt} '+  
'	</div> '+
'';

$(document).ready(function(){
	check_logged();
	load_ideas();
	load_ideas_public();
});

$("#register form").submit(function(){
		register( $("#register input[name='user']").val(), $("#register input[name='password']").val() );
		return false;
	}
);

$("#auth form").submit(function(){
		login( $("#auth input[name='user']").val(), $("#auth input[name='password']").val() );
		return false;
	}
);

$("#create form").submit(function(){
		create( {
				"m": "ideas.json",
				"t": "POST",
				"auth_token": token,
				"idea[essential]": $("#create textarea[name='essential']").val(), 
				"idea[public]": $("#create input[name='public']:checked").val(),
				"idea[is_public]": $("#create input[name='public']:checked").val() 
			});
		return false;
	}
);

$('#ideas h2 i').click( load_ideas );
$('#ideas_public h2 i').click( load_ideas_public );
$('a[href="#logout"]').click( logout );
$('a[href="#login"]').click( function(){
	$('#auth:hidden').slideDown();
} );
$('a[href="#register"]').click( function(){
	$('#register:hidden').slideDown();
} );


function register( user, password){
	$.ajax({
		url: api,
		type: "post",
		dataType: "json",
		data: {
			"m": "users.json",
			"t": "post",
			"user[email]": user,
			"user[password]": password
		}
	}).done(function(o){
		console.log(o);
		if(o.auth_token){
			$("#register .alert").removeClass('alert-error').addClass('alert-success').show().html( "Auth token: " + o.auth_token + "<br/>User id: " + o.user_id);
			token = o.auth_token;
			userid = o.user_id;
			$("#register form, #auth form").hide();
			load_ideas();
			load_ideas_public();
		}
		if(o.errors){
			$("#register .alert").removeClass('alert-success').addClass('alert-error').show().html( o.errors.toString() );
		}
	});
}


function login( user, password ){
	$.ajax({
		url: api,
		type: "post",
		dataType: "json",
		data: {
			"m": "users/sign_in.json",
			"t": "post",
			"user[email]": user,
			"user[password]": password
		}
	}).done(function(o){
		console.log(o);
		if(o.auth_token){
			$("#auth .alert").removeClass('alert-error').addClass('alert-success').show().html( "Auth token: " + o.auth_token + "<br/>User id: " + o.user_id );
			token = o.auth_token;
			$("#register form, #auth form").hide();
			load_ideas();
			load_ideas_public();
		}
		if(o.error){
			$("#auth .alert").removeClass('alert-success').addClass('alert-error').show().html( o.error.toString() );
		}
		check_logged();
	});
}


function logout(){
	token = false;
	check_logged();	
}


function load_ideas(){
	if(!token){	return false; }
	$.ajax({
		url: api,
		type: "post",
		dataType: "json",
		data: {
			"m": "ideas.json",
			"t": "GET",
			"auth_token": token
		}
	}).done(function(o){
		console.log(o);
		if(o.length){
			$("#ideas .alert").removeClass('alert-error');
			$("#ideas div:first").html( render_ideas(o) );
		} else {
			$("#ideas div:first").html( ".. nothing found" );
		}
		if(o.error){
			$("#ideas .alert").removeClass('alert-success').addClass('alert-error').show().html( o.error.toString() );
		}
	});		
}


function load_ideas_public(){
	if(!token){	return false; }
	$.ajax({
		url: api,
		type: "post",
		dataType: "json",
		data: {
			"m": "ideas/public.json",
			"t": "GET",
			"auth_token": token
		}
	}).done(function(o){
		console.log(o);
		if(o.length){
			$("#ideas_public .alert").removeClass('alert-error');
			$("#ideas_public div:first").html( render_ideas(o) );
		} else {
			$("#ideas_public div:first").html( ".. nothing found" );
		}
		if(o.error){
			$("#ideas_public .alert").removeClass('alert-success').addClass('alert-error').show().html( o.error.toString() );
		}
	});	
}


function render_ideas( a ){
	t = '';
	for(i in a){
		t = t + tpl_idea.replace(/{id}/g, a[i]._id).replace("{txt}", a[i].essential).replace(/{usr}/g, a[i].user_id).replace("{time}", a[i].created_at);
	}
	return t;
}


function create( idea_data ){
	if(!token){	return false; }
	$.ajax({
		url: api,
		type: "post",
		dataType: "json",
		data: idea_data
	}).done(function(o){
		console.log(o);
		if(o && !o.error){
			$("#ideas_public .alert").removeClass('alert-error');
			$("#create form textarea").text('');
			load_ideas();
			load_ideas_public();
		}
		if(o.error){
			$("#ideas_public .alert").removeClass('alert-success').addClass('alert-error').show().html( o.error.toString() );
		}
	});
}


function check_logged(){
	$('#auth, #register').hide();
	if(!token){
		$('header a[href="#login"], header a[href="#register"]').show();
		$('header a[href="#logout"]').hide();
		$('#welcome').show();
		$("#auth form, #register form").show();
		$('#create').hide();
		$('#ideas, #ideas_public').hide();
	} else {
		$('header a[href="#login"], header a[href="#register"]').hide();
		$('header a[href="#logout"]').show();
		$('#welcome').hide()
		$('#create').show();
		$('#ideas, #ideas_public').show();
	}
}
