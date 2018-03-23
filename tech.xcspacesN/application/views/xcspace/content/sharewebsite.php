
<?php include($path . "/topicNodes.php"); ?>

<div class="wrap tag-index">
    <div class="container">
        <!-- /.stream-list -->
        <!-- <?php foreach($websites as $key => $val): ?>
        <h2 class="h4 mt30"><?php echo $val['type_name'];?></h2>
        <div class="row">
            <?php foreach($val['data'] as $k => $v): ?>
            <div class="col-md-3">
                <div class="media border">
                    <a class="pull-left" href="<?php echo $v['href'];?>" target="_blank">
                    <img class="media-object" width="40" height="40" src="/images/sharewebsite_ico/<?php echo $v['src'];?>" alt="">
                    </a>
                    <div class="media-body">
                        <h3 class="h5 media-heading"><a target="_blank" href="<?php echo $v['href'];?>"><?php echo $v['title'];?></a></h3>
                        <p class="text-muted mb0">
                            <?php if( !empty($v['text']) ){ echo strCut($v['text'], 20); }else{ echo "...";}?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?> -->

        <?php include($path . "/staticShareWebsite.php"); ?>

        <div class="text-center">
        </div>
    </div>
</div>