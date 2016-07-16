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
            <p>- 通过连接UCenter，让商城会员与UCenter进行数据通信，实现与其他系统进行会员数据同步。</p>
        </div>
    </div>
    <div class="hr-gray"></div>
    <form class="addfrom" name="form1" id="form1" action="" method="post">
        <?php if (!$config) : ?>
            <?php echo Form::input('text', 'ucapi', '', 'UCenter 的 URL：', 'UCenter 的 URL。'); ?>
            <?php echo Form::input('text', 'ucfounderpw', '', 'UCenter 创始人密码：', 'UCenter 创始人密码。'); ?>

        <?php else: ?>
            <?php echo Form::input('text', 'uc_id', $config['uc_id'], 'UCenter 应用 ID：', '该值为当前商店在 UCenter 的应用 ID，一般情况请不要改动。'); ?>
            <?php echo Form::input('text', 'uc_key', $config['uc_key'], 'UCenter 通信密钥：', '通信密钥用于在 UCenter 和 Haidao 之间传输信息的加密，请设置完全相同的通讯密钥，以确保两套系统能够正常通信。'); ?>

            <?php echo Form::input('text', 'uc_url', $config['uc_url'], 'UCenter 访问地址：', '该值在您安装完 UCenter 后会被初始化，在您 UCenter 地址或者目录改变的情况下，修改此项，一般情况请不要改动。'); ?>

            <?php echo Form::input('text', 'uc_ip', $config['uc_ip'], 'UCenter IP 地址：', '如果您的服务器无法通过域名访问 UCenter，可以输入 UCenter 服务器的 IP 地址。'); ?>

            <?php echo Form::input('radio', 'uc_connect', $config['uc_connect'], 'UCenter 连接方式：', '请根据您的服务器网络环境选择适当的连接方式。', array('items' => array('mysql' => '数据库链接方式', 'post' => '接口方式'), 'colspan' => 2)); ?>

            <div class="mysql">
                <?php echo Form::input('text', 'db_host', $config['db_host'], 'UCenter 数据库服务器：', '可以是本地也可以是远程数据库服务器。'); ?>
                <?php echo Form::input('text', 'db_user', $config['db_user'], 'UCenter 数据库用户名：', 'UCenter 数据库用户名。'); ?>
                <?php echo Form::input('text', 'db_name', $config['db_name'], 'UCenter 数据库名：', 'UCenter 数据库名。'); ?>
                <?php echo Form::input('password', 'db_pass', $config['db_pass'], 'UCenter 访问地址UCenter 数据库密码：', 'UCenter 数据库密码。'); ?>
                <?php echo Form::input('text', 'db_pre', $config['db_pre'], 'UCenter 表前缀：', 'UCenter 表前缀。'); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <input type="submit" class="button bg-main" value="确定"/>
            <a class="button margin-left bg-gray" href="">返回</a>
        </div>
    </form>
</div>
<script>
    $(function () {
        $('[name=uc_connect]').change(function () {
            if(this.value == 'post'){
                $('.mysql').hide();
            }else{
                $('.mysql').show();
            }
        })
    })
</script>