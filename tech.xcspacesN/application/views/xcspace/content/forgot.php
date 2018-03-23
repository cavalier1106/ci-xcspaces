<div id="Wrapper">
    <div class="content">
        
        <div id="Leftbar"></div>
        <div id="Rightbar">
            <div class="sep20"></div>  

            <?php include($path . "/{$user_tab}.php"); ?>
        
        </div>
        <div id="Main">
            <div class="sep20"></div>
            <div class="box">
<div class="header"><a href="/">xcspace</a> <span class="chevron">&nbsp;›&nbsp;</span> 通过电子邮件重设密码</div>

<div class="inner">
    <form method="post" action="/forgot">
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tbody><tr>
            <td width="120" align="right">用户名</td>
            <td width="auto" align="left"><input type="text" class="sl" name="u" value=""></td>
        </tr>
        <tr>
            <td width="120" align="right">注册邮箱</td>
            <td width="auto" align="left"><input type="text" class="sl" name="e" value=""></td>
        </tr>
        <tr>
            <td width="120" align="right">你是机器人么？</td>
            <td width="auto" align="left"><div style="background-image: url('/_captcha?once=84620'); background-repeat: no-repeat; width: 320px; height: 80px; border-radius: 3px; border: 1px solid #ccc;"></div><div class="sep10"></div><input type="text" class="sl" name="c" value="" autocorrect="off" spellcheck="false" autocapitalize="off" placeholder="请输入上图中的验证码"></td>
        </tr>
        <tr>
            <td width="120" align="right"></td>
            <td width="auto" align="left"><input type="hidden" name="once" value="84620"><input type="submit" class="super normal button" value="继续">
            </td>
        </tr>
        <tr>
            <td width="120" align="right"></td>
            <td width="auto" align="left"><span class="gray">24 小时内，至多可以重新设置密码 2 次。</span></td>
        </tr>
    </tbody></table>
    </form>
</div>
</div>

        </div>
        
        
    </div>
    <div class="c"></div>
    <div class="sep20"></div>
</div>