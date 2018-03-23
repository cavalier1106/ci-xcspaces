 <!-- /. NAV SIDE  -->
  <div id="page-wrapper">
      <div id="page-inner">
          <div class="row">
              <div class="col-md-12">
                  <h1 class="page-head-line">XCSPACES</h1>
                  <h1 class="page-subhead-line">
                  响创空间是分享创意、分享创业、分享交流的空间。 希望大家能通过这个平台，把生活中一些比较有创意的想法、有趣的事物通过思想交流的方式传递一些有价值的信息给想创业的朋友，在这里还可以找到志同道合的朋友的机会。
                  </h1>
              </div>
          </div>
          <!-- /. ROW  -->

          <!-- <hr /> -->


<div class="row">
<div class="col-md-12">
<div class="panel panel-default">

<div class="panel-heading">
Topics Menu
</div>

<div class="panel-body">

<ul class="media-list">

<form class="form-inline" action="/xcspaceAdmin" method="post">
<?php foreach($nodes as $k => $v ): ?>
<li class="media">

<a class="pull-left" href="#">
<!-- <img class="media-object img-circle" src="static/admin/assets/img/user.png"> -->
<img class="media-object img-circle" src="images/<?php echo $v['fid']['img_path'];?>">
</a>

<div class="media-body">

  <h4 class="media-heading"><?php echo $v['fid']['name'];?></h4>

  <div class="form-group">
    <label for="">Topic Name:</label>
    <input type="text" class="form-control" name="TopicName" placeholder="Topic Name" style="width:120px;" value="<?php echo $v['fid']['name'];?>" >
  </div>
  <div class="form-group">
    <label for="">Topic Tab Name:</label>
    <input type="text" class="form-control" name="TopicTabName" placeholder="Topic Name" style="width:120px;" value="<?php echo $v['fid']['tab_name'];?>" >
  </div>
  <div class="form-group">
    <label for="">Sort:</label>
    <input type="test" class="form-control" name="sort" placeholder="Sort" style="width:60px;" value="<?php echo $v['fid']['sort'];?>" >
  </div>
  <div class="form-group">
    <label for="">Status:</label>
    <select class="form-control" name="status" >
      <option value="0" <?php if($v['fid']['status']==0) echo 'selected';?> >审核状态</option>
      <option value="1" <?php if($v['fid']['status']==1) echo 'selected';?> >通过</option>
      <option value="2" <?php if($v['fid']['status']==2) echo 'selected';?> >拒绝</option>
      <option value="3" <?php if($v['fid']['status']==3) echo 'selected';?> >审核中</option>
      <option value="4" <?php if($v['fid']['status']==4) echo 'selected';?> >删除</option>
    </select>
  </div>
  <button type="button" class="btn btn-success" onclick="xcspaces.SaveTopicsMenu(this,<?php echo $v['fid']['zid'];?>,'<?php echo $this->token_name;?>','<?php echo $this->token_hash;?>')" >Save</button>
  <a class="btn btn-primary" href="/xcspaceAdmin/spaceEdit/<?php echo $v['fid']['zid'];?>" >Edit</a>
  <button type="button" class="btn btn-danger" onclick="xcspaces.DelTopicsMenu(<?php echo $v['fid']['zid'];?>,'<?php echo $this->token_name;?>','<?php echo $this->token_hash;?>')" >Del</button>

