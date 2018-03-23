
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap tag-index">
    <div class="container">
        <h2 class="h4 mt30">
            全部空间
        </h2>
        <div class="row tag-list mt20">
            <?php foreach($nodes as $k => $v): ?>
            <section class="xcspace-tag-list-item col-md-3">
            <div class="xcspace-tag-list-itemWraper">
                <h3 class="h5 xcspace-tag-list-itemheader"><?php echo $v['fid']['name']; ?></h3>
                <ul class="xcspace-tag-list-itembody xcspace-taglist-inline multi">
                    <?php foreach($v['zid'] as $k2 => $v2): ?>
                    <li class="tagPopup">
                    <a href="/go/<?php echo $v2['tab_name']; ?>" class="tag" ><?php echo $v2['name']; ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            </section>
        <?php endforeach; ?>
        </div>
        <!-- /.stream-list -->
        <!-- <h2 class="h4 mt30">合作伙伴</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="media border">
                    <a class="pull-left" href="/101">
                    <img class="media-object" width="40" height="40" src="https://sfault-avatar.b0.upaiyun.com/393/562/3935623170-1140000000142909_big64" alt="101 新手上路">
                    </a>
                    <div class="media-body">
                        <h3 class="h5 media-heading"><a href="/101">101 新手上路</a></h3>
                        <p class="text-muted mb0">
                            面向新手开发者的问题集中营
                        </p>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="widget-box visible-xs-block">
           <?php include($path . "/set-xcSpaces-jiathis.php"); ?>
        </div>
        <div class="text-center">
        </div>
    </div>
</div>