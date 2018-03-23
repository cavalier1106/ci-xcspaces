<h2 class="h4 mt30"><?php echo $type_name;?></h2>
<div class="row">
    <?php foreach($data as $k => $v): ?>
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