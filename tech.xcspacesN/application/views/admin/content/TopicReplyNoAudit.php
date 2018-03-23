<div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Topic Reply No Audit</h1>
                        <h1 class="page-subhead-line"> 编辑 没有审核的主题回复 </h1>

                    </div>
                </div>
               
                <!-- /. ROW  -->
                 <div class="row">
                   <div class="col-md-12">
                       <div class="panel panel-primary">
      <!-- Default panel contents -->
      <div class="panel-heading">Topic No Audit</div>

      <!-- Table -->
      <table class="table">
        <thead>
          <tr>
            <th>选择</th>
            <th>回复用户</th>
            <th>回复状态</th>
            <th>回复时间</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $k => $v): ?>
            <tr class="<?php if($v['status']!=2){echo "list-group-item-success";}else{echo "list-group-item-danger";} ?>">
              <td><input type="checkbox" value="<?php echo $v['id']; ?>"></td>
              <td><?php echo $v['reply_username']; ?></td>
              <td>
                <select class="form-control" id="sel<?php echo $v['id']; ?>" >
                    <option value="1" <?php if($v['status']==1) echo "selected"; ?> >通过</option>
                    <option value="2" <?php if($v['status']==2) echo "selected"; ?> >拒绝</option>
                    <option value="3" <?php if($v['status']==3) echo "selected"; ?> >审核中</option>
                    <option value="4" <?php if($v['status']==4) echo "selected"; ?> >删除</option>
                </select>
              </td>
              <td><?php echo date("Y-m-d", $v['time']); ?></td>
              <td>
              <a href="javascript:;" onclick="modifyTopicsReplyStatus(<?php echo $tid; ?>,<?php echo $v['id']; ?>)" class="btn btn-primary">保存</a>
              </td>
            </tr>
            <tr class="list-group-item-success">
              <td colspan="5" ><?php echo strip_tags( $v['content'] ); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php if( $page ): ?>
      <div class="text-center">
          <ul class="pagination">
              <?php echo $page;?>
          </ul>
      </div>
      <?php endif; ?>
    </div>
           </div>
             </div>
    </div>
    <!-- /. PAGE INNER  -->
</div>

<script>
function modifyTopicsReplyStatus(tid,rid){
  var status = $('#sel'+rid).val();
  $.post('/xcspaceAdmin/modifyTopicsReplyStatus',{tid:tid,rid:rid,status:status,'<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'},function(d){
      //done
      location.href = '/xcspaceAdmin/TopicReplyNoAudit/'+tid;
  });
}
</script>