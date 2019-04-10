<?php
namespace Lfyr\SendMsg;
date_default_timezone_set("GMT"); 
class Sms
{
    protected $host = "https://dysmsapi.aliyuncs.com/?";
    
    /**
    * 发送
    * User:lfyr
    * 2019/4/1 14:57
    * @param str $url
    * @param str $headers
    * @return Result
    */
    public function exec($accessKeyId, $accessKeySecret, $param=array(), $type)
    {  
        if(empty($accessKeyId) || empty($accessKeySecret) || empty($param) || empty($type)){
            return ['Code'=>'400','Message'=>'请检查参数'];
        }

        $target = $this->host;  

        if($type==1){
            $data=$this->getSendList($param);
            if(isset($data['Code'])==400){
                return $data;
            }
        }else{
            $data=$this->getSendList($param);
            if(isset($data['Code'])==400){
                return $data;
            }
        }
      
        $data['AccessKeyId'] = $accessKeyId;  
        // 计算签名
        $data['Signature'] = $this->computeSignature($data, $accessKeySecret);  

        // 发送请求  
        $result = json_decode($this->https_request($target.http_build_query($data)), true);  
        return $result;
    }

    /**
    * 发送参数构造
    * User:lfyr
    * 2019/4/1 14:57
    * @param array $param
    * @return Result
    */
    public function sendCode($param=array())
    {   
        if(empty($param['phoneNumber']) || empty($param['signName']) || empty($param['templateCode']) || empty($param['paramString'])){
            return ['Code'=>'400','Message'=>'请检查参数'];
        }

        return $data = array(  
                    'Action' => 'SendSms',
                    'Format' => 'JSON', 
                    'PhoneNumbers' => $param['phoneNumber'],
                    'RegionId' => 'cn-hangzhou',
                    'SignName'=> $param['signName'],
                    'SignatureMethod' => 'HMAC-SHA1',
                    'SignatureNonce'=> uniqid(),
                    'SignatureVersion' => '1.0',
                    'TemplateCode' => $param['templateCode'], 
                    'TemplateParam' => $param['paramString'],
                    'Timestamp' =>  date('Y-m-d\TH:i:s\Z'),
                    'Version' => '2017-05-25'
                );  
    }

    /**
    * 查询参数构造
    * User:lfyr
    * 2019/4/1 14:57
    * @param array $param
    * @return Result
    */
    public function getSendList($param=array())
    {   
        if(empty($param['phoneNumber']) || empty($param['page']) || empty($param['pageSize']) || empty($param['selectDate'])){
            return ['Code'=>'400','Message'=>'请检查参数'];
        }
         
        return  $data = array(  
                    'Action' => 'QuerySendDetails',
                    'Format' => 'JSON', 
                    'PhoneNumber' => $param['phoneNumber'],
                    'CurrentPage' => $param['page'],
                    'PageSize' => $param['pageSize'],
                    'SendDate' => $param['selectDate'],
                    'RegionId' => 'cn-hangzhou',
                    'SignatureMethod' => 'HMAC-SHA1',
                    'SignatureNonce'=> uniqid(),
                    'SignatureVersion' => '1.0',
                    'Timestamp' =>  date('Y-m-d\TH:i:s\Z'),
                    'Version' => '2017-05-25'
                );  
    }

    /**
    * 处理请求参数
    * User:lfyr
    * 2019/4/1 14:57
    * @param str $str
    * @return Result
    */
    public function percentEncode($str)  
    {  
        // 使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范  
        $res = urlencode($str);  
        $res = preg_replace('/\+/', '%20', $res);  
        $res = preg_replace('/\*/', '%2A', $res);  
        $res = preg_replace('/%7E/', '~', $res);  
        return $res;  
    }  

    /**
    * 签名
    * User:lfyr
    * 2019/4/1 14:57
    * @param str $parameters
    * @param str $accessKeySecret
    * @return Result
    */
    public function computeSignature($parameters, $accessKeySecret)  
    {  
        // 将参数Key按字典顺序排序  
        ksort($parameters);  
        // 生成规范化请求字符串  
        $canonicalizedQueryString = '';  
        foreach($parameters as $key => $value)  
        {  
        $canonicalizedQueryString .= '&' . $this->percentEncode($key)  
            . '=' . $this->percentEncode($value);  
        }  
        // 生成用于计算签名的字符串 stringToSign  
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));  
        //echo "<br>".$stringToSign."<br>";  
        // 计算签名，注意accessKeySecret后面要加上字符'&'  
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));  
        return $signature;  
    }  

    /**
    * 发送请求
    * User:lfyr
    * 2019/4/1 14:57
    * @param str $str
    * @return Result
    */
    public function https_request($url)  
    {  
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        $data = curl_exec($curl);  
        if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}  
        curl_close($curl);  
        return $data;  
    }  
}
