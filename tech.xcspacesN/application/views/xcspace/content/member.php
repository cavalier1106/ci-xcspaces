<div id="Wrapper">
        <div class="content">
            
            <div id="Leftbar"></div>

<div id="Rightbar">
<!--div class="sep20"></div>

<div class="box">
<div class="cell"><img src="./images/neue_comment.png" width="18" align="absmiddle"> &nbsp;cavalier1106 最近的时间轴更新</div>
<div id="statuses">

<div class="cell" id="s_628078" style="line-height: 140%;">
@<a href="/member/CodingNET">CodingNET</a>
<div class="sep5"></div>
<span class="gray">45 分钟前</span>
</div>

<div class="cell" id="s_627978" style="line-height: 140%;">
烦烦烦 ..................
<div class="sep5"></div>
<span class="gray">9 天前</span>
</div>

<div class="cell" id="s_627973" style="line-height: 140%;">
为了一个女生 选择去她的城市发展 ... 这种选择合理？ 而且那个女生因异地恋不能接受，而且渐渐不怎么喜欢自己了，该怎么抉择？
<div class="sep5"></div>
<span class="gray">9 天前</span>
</div>

<div class="cell" id="s_627957" style="line-height: 140%;">
个人 技术领域的发展 和 转 管理层 问题
<div class="sep5"></div>
<span class="gray">11 天前</span>
</div>

</div>    
</div>
<div class="sep20"></div-->


<div class="sep20"></div>
<?php include($path . "/{$user_tab}.php"); ?>
<div class="sep20"></div>
<div class="box">
<div class="inner" align="center">
<a href="<?php echo $ads['logo_href']; ?>" target="_blank">
<img src="./images/<?php echo $ads['logo_path']; ?>" border="0" width="250" height="250" alt="100offer">
</a>
</div>
</div>
<div class="sep20"></div>


</div>



<div id="Main">

<div class="sep20"></div>


<div class="box">


<div class="cell">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tbody>
            <tr>
                <td width="73" valign="top" align="center">
                    <img src="./images/<?php echo $one_user_info['avatar'];?>" class="avatar" border="0" align="default" style="width: 73px; height: 73px;">
                    <?php if( $is_login ): ?>
                    <div class="sep10"></div>
                    <strong class="online">ONLINE</strong>
                    <?php endif; ?>
                </td>
                <td width="10"></td>
                <td width="auto" valign="top" align="left">
                    <div class="fr">
                    <?php if( $is_login ): ?>
                        <?php if( $one_user_info['id'] != $user_info['id']): ?>
                        <?php if( !$is_favorite ){ ?>
                        <input type="button" value="加入特别关注" onclick="location.href = '/favorite/<?php echo $one_user_info['id'];?>?t=users_str';" class="super special button">
                        <?php }else{ ?>
                        <input type="button" value="取消特别关注" onclick="location.href = '/unfavorite/<?php echo $one_user_info['id'];?>?t=users_str';" class="super inverse button">
                        <?php } ?>
                        <?php endif; ?>
                    <?php endif; ?>
                        <!--div class="sep10"></div>
                        <input type="button" value="Block" onclick="if (confirm('确认要屏蔽 wuyinyin？')) { location.href = '/block/141818?t=1450074470'; }" class="super normal button"-->
                    </div>
                    <h1 style="margin-bottom: 5px;"><?php echo $one_user_info['name'];?></h1>
                    <span class="bigger"></span>
                    <div class="sep10"></div>
                    <span class="gray">
                    <li class="fa fa-time"></li> &nbsp; xcspace 第 <?php echo $one_user_info['id'];?> 号会员，加入于 <?php echo date('Y-m-d H:i:s', $one_user_info['reg_time']);?>，今日活跃度排名 
                    <a href="/top/dau">62</a>
                    </span>
                    <div class="sep10"></div>
                    <div class="balance_area" style=""><?php echo $one_user_info['silver'];?>
                    <img src="./images/silver.png" alt="S" align="absmiddle" border="0" style="padding-bottom: 2px;">&nbsp;<?php echo $one_user_info['coin'];?>
                    <img src="./images/bronze.png" alt="B" align="absmiddle" border="0">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="sep5"></div>
</div>
    