<?php foreach($v['zid'] as $k2 => $v2 ): ?>
<!-- Nested media object -->
<div class="media">

  <a class="pull-left" href="#">
  <!-- <img class="media-object img-circle" src="static/admin/assets/img/user.png"> -->
  <img class="media-object img-circle" src="images/<?php echo $v2['img_path'];?>">
  </a>
  <div class="media-body">
      <h4 class="media-heading"> <?php echo $v2['name'];?> </h4>

      <div class="form-group">
        <label for="">Topic Name:</label>
        <input type="text" class="form-control" name="TopicName" placeholder="Topic Name" style="width:100px;" value="<?php echo $v2['name'];?>" >
      </div>
      <div class="form-group">
        <label for="">Topic Tab Name:</label>
        <input type="text" class="form-control" name="TopicTabName" placeholder="Topic Tab Name" style="width:90px;" value="<?php echo $v2['tab_name'];?>" >
      </div>
      <div class="form-group">
        <label for="">Sort:</label>
        <input type="test" class="form-control" name="sort" placeholder="Sort" style="width:60px;" value="<?php echo $v2['sort'];?>" >
      </div>
      <div class="form-group">
        <label for="">Status:</label>
        <select class="form-control" name="status" value="<?php echo $v2['status'];?>" >
          <option value="0" <?php if($v2['status']==0) echo 'selected';?> >审核状态</option>
          <option value="1" <?php if($v2['status']==1) echo 'selected';?> >通过</option>
          <option value="2" <?php if($v2['status']==2) echo 'selected';?> >拒绝</option>
          <option value="3" <?php if($v2['status']==3) echo 'selected';?> >审核中</option>
          <option value="4" <?php if($v2['status']==4) echo 'selected';?> >删除</option>
        </select>
      </div>
      <button type="button" class="btn btn-success" onclick="xcspaces.SaveTopicsMenu(this,<?php echo $v2['zid'];?>,'<?php echo $this->token_name;?>','<?php echo $this->token_hash;?>')" >Save</button>
      <a class="btn btn-primary" href="/xcspaceAdmin/spaceEdit/<?php echo $v2['zid'];?>" >Edit</a>
      <button type="button" class="btn btn-danger" onclick="xcspaces.DelTopicsMenu(<?php echo $v2['zid'];?>,'<?php echo $this->token_name;?>','<?php echo $this->token_hash;?>')" >Del</button>
  </div>

</div>
<?php endforeach; ?>

<div class="media">

  <a class="pull-left" href="#">
    <img class="media-object img-circle" src="images/xcspaces.png">
  </a>
  <div class="media-body">
    <h4 class="media-heading">Add Children Topic Name </h4>

    <div class="form-group">
      <label for="">Topic Name:</label>
      <input type="text" class="form-control" id="AddTopicName" placeholder="Topic Name" style="width:100px;">
    </div>
    <div class="form-group">
      <label for="">Topic Tab Name:</label>
      <input type="text" class="form-control" id="AddTopicTabName" placeholder="Topic Tab Name" style="width:90px;" >
    </div>
    <div class="form-group">
      <label for="">Sort:</label>
      <input type="test" class="form-control" id="AddSort" placeholder="Sort" style="width:60px;" >
    </div>
    <div class="form-group">
      <label for="">Status:</label>
      <select class="form-control" id="AddStatus" >
        <option value="0" >审核状态</option>
        <option value="1" >通过</option>
        <option value="2" >拒绝</option>
        <option value="3" >审核中</option>
        <option value="4" >删除</option>
      </select>
    </div>
    <button type="button" class="btn btn-default" onclick="xcspaces.AddTopicsMenu(this,'<?php echo $v['fid']['zid'];?>','<?php echo $this->token_name;?>','<?php echo $this->token_hash;?>')" >Add</button>
  </div>
  
</div>

</div>
</li>
<?php endforeach; ?>

<li class="media">

