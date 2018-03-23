<div id="page-wrapper">
    <div id="page-inner">
      <div class="row">
          <div class="col-md-12">
              <h1 class="page-head-line">Topic Edit</h1>
              <h1 class="page-subhead-line"> 主题编辑 </h1>
          </div>
      </div>

      <!-- /. ROW  -->
      <div class="row">
        <div class="col-md-11">
            <form class="form-horizontal" method='post' action="/xcspaceAdmin/TopicsEdit/<?php echo $data['id'];?>">
            <input type="hidden" class="form-control" name="id" value="<?php echo $data['id'];?>">
            <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
            <div class="form-group">
              <label for="Title" class="col-sm-2 control-label">Title</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="Title" id="Title" placeholder="Title" value="<?php echo $data['title'];?>">
              </div>
            </div>
            <div class="form-group">
              <label for="Status" class="col-sm-2 control-label">Status</label>
              <div class="col-sm-10">
                <select class="form-control" name="Status" >
                  <option value="1" <?php if($data['status']==1) echo "selected"; ?> >通过</option>
                  <option value="2" <?php if($data['status']==2) echo "selected"; ?> >拒绝</option>
                  <option value="3" <?php if($data['status']==3) echo "selected"; ?> >审核中</option>
                  <option value="4" <?php if($data['status']==4) echo "selected"; ?> >删除</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="keyworks" class="col-sm-2 control-label">keyWorks</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="keyWorks" id="keyworks" placeholder="keyWorks" value="<?php echo $data['keyworks'];?>" >
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">Description</label>
              <div class="col-sm-10">
                <textarea class="form-control" rows="3" name="Description" id="description" placeholder="Description" ><?php echo $data['description'];?></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="/xcspaceAdmin/TopicNoAudit" class="btn btn-danger">back</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- /. PAGE INNER  -->
</div>

<script>
function modifyTopicsStatus(id){
  var status = $('#sel'+id).val();
  $.post('/xcspaceAdmin/modifyTopicsStatus',{id:id,status:status},function(d){
      //done
      location.href = '/xcspaceAdmin/TopicNoAudit';
  });
}
</script>