<div class="cell markdown_body"></div>
    
</div>

<div class="sep20"></div>

<div class="box">
    <div class="cell_tabs">
    <div class="fl">
    <img src="./images/<?php echo $one_user_info['avatar'];?>" style="width: 24px; height: 24px; border-radius: 24px; margin-top: -2px;" border="0">
    </div>
    <a href="javascript:;" class="cell_tab"><?php echo $one_user_info['name'];?> 创建的所有主题</a>
    <!--a href="/member/cavalier1106" class="cell_tab_current">cavalier1106 创建的所有主题</a>
    <a href="/member/cavalier1106/qna" class="cell_tab">test</a-->
    </div>

<?php foreach($user_center['topics'] as $k => $v): ?>
<div class="cell item" style="">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tbody>
            <tr>
                <td width="auto" valign="middle">
                    <span class="item_title">
                        <a href="./detail/<?php echo $v['id'];?>"><?php echo $v['title'];?></a>
                    </span>
                    <div class="sep5"></div>
                    <span class="small fade">
                    <div class="votes"></div>
                    <a class="node" href="./go/<?php echo $v['tab_name'];?>"><?php echo $v['name'];?></a> &nbsp;•&nbsp; 
                    <strong>
                    <a href="/user/member/<?php echo $one_user_info['id'];?>"><?php echo $one_user_info['name'];?></a>
                    </strong> &nbsp;•&nbsp; <?php echo date('Y-m-d H:i:s', $v['time']);?>
                    </span>
                </td>
                <td width="70" align="right" valign="middle">
                    <!--a href="javascript:;" class="count_livid">19</a-->
                </td>
        </tr>
    </tbody></table>
</div>
<?php endforeach; ?>

    
<div class="inner"><span class="chevron">»</span> <a href="/user/memberTopics/<?php echo $one_user_info['id'];?>"><?php echo $one_user_info['name'];?> 创建的更多主题</a></div>
    
    
</div>
<div class="sep20"></div>
<div class="box">
    <div class="cell"><span class="gray"><?php echo $one_user_info['name'];?> 最近回复了</span></div>
    
    <?php foreach($user_center['replys'] as $k => $v): ?>
    <div class="dock_area">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tbody>
                <tr>
                    <td style="padding: 10px 15px 8px 15px; font-size: 12px; text-align: left;">
                        <div class="fr">
                        <span class="fade"><?php echo date('Y-m-d H:i:s', $v['time']);?></span>
                        </div>
                        <span class="gray">回复了 <?php echo $v['create_username'];?> 创建的主题 
                        <span class="chevron">›</span> 
                        <a href="./detail/<?php echo $v['tid'];?>"><?php echo $v['title'];?></a>
                        </span>
                    </td> 
                </tr>
            <tr>
                <td align="left"><img src="/images/arrow.png" style="margin-left: 20px;"></td>
            </tr>
        </tbody></table>
    </div>
    <div class="inner">
        <div class="reply_content">@<a href="javascript:;">qiayue</a> <?php echo $v['content'];?></div>
    </div>
    <?php endforeach; ?>

    <!--div class="dock_area">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tbody><tr>
                <td style="padding: 10px 15px 8px 15px; font-size: 12px; text-align: left;"><div class="fr"><span class="fade">2 天前</span></div><span class="gray">回复了 <?php echo $one_user_info['name'];?> 创建的主题 <span class="chevron">›</span> <a href="/t/266289#reply5">公众号推荐关注跟直接关注，在腾讯回调结果没有区别</a></span></td> 
            </tr>
            <tr>
                <td align="left"><img src="/images/arrow.png" style="margin-left: 20px;"></td>
            </tr>
        </tbody></table>
    </div>
    <div class="inner">
        <div class="reply_content">@<a href="/member/qiayue">qiayue</a> 公司的肯定认证了的</div>
    </div-->
    
    <div class="inner">
        <span class="chevron">»</span> 
        <a href="/user/memberReplys/<?php echo $one_user_info['id'];?>"><?php echo $one_user_info['name'];?> 创建的更多回复</a>
    </div>
    
</div>

            </div>
            
            
        </div>
        <div class="c"></div>
        <div class="sep20"></div>
    </div>