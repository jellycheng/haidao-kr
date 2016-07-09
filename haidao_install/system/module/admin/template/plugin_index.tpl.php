<?php include template('header','admin');?>
		<div class="fixed-nav layout">
			<ul>
				<li class="first">插件管理<a id="addHome" title="添加到首页快捷菜单">[+]</a></li>
				<li class="spacer-gray"></li>
				<li><a <?php if($_GET['status'] == 1 || !isset($_GET['status'])){?>class="current"<?php }?> href="<?php echo url('index',array('status' => 1))?>">已启用插件</a></li>
				<li><a <?php if($_GET['status'] == 0 && isset($_GET['status'])){?>class="current"<?php }?> href="<?php echo url('index',array('status' => 0))?>">未启用插件</a></li>
				<li><a <?php if($_GET['status'] == -1){?>class="current"<?php }?> href="<?php echo url('index',array('status' => -1))?>">未安装插件</a></li>
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
			<div class="table-work border margin-tb">
				<div class="border border-white tw-wrap">
					<a href="javascript:;" id="ajax_upgrade"><i class="ico_in"></i>获取最新版本</a>
					<div class="spacer-gray"></div>
				</div>
			</div>
			<div class="hr-gray"></div>
			<div class="table resize-table paging-table high-table border margin-top clearfix">
				<div class="tr">
					<span class="th" data-width="50">
						<span class="td-con">插件名称</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">版本</span>
					</span>
					<span class="th" data-width="20">
						<span class="td-con">服务商</span>
					</span>
					<span class="th" data-width="15">
						<span class="td-con">操作</span>
					</span>
				</div>
				<?php foreach ($lists AS $plugin):?>
				<div class="tr" data-id="<?php echo $plugin['branch_id']?>">
					<div class="td">
						<div class="td-con td-pic text-left">
							<span class="pic"><img src="<?php echo file_exists('./system/plugin/'.$plugin['identifier'].'/icon.png') ? './system/plugin/'.$plugin['identifier'].'/icon.png' : './statics/images/default_no_upload.png'?>" /></span>
							<span class="title text-ellipsis txt"><?php echo $plugin['name']?></span>
							<span class="icon">
								<em class="text-main">简介：</em><?php echo $plugin['description']?>
							</span>
						</div>
					</div>
					<div class="td">
						<span class="td-con"><?php echo $plugin['version']?>&emsp;&emsp;&emsp;&emsp;<em class="text-main new_update"></em></span>
					</div>
					<div class="td">
						<span class="td-con"><?php echo $plugin['copyright']?></span>
					</div>
					<div class="td">
						<span class="td-con double-row">
							<?php if($_GET['status'] == 1 || !isset($_GET['status'])){?><a href="<?php echo url('module',array('mod' => $plugin['identifier']))?>">管理</a><br /><?php }?>
							<?php if($_GET['status'] == 1 || !isset($_GET['status'])){?><a href="<?php echo url('available',array('identifier' => $plugin['identifier']))?>">关闭</a><?php }?><?php if($_GET['status'] == 0 && isset($_GET['status'])){?><a href="<?php echo url('available',array('identifier' => $plugin['identifier']))?>">开启</a><?php }?><?php if($_GET['status'] == -1){?><a href="<?php echo url('install',array('identifier' => $plugin['identifier']))?>">安装</a><?php }?>&nbsp;&nbsp;&nbsp;<a href="<?php echo url('upgrade',array('identifier' => $plugin['identifier']))?>">更新</a><?php if($_GET['status'] == 1 || !isset($_GET['status']) || ($_GET['status'] == 0 && isset($_GET['status']))){?>&nbsp;&nbsp;&nbsp;<a data-confirm="卸载将清空该插件的所有配置信息，是否确认卸载？" href="<?php echo url('uninstall',array('identifier' => $plugin['identifier']))?>">卸载</a><?php }?>
						</span>
					</div>
				</div>
				<?php endforeach?>
				<div class="paging padding-tb body-bg clearfix">
					<?php echo $pages?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<script>
			$('.table').resizableColumns();
			$('.paging-table').fixedPaging();
			$(function(){
				ajax_upgrade(0);
				$('#ajax_upgrade').bind('click',function(){
					ajax_upgrade(1);
				})
			})
			function ajax_upgrade(flag){
				$.get('<?php echo url("ajax_upgrade")?>',{flag : flag},function(ret){
					$.each(ret.result,function(i,item){
						if(item.new_version){
							$('.tr[data-id='+item.branch_id+']').find('.new_update').html('发现新版本'+item.new_version);
						}
					})
				},'json');

			}

		

		</script>
	</body>
</html>
