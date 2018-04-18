<?php


class debugController extends ProjectController{

    private $uin = '';
    
    /*
     * qq数组
    */
    private $safeQQArr = array(
            '3258781056',
            '2447007665',
            '2058283515',
            '418326187',
            '936832595',
            '452181556',
            '574414762',
            '53552758',
            '370801139',
            '1104700543',
            '570860524',
            '2316271184',
            '1461304340',
            '412938038',
    		'1024557205'
    );
    
    /*
     * 构造方法，检查qq登陆
    */
    public function __construct()
    {
        if(!TMAuthUtils::isLogin())
        {
            throw new TMException('请先登陆');
        }
        $this->uin  = TMAuthUtils::getUin();
        if(!in_array(($this->uin), $this->safeQQArr)){
            throw new TMException('你没有权限访问该页面');
        }
    }

    /**
     * 获取项目域名
     * @return string 域名
     */
    public static function getDomainUrl(){
    	$url = 'sh.act.qq.com/631007340';
    	$domain = TMConfig::get("domain");
    	if (substr($domain, 0,3) == "shf"){
    		$url = "shf.act.qq.com";
    	}else{
    		
    	}
    	return $url;
    
    }
    
    /**
     * 设置用户投票标志
     * @param openid
     * @param desid 被投票作品的ID
     */
    public function setUserCounterServiceAction(){
    	echo "<h3 align='center'>设置用户投票标志</h3>";
    	$openid = OMHttpRequest::getGetParameter('openid');
    	$FDesId = OMHttpRequest::getGetParameter('desid');
    	
    	if(strlen($openid)>0&&strlen($FDesId)>0){
    		$rest = CommonUtil::setUserCounterService($openid.$FDesId);
    		var_dump($rest);
    	}else{
    		print_r("参数错误<br>");
    		print_r("正确的URL格式是：<br>");
            $domain = self::getDomainUrl();
            print_r("http://".$domain."/debug/setUserCounterService?openid=&desid=");
    		exit();
    	}
    }
  
    
    /**
     * 查询计数服务用户投票标志
     * @param openid
     * @param desid 被投票作品的ID
     */
    public function getUserCounterServiceAction(){
    	echo "<h3 align='center'>查询计数服务用户投票标志</h3>";
    	$openid = OMHttpRequest::getGetParameter('openid');
    	$FDesId = OMHttpRequest::getGetParameter('desid');
    	 
    	if(strlen($openid)>0&&strlen($FDesId)>0){
    		$rest = CommonUtil::getUserCounterService($openid.$FDesId);
    		var_dump($rest);
    	}else{
    		print_r("参数错误<br>");
    		print_r("正确的URL格式是：<br>");
    		$domain = self::getDomainUrl();
    		print_r("http://".$domain."/debug/getUserCounterService?openid=&desid=");
    		exit();
    	}
    }

    /**
     * 设置节目状态
     * @param estat 
     */
    public function setProgramStatusAction(){
    	echo "<h3 align='center'>设置节目状态</h3>";
    	$estat = OMHttpRequest::getGetParameter('estat');
    	 
    	if(strlen($estat)>0){
    		$rest = CommonUtil::setProgramStatusCounter($estat);
    		var_dump($rest);
    	}else{
    		print_r("参数错误<br>");
    		print_r("正确的URL格式是：<br>");
    		$domain = self::getDomainUrl();
    		print_r("http://".$domain."/debug/setProgramStatus?estat=");
    		exit();
    	}
    }

    
    /**
     * 获取节目状态
     * @param estat
     */
    public function getProgramStatusAction(){
    	echo "<h3 align='center'>获取节目状态</h3>";
    	$estat = OMHttpRequest::getGetParameter('estat');
    
    	if(strlen($estat)>0){
    		$rest = CommonUtil::getProgramStatusCounter($estat);
    		var_dump($rest);
    	}else{
    		print_r("参数错误<br>");
    		print_r("正确的URL格式是：<br>");
    		$domain = self::getDomainUrl();
    		print_r("http://".$domain."/debug/getProgramStatus?estat=");
    		exit();
    	}
    }
    
    
    /**
     * 设置用户猜中的数量
     * @param openid
     * @param nums
     */
    public function setGuessNumAction(){
    	echo "<h3 align='center'>设置用户猜中的数量</h3>";
    	$openid = OMHttpRequest::getGetParameter('openid');
    	$nums = OMHttpRequest::getGetParameter('nums');
    
    	if(strlen($openid)>0){
    		$rest = CommonUtil::setGuessNum($openid,$nums);
    		var_dump($rest);
    	}else{
    		print_r("参数错误<br>");
    		print_r("正确的URL格式是：<br>");
    		$domain = self::getDomainUrl();
    		print_r("http://".$domain."/debug/setGuessNum?openid=&nums=");
    		exit();
    	}
    }
    
    /**
     * 获取用户盲选的猜中的数量
     * @param openid
     * @param nums
     */
    public function setGuessNumAction(){
    	echo "<h3 align='center'>获取用户盲选的猜中的数量</h3>";
    	$openid = OMHttpRequest::getGetParameter('openid');

    	if(strlen($openid)>0){
    		$rest = CommonUtil::getGuessNum($openid);
    		var_dump($rest);
    	}else{
    		print_r("参数错误<br>");
    		print_r("正确的URL格式是：<br>");
    		$domain = self::getDomainUrl();
    		print_r("http://".$domain."/debug/getGuessNum?openid=");
    		exit();
    	}
    }
}