
<style type="text/css">

html body {
	margin-top: 50px !important;
}

#top_form {
	position: fixed;
	top:0;
	left:0;
	width: 100%;
	
	margin:0;
	
	z-index: 2100000000;
	-moz-user-select: none; 
	-khtml-user-select: none; 
	-webkit-user-select: none; 
	-o-user-select: none; 
	
	border-bottom:1px solid #151515;
	
    background:#FFC8C8;
	
	height:45px;
	line-height:45px;
}

#top_form input[name=url] {
	width: 550px;
	height: 20px;
	padding: 5px;
	font: 13px "Helvetica Neue",Helvetica,Arial,sans-serif;
	border: 0px none;
	background: none repeat scroll 0% 0% #FFF;
}

div.close
{
	float: right;
	margin-right: 20px;
	cursor: default;
}

div.open
{
	position: fixed;
	top:0;
	right: 0;
	width: 20px;
	height:45px;
	line-height:45px;

	z-index: 2100000000;
	-moz-user-select: none;
	-khtml-user-select: none;
	-webkit-user-select: none;
	-o-user-select: none;

	background:#FFC8C8;

	padding-left: 10px;
	padding-right: 20px;

	cursor: default;
}

</style>

<script>
var url_text_selected = false;

function smart_select(ele){

	ele.onblur = function(){
		url_text_selected = false;
	};
	
	ele.onclick = function(){
		if(url_text_selected == false){
			this.focus();
			this.select();
			url_text_selected = true;
		}
	};
}
</script>

<script src="../../../web/assets/js/jquery-3.2.1.js"></script>

<div class="open" style="display: none">Show</div>

<div id="top_form">
	<div class="close">X</div>

	<div class="url-form" style="width:80%; margin:0 auto;">
	
		<form method="post" action="proxy/confirm" target="_top" style="margin:0; padding:0;">
			<input type="button" value="Home" onclick="window.location.href='proxy'">
			<input type="button" value="Select single element" onclick="window.location.href=''">
			<input type="button" value="Select multiple elements" onclick="window.location.href=''">
			<input type="text" name="url" value="<?php echo $url; ?>" autocomplete="off">
			<input type="hidden" name="form" value="1">
			<input type="submit" value="Go">
		</form>
		
	</div>
	
</div>

<script type="text/javascript">
	smart_select(document.getElementsByName("url")[0]);

	$(document).ready(function(){
		$('.close').on('click', function(){
			$('#top_form').css('display', 'none');
			$('.open').css('display', '');
		});

		$('.open').on('click', function(){
			$('#top_form').css('display', '');
			$(this).css('display', 'none');
		});
	});
</script>
