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
            <p>- 通过SAAS云验证机制，为系统增加滑动验证码，杜绝注册机恶意注册。</p>
        </div>
    </div>
    <div class="hr-gray"></div>
    <form action="" method="post">
        <?php echo Form::input('text', 'id', $config['id'], 'ID：', '极验验证是基于SAAS的云应用，此公钥为本站验证对应的唯一标识。') ?>
        <?php echo Form::input('text', 'key', $config['key'], 'KEY：', '对应其相应公钥，类似于密码，请勿泄露。') ?>

        <?php echo form::input('checkbox', 'position[]', $config['position'], '使用场景：', '请选择需要验证的场景。', array('items' => array('login' => '登录', 'reg' => '注册'), 'colspan' => '2')); ?>

        <?php echo form::input('radio', 'aspect', $config['aspect'], '验证形式：', '请选择极验形式。', array('items' => array('float' => '浮动式', 'embed' => '嵌入式', 'pop' => '弹出式'), 'colspan' => '3')); ?>

        <?php //echo form::input('radio', 'mobile', $config['mobile'], '是否支持移动端：', '是否支持移动端。', array('items' => array('1' => '支持', '2' => '不支持'), 'colspan' => '2')); ?>

        <div class="form-group">
            <input type="submit" class="button bg-main" value="确定"/>
            <a class="button margin-left bg-gray" href="">返回</a>
        </div>
    </form>
</div>
