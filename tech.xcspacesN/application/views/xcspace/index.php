<?php
	$path = dirname(__FILE__).'/';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<?php include($path.'head.php');?>
    <?php include($path.'seo.php');?>
	<?php include($path.'css.php');?>

<!--[if lt IE 9]>
<link rel="stylesheet" href="https://sf-static.b0.upaiyun.com/v-56fe50c3/global/css/ie.css"/>
<script src="./js/html5shiv.js"></script>
<script src="./js/respond.js"></script>
<![endif]-->
<script src="./static/js/jquery.js"></script>

<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?ef89e712f7d29f0b19598a30adccb2e6";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>

<!-- <script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=3957280415" type="text/javascript" charset="utf-8"></script> -->

</head>
<body>

<?php if( !$is_login ){ ?>
<?php include($path.'/header.php');?>
<?php }else{ ?>
<?php include($path.'/header_logined.php');?>
<?php } ?>

<?php 

	$pages_arr = array('index','go','detail','about','faq','advertise','login','register','forgot','createNewTitle','collectionNodes','collectionTopics', 'focusMemberTopics', 'setUserInfo', 'userCenter', 'wish', 'moreTopics', 'member', 'memberTopics', 'memberReplys','daily', 'myCoin', 'notifications', 'avatar', 'tabs', 'setPasswd', 'contact', 'join', 'focusMember', 'search', 'sharewebsite', 'version', 'version2', 'feedback', 'donation', 'prestige', 'memberNotepad', 'createNotepad', 'noteDetail', 'wbLogin', 'qqLogin');

	if( !in_array($content, $pages_arr) ){
		include($path . 'content/index.php');
	}else{
		include($path . "content/{$content}.php");
	}

?>

<?php include($path.'footer-nav.php');?>

<?php include($path.'js.php');?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-82766513-1', 'auto');
  ga('send', 'pageview');

</script>

</body>
</html>
