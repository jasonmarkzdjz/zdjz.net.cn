<?php
/**
 * Created by PhpStorm.
 * User: fulan
 * Date: 2016/4/27
 * Time: 15:32
 */
class WxPayApiResults extends WxPayApiBase
{
    /**
     *
     * 检测签名
     */
    public function CheckSign()
    {
        //fix异常
        if(!$this->IsSignSet()){
            return false;
        }

        $sign = $this->MakeSign();
        if($this->GetSign() == $sign){
            return true;
        }
        return false;
    }

    /**
     *
     * 使用数组初始化
     * @param array $array
     */
    public function FromArray($array)
    {
        $this->values = $array;
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function arrayToXml()
    {
        $xml = "<xml>";
        foreach ($this->values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public  function FromXml($xml){
        //将XML转为array 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     *
     * 使用数组初始化对象
     * @param array $array
     */
    public static function InitFromArray($array, $noCheckSign = false)
    {
        $obj = new self();
        $obj->FromArray($array);
        if($noCheckSign == false){
            $obj->CheckSign();
        }
        return $obj;
    }

    /**
     *
     * 设置参数
     * @param string $key
     * @param string $value
     */
    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * 将xml转为array
     * @param string $xml
     */
    public static function Init($xml)
    {
        $obj = new self();
        $obj->FromXml($xml);
        if($obj->values['return_code'] != 'SUCCESS'){
            return $obj->GetValues();
        }
        $obj->CheckSign();
        return $obj->GetValues();
    }
}