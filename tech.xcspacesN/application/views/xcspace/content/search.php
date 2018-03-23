
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap">
    <div class="container mt20">
        <form action="/search" class="row">
            <div class="col-md-9">
                <input class="form-control" type="text" name="s" value="<?php if(isset($s)) echo $s; ?>" placeholder="输入关键字搜索">
            </div>
            <br class="visible-xs-block" >
            <div class="col-md-2">
                <button type="submit" class="btn btn-info btn-block form-control">搜索</button>
            </div>
        </form>
    </div>
    <div class="container mt20">
        <div class="row">
            <div class="col-md-9 main search-result">
                <h3 class="h5 mt0">找到约 <strong><?php echo $counts; ?></strong> 条结果</h3>

                <?php foreach($datas as $k => $v ): ?>
                    <section class="widget-question">
                    <h2 class="h4">
                        <a href="/detail/<?php echo $v['hashId']; ?>" target="_blank"><?php echo $v['title']; ?></a>
                    </h2>
                    <p class="excerpt">
                        <?php echo strCut( $v['content'], 250 ); ?>
                    </p>
                    </section>
                <?php endforeach; ?>

                <div class="text-center">
                    <?php if( $page ): ?>
                        <div class="text-center">
                            <ul class="pagination">
                                <?php echo $page;?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <div class="col-md-3 side">
                <ul class="list-unstyled">
                    <li><a target="_blank" href="https://www.google.com/?gws_rd=ssl#newwindow=1&q=site:xcspaces.com+<?php if(isset($s)) echo $s; ?>">在 Google 中搜索 »</a></li>
                    <li><a target="_blank" href="http://www.baidu.com/s?wd=site%3Axcspaces.com%20<?php if(isset($s)) echo $s; ?>">在 百度 中搜索 »</a></li>
                </ul>
            </div>
        </div>
    </div>

</div>