<div class="media">

  <a class="pull-left" href="#">
    <img class="media-object img-circle" src="images/xcspaces.png">
  </a>
  <div class="media-body">
    <h4 class="media-heading">Add Parent Topic Name </h4>

    <div class="form-group">
      <label for="">Topic Name:</label>
      <input type="text" class="form-control" id="AddParentTopicName" placeholder="Topic Name" style="width:100px;">
    </div>
    <div class="form-group">
      <label for="">Topic Tab Name:</label>
      <input type="text" class="form-control" id="AddParentTopicTabName" placeholder="Topic Tab Name" style="width:90px;" >
    </div>
    <div class="form-group">
      <label for="">Sort:</label>
      <input type="test" class="form-control" id="AddParentSort" placeholder="Sort" style="width:60px;" >
    </div>
    <div class="form-group">
      <label for="">Status:</label>
      <select class="form-control" name="AddStatus" id="AddParentStatus" >
        <option value="0" >审核状态</option>
        <option value="1" >通过</option>
        <option value="2" >拒绝</option>
        <option value="3" >审核中</option>
        <option value="4" >删除</option>
      </select>
    </div>
    <button type="button" class="btn btn-default" onclick="xcspaces.AddParentTopicsMenu(this,'<?php echo $this->token_name;?>','<?php echo $this->token_hash;?>')" >Add</button>
  </div>
  
</div>

</li>

<hr />
模板
<hr />

<li class="media">

<a class="pull-left" href="#">
<img class="media-object img-circle" src="images/xcspaces.png">
</a>

<div class="media-body">

<h4 class="media-heading">NODES Name</h4>

  <div class="form-group">
    <label for="exampleInputName2">Topic Name:</label>
    <input type="text" class="form-control" id="exampleInputName2" placeholder="Topic Name">
  </div>
  <div class="form-group">
    <label for="">Sort:</label>
    <input type="test" class="form-control" id="" placeholder="Sort">
  </div>
  <div class="form-group">
    <label for="">Status:</label>
    <select class="form-control" name="status" >
      <option value="0" >审核状态</option>
      <option value="1" >通过</option>
      <option value="2" >拒绝</option>
      <option value="3" >审核中</option>
      <option value="4" >删除</option>
    </select>
  </div>
  <button type="submit" class="btn btn-default">Save</button>

<!-- Nested media object -->
<div class="media">

  <a class="pull-left" href="#">
  <img class="media-object img-circle" src="images/xcspaces.png">
  </a>
  <div class="media-body">
  <h4 class="media-heading">Nulla gravida vitae neque </h4>

  <div class="form-group">
    <label for="exampleInputName2">Topic Name:</label>
    <input type="text" class="form-control" id="exampleInputName2" placeholder="Topic Name">
  </div>
  <div class="form-group">
    <label for="">Sort:</label>
    <input type="test" class="form-control" id="" placeholder="Sort">
  </div>
  <div class="form-group">
    <label for="">Status:</label>
    <select class="form-control" name="status" >
      <option value="0" >审核状态</option>
      <option value="1" >通过</option>
      <option value="2" >拒绝</option>
      <option value="3" >审核中</option>
      <option value="4" >删除</option>
    </select>
  </div>
  <button type="submit" class="btn btn-default">Save</button>
  </div>

</div>

<div class="media">

  <a class="pull-left" href="#">
  <img class="media-object img-circle" src="static/admin/assets/img/user.png">
  </a>
  <div class="media-body">
  <h4 class="media-heading">Add Topic Name </h4>

  <div class="form-group">
    <label for="">Topic Name:</label>
    <input type="text" class="form-control" id="" placeholder="Topic Name">
  </div>
  <div class="form-group">
    <label for="">Sort:</label>
    <input type="test" class="form-control" id="" placeholder="Sort">
  </div>
  <div class="form-group">
    <label for="">Status:</label>
    <select class="form-control" name="status" >
      <option value="0" >审核状态</option>
      <option value="1" >通过</option>
      <option value="2" >拒绝</option>
      <option value="3" >审核中</option>
      <option value="4" >删除</option>
    </select>
  </div>
  <button type="submit" class="btn btn-default">Add</button>
  </div>
  
</div>

</div>
</li>

</form>

</ul>

      </div>
    </div>
  </div>
</div>

      </div>
      <!-- /. PAGE INNER  -->
  </div>
  <!-- /. PAGE WRAPPER  -->