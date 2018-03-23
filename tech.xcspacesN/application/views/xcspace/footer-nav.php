<footer id="footer" class="footer hidden-print">
<div class="container">
    <div class="row">
        <div class="footer-about col-md-5 col-sm-12" id="about">
            <h4>关于 xcSpaces</h4>
          <!--   <p>
xcSpaces 是分享创意、分享创业、分享交流的空间。
希望大家能通过这个平台，把生活中一些比较有创意的想法、有趣的事物通过思想交流的方式传递一些有价值的信息给想创业的朋友，在这里还可以找到志同道合的朋友的机会。
            </p> -->
            <p>
                xcSpaces ( www.xcspaces.com ) 是中国领先的创业梦想家分享空间。
我们希望为创业梦想家提供一个充满创意、有梦想、有激情、高质的交流空间，
与创业梦想家一起学习、交流与成长，创造属于创业梦想家的时代！
            </p>
           <!--  <p>
                自<time>2016年6月1日</time>上线以来已经有10000这个用户注册
            </p> -->
            <p>
                反馈或建议请发送邮件至：xcspaceswork@Gmail.com
            </p>
        </div>
        <div class="footer-links col-md-2 col-sm-12 hidden-xs">
            <h4>xcSpaces</h4>
            <ul class="list-unstyled">
                <li><a href="/detailpage" target="_blank" >关于我们</a></li>
                <!-- <li><a href="/detailpage/join" target="_blank" >加入我们</a></li> -->
                <li><a href="/detailpage/wish" target="_blank" >我们的目标</a></li>
                <li><a href="/detailpage/version">发展历程</a></li>
                <?php if(0): ?>
                <li><a href="/detailpage/advertise" target="_blank" >广告投放</a></li>
                <?php endif; ?>
                <!-- <li><a href="/detailpage/contact" target="_blank" >联系我们</a></li> -->
                <li><a href="/detailpage/faq" target="_blank" >常见问题</a></li>
                <li><a href="/feedback" target="_blank" >建议反馈</a></li>
            </ul>
        </div>
        <div class="footer-techs col-md-3 col-sm-12 hidden-xs">
            <h4>xcSpaces 当前用户</h4>
            <ul class="list-unstyled">
                <li><?php echo $this->user_online_counts; ?> 人在线</li>
                <li>最高记录 <?php echo $this->user_online_max_counts; ?></li> 
            </ul>
            <?php if(0): ?>
            <h4>语言</h4>
            <ul class="list-unstyled">
                <li><a href="javascript:;" target="_blank" >English</a></li>
                <li><a href="javascript:;" target="_blank" >中文(简体)</a></li>
                <li><a href="javascript:;" target="_blank" >中文(繁体)</a></li> 
            </ul>
            <?php endif; ?>
        </div>
        <div class="footer-sponsors col-md-2 col-sm-12 hidden-xs">
            <h4>关注 xcSpaces</h4>
            <p>
                <a href="http://www.xcspaces.com" style="border-bottom: none" target="_blank" onclick="_hmt.push(['_trackEvent', 'footer', 'click', 'footer-upyun.com'])"><img src="./images/xcspace-code.png" style="width: 120px" alt="xcspaces.com"></a>
            </p>
        </div>
    </div>
</div>
<div class="copy-right">
    Copyright © 2016-2017 xcspaces. 当前呈现版本 01.01.00
    <br />
    <a href="http://www.miibeian.gov.cn/" target="_blank">粤ICP备16052906号-1</a><!-- <span>粤公网安备11010802014853</span> -->
</div>
</footer>


<div class="gotop hidden-xs" id="gotop" style="display: none;" ><a href="javascript:;" class="th7-weixin"></a>
<div class="th7-weixin-con hide">
<p class="th7-weixin-title">微信扫一扫</p>
<div class="th7-weixin-pic"> <img src="/images/xcspace-code.png" alt="xcSpace微信公众平台" width="100" height="100"></div>
</div>
<a href="/feedback" target="_blank" class="feedback" rel="nofollow"></a><a href="javascript:;" class="go"></a>
</div>