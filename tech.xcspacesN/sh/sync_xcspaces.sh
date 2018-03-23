#!/bin/bash
# 同步 xcspaces 目录
# author liufengsheng
# 2016-06-19

LOCAL_PATH=/data/web/xcspaces/
# LOCAL_PATH=/data/web/xcspacestest2/
LOCAL_PATH_TEST=/data/web/xcspacestest/
RSYNC=/usr/bin/rsync

fun_update_xcspaces() {
    echo "更新开发服本地 xcspaces 目录"
    cd ${LOCAL_PATH_TEST}/
    $RSYNC -avz --exclude=session/* --exclude=.svn* ${LOCAL_PATH_TEST}/ ${LOCAL_PATH}/
    # $RSYNC -avz --delete --exclude=session/* --exclude=.svn* ${LOCAL_PATH_TEST}/ ${LOCAL_PATH}/ 
    # --delete 同步使数据全部一致
    #chown -R www:www ${LOCAL_PATH}
    #chmod -R 755 ${LOCAL_PATH}
}


# 更新 xcspaces 目录
fun_update_xcspaces




