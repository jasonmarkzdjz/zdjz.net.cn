<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo TMConfig::get('base_url'); ?>" />
<title>您访问的页面不存在</title>
</head>
<script>
function  heightSet(thisFrame){
    if($.browser.mozilla || $.browser.msie){
        bodyHeight =window.frames["mainFrame"].document.body.scrollHeight;
    }else{
        bodyHeight =thisFrame.contentWindow.document.documentElement.scrollHeight;
        //这行可代替上一行，这样heightSet函数的参数可以省略了
        //bodyHeight = document.getElementById("thisFrameId").contentWindow.document.documentElement.scrollHeight;
    }
     document.getElementById("mainFrame").height=bodyHeight;
}
document.domain = "qq.com";
</script>
</head>
<body>

</body>
</html>
