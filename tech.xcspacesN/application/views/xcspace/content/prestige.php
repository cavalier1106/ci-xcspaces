
<?php include($path . "/topicNodes.php"); ?>
<?php include($path . "/set-webinfo-top.php"); ?>

<div class="wrap">
	<div class="container mt20">
		<div class="big-intro">
			<h1 class="h3">威望与权限</h1>
			<p style="font-size:16px;">
				xcSpaces 是个开放的社区，所有人都能参与到社区的管理中来
			</p>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h3 class="mt0 h4">威望规则</h3>
				<table class="table table-bordered table-hover">
				<thead>
				<tr class="active">
					<th>
						条件
					</th>
					<th class="text-center" width="20%">
						得分 / 扣分
					</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>
						发布新文章
					</td>
					<td class="text-center">
						+10
					</td>
				</tr>
				<tr>
					<td>
						评论话题
					</td>
					<td class="text-center">
						+5
					</td>
				</tr>
				<tr>
					<td>
						问题被收藏
					</td>
					<td class="text-center">
						+1
					</td>
				</tr>
				<tr>
					<td>
						感谢评论
					</td>
					<td class="text-center">
						-2
					</td>
				</tr>
				<tr>
					<td>
						感谢话题
					</td>
					<td class="text-center">
						-5
					</td>
				</tr>
				<!-- <tr>
					<td>
						评论被忽略
					</td>
					<td class="text-center">
						-5
					</td>
				</tr> -->
				<tr>
					<td>
						话题被删除（包括自己删除）
					</td>
					<td class="text-center">
						-5
					</td>
				</tr>
				<tr>
					<td>
						评论被删除
					</td>
					<td class="text-center">
						-5
					</td>
				</tr>
				</tbody>
				</table>
			</div>
			<div class="col-md-12">
				<h3 class="mt0 h4">空间权限</h3>
				<table class="table table-bordered table-hover">
				<thead>
				<tr class="active">
					<th>
						操作
					</th>
					<th class="text-center" width="20%">
						威望要求
					</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>
						删除话题
					</td>
					<td class="text-center">
						5000
					</td>
				</tr>
				<tr>
					<td>
						删除笔记
					</td>
					<td class="text-center">
						5000
					</td>
				</tr>
				<tr>
					<td>
						编辑话题
					</td>
					<td class="text-center">
						5000
					</td>
				</tr>
				<tr>
					<td>
						编辑笔记
					</td>
					<td class="text-center">
						5000
					</td>
				</tr>
				</tbody>
				</table>
				<p class="text-warning">
					* 部分子站点威望规则不同，以上规则仅针对 xcSpaces 主站点
				</p>
			</div>
		</div>
	</div>
</div>