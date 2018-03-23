
<?php include($path . "/topicNodes.php"); ?>

<div class="profile">
    <header class="xcspace-profile-com-heading">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-xs-offset-3">
                <img src="/images/donation_zfb.png" class="img-responsive" style="max-width: 180px">
            </div>
            <br class="visible-xs-block">
            <div class="col-md-6 hidden-xs">
                <img src="/images/donation_weixin.png" class="img-responsive" style="max-width: 180px">
            </div>
            <div class="col-md-3 col-xs-offset-3 visible-xs-block">
                <img src="/images/donation_weixin.png" class="img-responsive" style="max-width: 180px">
            </div>
        </div>
    </div>
    </header>
    <div class="wrap mt30">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning alert-dismissible mb0 mt10 fmt comment-helper" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="right:0;">
                            <span aria-hidden="true">×</span>
                        </button>
                        捐助：主要目的是为了维持本站每年的服务器费用,希望有你们的支持使我们做的更好！
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <br />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="xcspace-profile-heading"><span>捐助列表</span><span class="pull-right">总人次&nbsp;<?php echo count($data); ?></span>
                    </h4>
                   <!--  <ul class="profile-mine__content">
                        <li>
                            <div class="row">
                                <div class="col-md-9 profile-content-title-warp">
                                    cavalier
                                </div>
                                <div class="col-md-1">
                                    1元
                                </div>
                                <div class="col-md-2">
                                    <span class="xcspace-profile-content-date"><?php echo date('Y年m月d日', time());?></span>
                                </div>
                            </div>
                        </li>
                    </ul> -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>捐赠用户</th>
                                    <th>捐赠金额</th>
                                    <th>捐赠时间</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if( $data ): ?>
                                <tr>
                                    <td>1</td>
                                    <td>Cavalier</td>
                                    <td>1元</td>
                                    <td><?php echo date('Y年m月d日', time());?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if( !$data ): ?>
                    <div class="widget-invite mt30 mb30 text-center">
                    <p>
                    <span class="text-muted">尚无捐助</span>
                    </p>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>