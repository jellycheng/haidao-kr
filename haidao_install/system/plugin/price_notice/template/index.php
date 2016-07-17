<?php include template('header', 'admin'); ?>
<div class="fixed-nav layout">
    <ul>
        <li class="first">插件配置</li>
        <li class="spacer-gray"></li>
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
            <p>- 订阅商品，价格下降自动发送通知。</p>

        </div>
    </div>
    <div class="hr-gray"></div>
    <form action="" method="post" enctype="multipart/form-data">

        <?php //echo form::input('radio', 'is_login', '1', '是否需要登录：', '是否需要登录。', array('items' => array(1 => '需要', 2 => '不需要'), 'colspan' => '2')); ?>

        <h2 class="text-center">降价提醒列表</h2>

        <div class="table border clearfix">
            <div class="tr">
                <div class="th w25">电话</div>
                <div class="th w25">邮件</div>
                <div class="th w25">会员ID</div>
                <div class="th w25">订单ID</div>
            </div>
            <div class="tr">
                <?php foreach($notice_list as $notice) {?>
                <div class="td w25 border-bottom"><? echo $notice['mobile']?></div>
                <div class="td w25  border-bottom"><?php echo $notice['email']?></div>
                <div class="td w25  border-bottom"><?php echo $notice['mid']?></div>
                <div class="td w25  border-bottom"><?php echo $notice['sku_id']?></div>
                <?php }?>
            </div>
        </div>
        <?php //echo form::input('radio', 'auto_order', '1', '是否可选自动下单：', '是否可选自动下单。', array('items' => array(1 => '可选', 2 => '不可选'), 'colspan' => '2')); ?>

<!--        <div class="form-group">-->
<!--            <input type="submit" class="button bg-main" value="确定"/>-->
<!--            <a class="button margin-left bg-gray" href="">返回</a>-->
<!--        </div>-->
    </form>
</div>
</body>
</html>

