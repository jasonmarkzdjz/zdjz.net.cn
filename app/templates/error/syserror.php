<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo TMConfig::get('base_url'); ?>" />
<title>访问异常</title>
<script
	src="http://appmedia.qq.com/media/tae/jquery/jquery.1.7.2.min.js"></script>
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
<?php
$errorMsg = empty($errorMsg) ? '您当前访问的页面网络繁忙' : $errorMsg;
?>
<iframe id="mainFrame" name="mainFrame" frameborder="0" scrolling="no"
	src="http://appmedia.qq.com/media/tae/sdk/html/error_sys.html?error=<?php echo $errorMsg;?>&index_page=<?php echo urlencode(TMConfig::get('base_url'));?>"
	onload="heightSet(this)" width="100%" height="0"></iframe>
</body>
</html>
