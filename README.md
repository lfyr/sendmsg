使用方法
use Lfyr\SendMsg\Sms;

$accessKeyId='';
$accessKeySecret='';

模板参数
$paramString=json_encode(['username'=>'官方']); 

发送消息参数示例
$param=['phoneNumber'=>'','signName'=>'','templateCode'=>'SMS_162196259','paramString'=>$paramString];  

查询发送记录参数示例
$param=['phoneNumber'=>'','signName'=>'','page'=>1,'pageSize'=>10,'selectDate'=>20190401];

请求发送
$res=$this->smsModel->exec($accessKeyId, $accessKeySecret, $param, 2); 参数1发送 2查询

print_r($res);
