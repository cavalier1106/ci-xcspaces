<div class="global-nav">
    <nav class="container nav">

    <div class="dropdown m-menu">
        <a href="javascript:void(0);" id="dLabel" class="visible-xs-block m-toptools" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="glyphicon glyphicon-align-justify"></span>
        <span class="mobile-menu__unreadpoint"></span>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <!-- <li class="xcspace-mobile-menu-item"><a href="/">响创空间</a></li> -->
            <li><a href="/user/memberTopics/<?php echo $user_info['id']; ?>">我的中心</a></li>
            <!-- <li><a href="javascript:;">我的档案</a></li> -->
            <li><a href="/createNotepad">我的笔记</a></li>
            <li><a href="/user/setUserInfo">账号设置</a></li>
            <li><a href="/loginreg/logout/<?php echo $user_info['name']?>" >退出</a></li>
            <li class="divider"></li>
            <li class="xcspace-mobile-menu-item"><a href="/detailpage/faq">常见问题</a></li>
            <li><a href="/feedback">建议反馈</a></li>
            <li><a href="/detailpage/sharewebsite">酷站</a></li>
            <li><a href="/donation">捐助</a></li>
        </ul>
    </div>

    <h1 class="logo"><a class="xcs" href="/">XCSPACES</a></h1>

    <ul class="menu list-inline pull-left hidden-xs">
        <!-- <li class="menu__item">
            <a href="/">话题</a></li> -->
        <li class="menu__item">
            <a href="http://www.xcspaces.com/">首站</a>
        </li>
        <li class="menu__item">
            <a href="/detailpage/sharewebsite">酷站</a>
        </li>
        <li class="menu__item">
            <a href="/donation">捐助</a>
        </li>
        <li class="menu__item">
        <!-- <a href="/user/note">笔记</a></li> -->
        <li class="menu__item">
        <!-- <a href="/events">活动</a> -->
        </li>
    </ul>
    <a href="/user/memberTopics/<?php echo $user_info['id']; ?>" class="visible-xs-block pull-right xcs-img m-toptools">
        <?php if(isset($_SESSION['user']['isQQLogin']) && $_SESSION['user']['isQQLogin']){ ?>
            <img src="<?php echo $_SESSION['user']['qq_info']['figureurl']; ?>" class="dropdownBtn user-avatar" data-toggle="dropdown"  border="0" align="default">
        <?php }else{ ?>
            <img src="/images/<?php if(!empty($avatar_arr)){echo 'avatar/' . $user_info['id'].'/'.$user_info['id'].'_normal.png';}else{echo $user_info['img_path'];}?>" class="dropdownBtn user-avatar" data-toggle="dropdown"  border="0" align="default">
        <?php } ?>
    </a>
    <a href="/search" class="visible-xs-block pull-right xcs-searched m-toptools">
        <!-- <i class="fa fa-search xcs-border"></i> -->
        <span class="glyphicon glyphicon-search xcs-border"></span>
    </a>
    <a href="/createNewTitle" class="visible-xs-block pull-right xcs-logined m-toptools">
        <span class="glyphicon glyphicon-pencil xcs-border"></span>
    </a>
    <!-- <a href="/createNewTitle" class="visible-xs-block pull-right">
        <img src="/images/avatar/<?php echo $user_info['id'].'/'.$user_info['id'].'_normal.png';?>" border="0" align="default">
    </a> -->
    <ul class="opts pull-right list-inline hidden-xs">
        
        <li class="opts__item dropdown hoverDropdown write-btns">
            <a href="./user/notifications?uid=<?php echo $user_info['id']?>">
                <?php if( $notifications > 0 ){ ?>
                    <span class="badge label-info"><?php echo $notifications; ?></span> 条未读提醒
                <?php }else{ ?>
                    <?php echo $notifications; ?> 条未读提醒
                <?php } ?>
                
            </a>
        </li>
        <!-- <li class="opts__item dropdown hoverDropdown write-btns">
            <a href="/createNewTitle">写话题</a>
        </li> -->
        <li class="opts__item dropdown hoverDropdown write-btns">
        <a class="dropdownBtn" data-toggle="dropdown" href="javascript:;">撰写<span class="caret"></span></a>
        <ul class="dropdown-menu dropdown-menu-right ">
            <li><a href="/createNewTitle">写话题</a></li>
            <li><a href="/createNotepad">写笔记</a></li>
        </ul>
        </li>
        <?php if(isset($_SESSION['user']['isQQLogin']) && $_SESSION['user']['isQQLogin']): ?>
            <li>
                欢迎 <a href="javascript:;" ><?php echo $_SESSION['user']['qq_info']['nickname'];?></a>
            </li>
        <?php endif; ?>
        <li class="opts__item user dropdown hoverDropdown">
        <?php if(isset($_SESSION['user']['isQQLogin']) && $_SESSION['user']['isQQLogin']){ ?>
            <img src="<?php echo $_SESSION['user']['qq_info']['figureurl']; ?>" class="dropdownBtn user-avatar" data-toggle="dropdown"  border="0" align="default">
        <?php }else{ ?>
            <img src="/images/<?php if(!empty($avatar_arr)){echo 'avatar/' . $user_info['id'].'/'.$user_info['id'].'_normal.png';}else{echo $user_info['img_path'];}?>" class="dropdownBtn user-avatar" data-toggle="dropdown"  border="0" align="default">
        <?php } ?>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="/user/memberTopics/<?php echo $user_info['id']; ?>">我的中心</a></li>
            <!-- <li><a href="javascript:;">我的档案</a></li> -->
            <!-- <li><a href="javascript:;">我的笔记</a></li> -->
            <li><a href="/user/setUserInfo">账号设置</a></li>
            <li class="divider"></li>
            <li><a href="/detailpage/faq">常见问题</a></li>
            <li><a href="/feedback">建议反馈</a></li>
            <li><a href="/loginreg/logout/<?php echo $user_info['name']?>" >退出</a></li>
        </ul>
        </li>
    </ul>
    <form action="/search" class="header-search pull-right hidden-sm hidden-xs">
        <button class="btn btn-link"><span class="sr-only">搜索</span><span class="glyphicon glyphicon-search"></span></button>
        <input name="s" type="text" placeholder="输入关键字搜索" class="form-control" value="">
    </form>
    </nav>
</div>