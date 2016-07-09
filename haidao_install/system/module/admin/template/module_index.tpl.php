<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">模块管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a <?php if($_GET['status'] == 1 || !isset($_GET['status'])){?>class="current"<?php }?> href="<?php echo url('index', array('status' => 1))?>">已启用模块</a></li>
				<li><a <?php if($_GET['status'] == 0 && isset($_GET['status'])){?>class="current"<?php }?>href="<?php echo url('index', array('status' => 0))?>">未启用模块</a></li>
				<li><a <?php if($_GET['status'] == -1){?>class="current"<?php }?>href="<?php echo url('index', array('status' => -1))?>">未安装模块</a></li>
			</ul>
			<div class="hr-gray"></div>
		</div>
		<div class="content padding-big have-fixed-nav">
			<div class="tips margin-tb">
				<div class="tips-info border">
					<h6>温馨提示</h6>
					<a id="show-tip" data-open="true" href="javascript:;">关闭操作提示</a>
				</div>
				<div class="tips-txt padding-small-top layout">
					<p>- 添加商品时可选择商品分类，用户可根据分类查询商品列表</p>
					<p>- 点击分类名前“+”符号，显示当前分类的下级分类</p>
					<p>- 对分类作任何更改后，都需要到 设置 -> 清理缓存 清理商品分类，新的设置才会生效</p>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table resize-table paging-table high-table border margin-top clearfix">
				<div class="tr">
					<span class="th" data-width="85">
						<span class="td-con">模块名称</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach ($lists as $identifier => $module): ?>
				<div class="tr" data-id="<?php echo $module['branch_id']?>">
					<div class="td">
						<div class="td-con td-pic text-left">
							<span class="pic"><img src="<?php echo $module['icon']?>" /></span>
							<span class="title text-ellipsis txt"><?php echo $module['name'] ?>（版本号：v<?php echo $module['version'];?>）&emsp;&emsp;&emsp;&emsp;<em class="text-main new_update"></em></span>
							<span class="icon">
								<em class="text-main">简介：</em><?php echo $module['description']; ?>
							</span>
						</div>
					</div>
					<div class="td">
						<span class="td-con double-row">
							<a href="<?php echo url('available',array('identifier' => $module['identifier']))?>"><?php if($_GET['status'] == 1 || !isset($_GET['status'])){?>禁用<?php }?><?php if($_GET['status'] == 0 && isset($_GET['status'])){?>开启<?php }?></a> |
							<?php if($_GET['status'] == -1){?>
							<a href="<?php echo url('install',array('identifier' => $module['identifier']))?>">安装</a>
							<?php }else{?>
							<a href="<?php echo url('upgrade',array('identifier' => $module['identifier']))?>">更新</a>
							<?php }?>
						</span>
					</div>
				</div>
				<?php endforeach ?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $pages; ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>
			$('.table').resizableColumns();
			$('.paging-table').fixedPaging();
			/*$(function(){
				$.get('<?php echo url("ajax_upgrade")?>',{},function(ret){
					$.each(ret.result,function(i,item){
						$('.tr[data-id='+item.branch_id+']').find('.new_update').html('发现新版本'+item.new_version);
					})
				},'json');
			})*/
		</script>
	</body>
</html>
