<div id="page-wrapper">
    <div id="page-inner">
      <div class="row">
          <div class="col-md-12">
              <h1 class="page-head-line">space Edit</h1>
              <h1 class="page-subhead-line"> 空间编辑 </h1>
          </div>
      </div>

      <!-- /. ROW  -->
      <div class="row">
        <div class="col-md-11">
            <form class="form-horizontal" method='post' action="/xcspaceAdmin/spaceEdit/<?php echo $data['zid'];?>">
            <input type="hidden" class="form-control" name="<?php echo $this->token_name;?>" value="<?php echo $this->token_hash;?>">
            <input type="hidden" class="form-control" name="zid" value="<?php echo $data['zid'];?>">
            <div class="form-group">
              <label for="Name" class="col-sm-2 control-label">Name</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="Name" id="Name" placeholder="Name" value="<?php echo $data['name'];?>">
              </div>
            </div>
            <div class="form-group">
              <label for="tabName" class="col-sm-2 control-label">tabName</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="tabName" id="tabName" placeholder="tabName" value="<?php echo $data['tab_name'];?>">
              </div>
            </div>
            <div class="form-group">
              <label for="imgPath" class="col-sm-2 control-label">imgPath</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="imgPath" id="imgPath" placeholder="imgPath" value="<?php echo $data['img_path'];?>">
              </div>
            </div>
            <div class="form-group">
              <label for="Summary" class="col-sm-2 control-label">Summary</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="Summary" id="Summary" placeholder="Summary" value="<?php echo $data['summary'];?>">
              </div>
            </div>
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
                <a href="/xcspaceAdmin" class="btn btn-danger">back</a>
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
  $.post('/xcspaceAdmin/modifyTopicsStatus',{id:id,status:status,'<?php echo $this->token_name;?>':'<?php echo $this->token_hash;?>'},function(d){
      //done
      location.href = '/xcspaceAdmin/TopicNoAudit';
  });
}
</script>