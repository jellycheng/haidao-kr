<?php

class plugin_price_notice extends plugin
{
    public function detail_price_right()
    {

        $config = cache('price_notice_config', '', 'plugin');
        if (1) {
            $member = model('member/member', 'service')->init();
            $member_id = $member['id'];
            if (!$member_id) {
                $url = url('member/public/login');
                echo "<a href={$url}>降价提醒</a>";
            } else {
                if (defined('MOBILE')) {
                    return "<a id='notice' href='javascript:;'>降价提醒</a><div class=\"notice-div padding-big\"><label>设定您的通知价格：</label><input class=\"input\" type=\"text\" name=\"price\"><br><label for=\"email\">邮箱：</label><input class=\"input\" type=\"email\" id=\"email\" name=\"email\" value=\"{$member['email']}\"><br><label for=\"mobile\">手机号：</label><input class=\"input\" type=\"text\" id=\"mobile\" name=\"mobile\" value=\"{$member['mobile']}\"><br><input type=\"hidden\" name=\"sku_id\"><button id=\"btn_sure\">确定</button></div>

					<script>
						$(function () {
						    $('.notice-div').hide();
							$('input[name=sku_id]').val($('.pro-title').attr('data-skuid'));
							
						});
						$('#notice').click(function() {
						    $('.notice-div').toggle();
						
                            $('input[name=price]').val($('#prom_price').html());
						});
						
						$('#btn_sure').click(function() {
                            $.post('plugin.php?id=price_notice:noticeservlet',
                                {
                                    'price':$('input[name=price]').val(),
                                    'email':$('input[name=email]').val(),
                                    'mobile':$('input[name=mobile]').val(),
                                    'sku_id':$('input[name=sku_id]').val(),
                                    'mid':'{$member['id']}'
                                },function (res) {
                                var data = eval(\"(\"+res+\")\");
                                alert(data.message);
                            });
                            $('.notice-div').hide();
						})
					</script>";
                } else {
                    //$member = model('member/member', 'service')->init();
                    return "<a id='notice' href='javascript:;'>降价提醒</a>
					<script>
						$(function () {
							//$('.promo-price em.text-mix').html()
							$('input[name=sku_id]').val($('h1.text-ellipsis').attr('data-skuid'));
							
						});
						
						$('#notice').click(function() {
                            top.dialog({
                                title: '降价提醒',
                                width: 300,
                                content: '<div class=\"padding-big\">设定您的通知价格<input class=\"input\" type=\"text\" name=\"price\"><br><label for=\"email\">邮箱：</label><input class=\"input\" type=\"email\" id=\"email\" name=\"email\" value=\"{$member['email']}\"><br><label for=\"mobile\">手机号：</label><input class=\"input\" type=\"text\" id=\"mobile\" name=\"mobile\" value=\"{$member['mobile']}\"><br><input type=\"hidden\" name=\"sku_id\"></div>',
                                okValue: '确定',
                                ok: function(){
                                    $.post('plugin.php?id=price_notice:noticeservlet',
                                        {
                                            'price':$('input[name=price]').val(),
                                            'email':$('input[name=email]').val(),
                                            'mobile':$('input[name=mobile]').val(),
                                            'sku_id':$('input[name=sku_id]').val(),
                                            'mid':'{$member['id']}'
                                        },function (res) {
                                        var data = eval(\"(\"+res+\")\");
                                        $.dialogTip({content: data.message});
                                    })
                                }
                            })
                            .showModal();
                            $('input[name=price]').val($('.promo-price em.text-mix').html());
						});
					</script>";
                }

            }
        }
    }

    // 发送提醒信息
    public function send_notice()
    {
        $notice_list = model('price_notice')->where(array('sku_id' => $_GET['sku_id']))->select();

        if (!$notice_list) return false;

        foreach ($notice_list as $notice) {

            if ($_GET['shop_price'] < $notice['price']) {
                $data['type'] = 'email';
                $data['method'] = 'send';
                $url = "http://" . $_SERVER ['HTTP_HOST'] . url('goods/index/detail', array('sku_id' => $notice['sku_id']));
                $params['to'] = $notice['email'];
                $params['subject'] = '降价通知';
                $params['body'] = "您在本商城订阅的<a href='{$url}'>商品</a>已经降价了，请注意查看。";
                $data['params'] = json_encode($params);
                $data['dataline'] = time();
                $data['sort'] = 0;
                model('notify/queue', 'service')->config($data)->send();

//                $data['type'] = 'sms';
//                $data['method'] = 'send';
//                $url = "http://" . $_SERVER ['HTTP_HOST'] . url('goods/index/detail', array('sku_id' => $notice['sku_id']));
//                $params['to'] = $notice['mobile'];
//                $params['subject'] = '降价通知';
//                $params['body'] = "您在本商城订阅的商品已经降价了，请注意查看。";
//                $data['params'] = json_encode($params);
//                $data['dataline'] = time();
//                $data['sort'] = 0;
//                model('notify/queue', 'service')->config($data)->send();

                $data['mobile'] = $notice['mobile'];
                $data['tpl_id'] = '162';
                $data['tpl_vars'] = "您在本商城订阅的商品已经降价了，请注意查看。";
                $params = unit::json_encode($data);
                model('notify/queue', 'service')->add('sms', 'send', $params, 0);

                model('price_notice')->where(array('id' => $notice['id']))->delete();
            }
        }
        return false;
    }
}

