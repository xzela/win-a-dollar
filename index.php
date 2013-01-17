<?php
ob_start();
session_regenerate_id(true); //always regenerate a new session id

include 'winadollar.methods.php';
$help = '';

if(isset($_REQUEST['help'])) {
	$help = 'me';
}

//test to see if session exsists in database
$b = testCurrentUser(); //tests to see if current user has a hash
if($b) {//current user has a hash, he can play, use his number
	//do nothing,
}
else {//current user does NOT have a hash or it has expired, you'll need to create one
	createUserWinningNumber(); //create number for session
}
$hash_win = getUsersWinningNumber();//this is the winning number for this session

if(isset($_POST['j']) && isset($_POST['jk'])) {
	$bool = checkUsersWinningNumber($_POST['j']);
	$a = array();
	if($bool && $_POST['jk']) {
		$a['validation'] = true;
		$a['message'] = 'Yo Yo Yo! YTMNG! Way to win a credit!';
	}
	else {
		$a['validation'] = false;
		$a['message'] = 'awe... that sucks, you didn\'t win!';
	}
	$a['reset'] = 'well, looks like the boards going to reset in ';
	echo json_encode($a);
	die(); //kills the script
}

?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="jquery.timer.js"></script>
	<script type="text/javascript">
		var token = 5;//seconds
		var jj = true;
		$(document).ready(function() {

			$(".box").bind('click', function() {
				if(jj) {
					$('span').each(function() {
						$(this).removeClass('box').addClass('disabled');
					});
					$(this).addClass('clicked');
					$.post("index.php",{
							j: this.id,
							jk: jj },
						function (data, status) {
							jj = false;
							$('#payout').html(data.message).slideDown();
							$.timer(1000, function(timer) {
								var msg = $('#message');
								msg.html(data.reset + token + ' seconds').slideDown();
								if(token <= 0) {
									window.location.reload(true);
								}
								token--;
							});
					},
					"json");
				}
			});
		});
	</script>
	<style type="text/css">
		body {
			font-family: Tahoma;
			margin: 0 auto;
			width: 800px;
		}

		h1 span {
			font-size: 20px;
		}
		h2 {
			text-align: center;
		}
		#dollar_table span {
			display: block;
			width: 30px;
			height: 30px;
			box-shadow: 1px 1px 5px #666;
			border-radius: 5px;
		}
		#dollar_table span.box {
			cursor: pointer;
			background-color: #fff;
		}
		#dollar_table span.box:hover {
			background-color: green;
		}
		#dollar_table span.disabled {
			background-color: #fff;
			cursor: default;
		}
		#dollar_table span.clicked {
			background-color: red;
		}
		#content {
			margin: 0 auto;
			width: 350px;
		}

		#dollar_area {
			border-radius: 5px;
			background-color: #e69;
			width: 352px;
			padding: 10px;

		}
		#dollar_table {
			margin-left:auto;
			margin-right:auto;
		}

		#payout {
			display: none;
			text-align: center;
		}
		#message {
			display: none;
			text-align: center;
		}
		header h1 {
			text-align: center;
			margin-bottom: 0px;
		}
		header h3 {
			margin-top: 0px;
			text-align: right;
			text-shadow: 1px 1px red;
		}
	</style>
</head>
<body>
	<header>
		<h1>Win-A-Dollar </h1>
		<h3>the no risk gambling sensation.</h3>
	</header>
	<h2>Find the winning Square and win a whole CREDIT!!!</h2>
	<div id="content">
		<div id='dollar_area'>
			<table id='dollar_table'>
			<?php for($i = 1; $i < TEN_THOUNSAND; $i++): ?>
				<?php $ctx = hash_init('sha256'); ?>
				<?php hash_update($ctx, SALT . $i); ?>
				<?php $hash = hash_final($ctx); ?>

				<?php if($hash == $hash_win): ?>
					<?php $number ='<span id="' . $hash . '" class="box" >' . $help . '</span>'; ?>
				<?php else: ?>
					<?php $number = '<span id="' . $hash . '" class="box" >' . '</span>'; ?>
				<?php endif; ?>

				<?php if(($i % ONE_HUNDRED) == 1): ?>
					<tr>
						<td><?php echo $number; ?></td>
				<?php elseif(($i % ONE_HUNDRED) == 0): ?>
						<td><?php echo $number; ?></td>
					</tr>
				<?php else: ?>
					<td><?php echo $number; ?></td>
				<?php endif; ?>
			<?php endfor; ?>

			</table>
		</div>
	</div>
	<div id='payout'></div>
	<div id='message'></div>

<p>If you need help....</p>
<?php if(isset($_REQUEST['help'])): ?>
	<p>... click here to <a href='index.php'>hide the winning square</a></p>
<?php else: ?>
	<p>... click here to <a href='index.php?help=1'>show the winning square</a></p>
<?php endif; ?>
</body>
</html>
<?php
ob_flush();
?>
