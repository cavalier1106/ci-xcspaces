<div class="global-navTags global-green">
    <div class="container lfs-node">
        <div class="iwrapper">
            <div class="iscroller">
                <nav class="nav">
                    <ul class="xcspace-nav-list">
                        <li class="xcspace-nav-item xcspace-tag-nav-item <?php if ($tab == 'home') {echo "tab_current";} else {echo "tab";}?>"><a href="/">home</a></li>
                        <?php foreach ($nodes['zid'] as $k => $v): ?>
                            <li class="xcspace-nav-item xcspace-tag-nav-item <?php if ($v['tab_name'] == $tab) {echo "tab_current";} else {echo "tab";}?>"><a href="./go/<?php echo $v['tab_name']; ?>"><?php echo $v['name']; ?></a></li>
                        <?php endforeach;?>
                        <!-- <li class="xcspace-nav-item xcspace-tag-nav-item tab" data-open="0">
                            <a class="xcspace-nav-item-more-link" href="/tnodes">
                                ●●●
                            </a>
                        </li> -->
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</div>
<!-- <div class="global-navTags global-gray">
    <div class="container lfs-znode">
        <div class="iwrapper">
            <div class="iscroller">
                <nav class=" nav">
                    <ul class="xcspace-nav-list">
                        <?php foreach ($znodes['zid'] as $k => $v): ?>
                            <li class="xcspace-nav-item xcspace-tag-nav-item"><a href="./go/<?php echo $v['tab_name']; ?>"><?php echo $v['name']; ?></a></li>
                        <?php endforeach;?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div> -->

<script type="text/javascript" src="/static/js/iscroll.js"></script>
<script type="text/javascript">

$(function () {
    $('.iwrapper').each(function(idx, item){
        new IScroll(item, { eventPassthrough: true, scrollX: true, scrollY: false, preventDefault: false });
    });

})


</script>