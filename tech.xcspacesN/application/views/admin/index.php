<?php
	$path = dirname(__FILE__).'/';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>

<?php include($path.'head.php');?>
<?php include($path.'seo.php');?>
<?php include($path.'css.php');?>

</head>
<body>

<div id="wrapper">

<?php if( !$is_login ){ ?>
<?php include($path.'/header.php');?>
<?php }else{ ?>
<?php include($path.'/header_logined.php');?>
<?php } ?>

<?php include($path.'/left.php');?>

<?php 

	$pages_arr = array('index', 'TopicsEdit', 'spaceEdit', 'TopicNoAudit', 'TopicReplyNoAudit');

	if( !in_array($content, $pages_arr) ){
		include($path . 'content/index.php');
	}else{
		include($path . "content/{$content}.php");
	}

?>

<?php include($path.'footer-nav.php');?>

<?php include($path.'js.php');?>

</div>

</body>
</html>