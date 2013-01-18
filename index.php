<?php
ob_start();
session_regenerate_id(true); //always regenerate a new session id

include 'winadollar.methods.php';
$dollars = new Dollars();

$help = '';

if(isset($_REQUEST['help'])) {
	$help = '<img src="accept.png" />';
}

//test to see if session exsists in database
$b = $dollars->testCurrentUser(); //tests to see if current user has a hash
if($b) {//current user has a hash, he can play, use his number
	//do nothing,
}
else {//current user does NOT have a hash or it has expired, you'll need to create one
	$dollars->createUserWinningNumber(); //create number for session
}
$hash_win = $dollars->getUsersWinningNumber();//this is the winning number for this session

if(isset($_POST['j']) && isset($_POST['jk'])) {
	$bool = $dollars->checkUsersWinningNumber($_POST['j']);
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
<!-- check out the true source on https://github.com/xzela/win-a-dollar -->
<html>
<head>
	<title>Win a Credit</title>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="jquery.timer.js"></script>
	<script type="text/javascript" src="init.js"></script>
</head>
<body>
	<header>
		<h1>Win-A-Dollar </h1>
		<h3>the at home, no risk gambling sensation!</h3>
	</header>
	<content>
		<h2>Try your <span class='luck'>luck</span> at finding the winning Square!</h2>
		<div id="content">
			<div id='dollar_area'>
				<table id='dollar_table'>
					<!-- thinking of taking a quick peak, eh? -->
					<!-- you and all of your friends have unique hashes. -->
					<?php for($i = 1; $i < TEN_THOUNSAND; $i++): ?>
						<?php $ctx = hash_init('sha256'); ?>
						<?php hash_update($ctx, SALT . $i); ?>
						<?php $hash = hash_final($ctx); ?>
						<?php if($hash == $hash_win): ?>
							<?php $number ='<span id="' . $hash . '" >' . $help . '</span>'; ?>
						<?php else: ?>
							<?php $number = '<span id="' . $hash . '" >' . '</span>'; ?>
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
		<div class="helper">
			<p>If you need help....</p>
			<?php if(isset($_REQUEST['help'])): ?>
				<p>... click here to <a href='index.php'>hide the winning square</a></p>
			<?php else: ?>
				<p>... click here to <a href='index.php?help=1'>show the winning square</a></p>
			<?php endif; ?>
		</div>
	</content>
</body>
</html>
<?php
ob_flush();
?>
