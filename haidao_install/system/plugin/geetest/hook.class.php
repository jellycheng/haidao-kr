<?php

class plugin_geetest extends plugin
{
    // 登录验证
    public function before_login_btn()
    {
        $plugins = cache('plugins');
        $plugins = $plugins[$_GET['mod']];
        $config = cache('geetest_config', '', 'plugin');
        if (in_array('login', $config['position'])) {
            // 登录验证开启
            // return $config;
            $html = "";
            if ($config['aspect'] == 'pop') {
                $html = "<div id=\"popup-captcha\"></div>
                            <script src=\"http://static.geetest.com/static/tools/gt.js\"></script>
                            <script>
                                var handlerPopup = function (captchaObj) {
                                    $(\"[name=dosubmit]\").click(function () {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) {
                                            alert('请先完成验证！');
                                            return false;
                                        }
                                    });
                                    captchaObj.bindOn(\"[name=dosubmit]\");
                                    captchaObj.appendTo(\"#popup-captcha\");
                                };
                                $.ajax({
                                    url: \"plugin.php?id=geetest:captchaservlet\",
                                    type: \"get\",
                                    dataType: \"json\",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            product: \"popup\", 
                                            offline: !data.success 
                                        }, handlerPopup);
                                    }
                                });
                            </script>";

            } elseif ($config['aspect'] == 'embed') {
                $html = "<style>.login-box{width: 360px;}#embed-captcha{padding-top: 20px;}</style>
                            <script src=\"http://static.geetest.com/static/tools/gt.js\"></script>
                            <div id=\"embed-captcha\"></div>
                            <p id=\"wait\" class=\"show\"></p>
                            <script>
                                var handlerEmbed = function (captchaObj) {
                                    $(\"[name=dosubmit]\").click(function (e) {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) {
                                            alert('请先完成验证。');
                                            return false;
                                            $(\"#notice\")[0].className = \"show\";
                                            setTimeout(function () {
                                                $(\"#notice\")[0].className = \"hide\";
                                            }, 2000);
                                            e.preventDefault();
    
                                        }
                                    });
                                    captchaObj.appendTo(\"#embed-captcha\");
                                    captchaObj.onReady(function () {
                                        $(\"#wait\")[0].className = \"hide\";
                                    });
                                };
                                $.ajax({
                                    url: \"plugin.php?id=geetest:captchaservlet\",
                                    type: \"get\",
                                    dataType: \"json\",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            product: \"embed\", 
                                            offline: !data.success 
                                        }, handlerEmbed);
                                    }
                                });
                            </script>";
            } elseif ($config['aspect'] == 'float') {
                $html = "<style>.login-box{width: 360px;}#embed-captcha{padding-top: 20px;}</style>
                            <script src=\"http://static.geetest.com/static/tools/gt.js\"></script>
                            <div id=\"embed-captcha\"></div>
                            <p id=\"wait\" class=\"show\"></p>
    
                            <script>
                                var handlerNormal = function (captchaObj) {
                                    $(\"[name=dosubmit]\").click(function (e) {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) {
                                            alert('请先完成验证。');
                                            return false;
                                            $(\"#notice\")[0].className = \"show\";
                                            setTimeout(function () {
                                                $(\"#notice\")[0].className = \"hide\";
                                            }, 2000);
                                            e.preventDefault();
    
                                        }
                                    });
                                    captchaObj.appendTo(\"#embed-captcha\");
                                    captchaObj.onReady(function () {
                                        $(\"#wait\")[0].className = \"hide\";
                                    });
                                };
                                $.ajax({
                                    url: \"plugin.php?id=geetest:captchaservlet\",
                                    type: \"get\",
                                    dataType: \"json\",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            product: \"float\", 
                                            offline: !data.success 
                                        }, handlerNormal);
                                    }
                                });
                            </script>";
            }
            return $html;
        } else {
            return false;
        }
    }

    // 注册验证
    public function before_register_btn()
    {
        $plugins = cache('plugins');
        $plugins = $plugins[$_GET['mod']];
        $config = cache('geetest_config', '', 'plugin');
        if (in_array('reg', $config['position'])) {
            // 注册验证开启
            $html = "";
            if ($config['aspect'] == 'pop') {
                $html = "<div id=\"popup-captcha\"></div>
                            <script src=\"http://static.geetest.com/static/tools/gt.js\"></script>
                            <script>
                                var handlerPopup = function (captchaObj) {
                                    $(\"[name=dosubmit]\").click(function () {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) {
                                            alert('请先完成验证！');
                                            return false;
                                        }
                                    });
                                    captchaObj.bindOn(\"[name=dosubmit]\");
                                    captchaObj.appendTo(\"#popup-captcha\");
                                };
                                $.ajax({
                                    url: \"plugin.php?id=geetest:captchaservlet\",
                                    type: \"get\",
                                    dataType: \"json\",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            product: \"popup\", 
                                            offline: !data.success 
                                        }, handlerPopup);
                                    }
                                });
                            </script>";

            } elseif ($config['aspect'] == 'embed') {
                $html = "<style>.login-box{width: 360px;}#embed-captcha{padding-top: 20px;}</style>
                            <script src=\"http://static.geetest.com/static/tools/gt.js\"></script>
                            <div id=\"embed-captcha\"></div>
                            <p id=\"wait\" class=\"show\"></p>
                            <script>
                                var handlerEmbed = function (captchaObj) {
                                    $(\"[name=dosubmit]\").click(function (e) {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) {
                                            alert('请先完成验证。');
                                            return false;
                                            $(\"#notice\")[0].className = \"show\";
                                            setTimeout(function () {
                                                $(\"#notice\")[0].className = \"hide\";
                                            }, 2000);
                                            e.preventDefault();
    
                                        }
                                    });
                                    captchaObj.appendTo(\"#embed-captcha\");
                                    captchaObj.onReady(function () {
                                        $(\"#wait\")[0].className = \"hide\";
                                    });
                                };
                                $.ajax({
                                    url: \"plugin.php?id=geetest:captchaservlet\",
                                    type: \"get\",
                                    dataType: \"json\",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            product: \"embed\", 
                                            offline: !data.success 
                                        }, handlerEmbed);
                                    }
                                });
                            </script>";
            } elseif ($config['aspect'] == 'float') {
                $html = "<script src=\"http://static.geetest.com/static/tools/gt.js\"></script>
                            <div style='padding-left: 90px' class=\"list margin-big-top\">
                            <div id=\"embed-captcha\"></div>
                            <p id=\"wait\" class=\"show\"></p>
                            </div>
                            <script>
                                var handlerNormal = function (captchaObj) {
                                    $(\"[name=dosubmit]\").click(function (e) {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) {
                                            alert('请先完成验证。');
                                            return false;
                                            $(\"#notice\")[0].className = \"show\";
                                            setTimeout(function () {
                                                $(\"#notice\")[0].className = \"hide\";
                                            }, 2000);
                                            e.preventDefault();
    
                                        }
                                    });
                                    captchaObj.appendTo(\"#embed-captcha\");
                                    captchaObj.onReady(function () {
                                        $(\"#wait\")[0].className = \"hide\";
                                    });
                                };
                                $.ajax({
                                    url: \"plugin.php?id=geetest:captchaservlet\",
                                    type: \"get\",
                                    dataType: \"json\",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            product: \"float\", 
                                            offline: !data.success 
                                        }, handlerNormal);
                                    }
                                });
                            </script>";
            }
            return $html;
        } else {
            return false;
        }
    }
}