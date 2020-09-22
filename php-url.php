/**
 * 签名并获取URL结果，json格式返回
 * 
 * 1. 查询弹性IP列表, DescribeAddresses
 * 2. 解绑弹性IP, DisassociateAddress
 * 3. 释放弹性IP, ReleaseAddresses
 * 4. 公网IP转弹性IP, TransformAddress
 * 
 * @param string $req_action : DescribeAddresses, DisassociateAddress, ReleaseAddresses, TransformAddress
 * @param string $params : 以 & 开头， 如 &xxx=yyy
 */
function qcloud_eip_sign($req_action='DescribeAddresses', $req_region='ap-beijing',  $req_extra_params='', $retry_NUM=3) {
    global $QCloud_SecretId;
    global $QCloud_SecretKey;
     
//  $req_action='DescribeAddresses'
//  $req_region = 'ap-beijing';                 // ap-guangzhou
 
    $req_method = 'GET';                            // GET  POST
    $req_api = 'eip.api.qcloud.com/v2/index.php';
    $req_version = '2017-03-12';
    $req_timestamp = strtotime(date('YmdHis')); // 1402992826
    $req_nonce = rand(1000, 1000000);           // 随机正整数
    $req_secretid = $QCloud_SecretId;           // 密钥ID，用作参数
    $req_secretkey = $QCloud_SecretKey;         // 密钥key，用作加密
    $req_signature_method = 'HmacSHA256';       // HmacSHA1(默认), HmacSHA256
    $req_signature = '';
     
//  $req_uri = "https://eip.api.qcloud.com/v2/index.php?Action=DescribeAddresses
//  &Version=2017-03-12
//  &AddressIds.1=eip-hxlqja90
//  &Region=ap-beijing
//  &Timestamp=1402992826
//  &Nonce=345122
//  &Signature=pStJagaKsV2QdkJnBQWYBDByZ9YPBsOi
//  &SecretId=AKIDpY8cxBD2GLGK9sT0LaqIczGLFxTsoDF6
     
    // 请求方法 + 请求主机 +请求路径 + ? + 请求字符串
    $req_params = sprintf("Action=%s&Region=%s&Version=%s&Timestamp=%s&Nonce=%s&SecretId=%s&SignatureMethod=%s%s", $req_action, $req_region, $req_version, $req_timestamp, $req_nonce, $req_secretid, $req_signature_method, $req_extra_params);
     
    $req_params_array = explode("&", $req_params);
    sort($req_params_array);        // 以value排序，value值为 Action=DescribeAddresses 、 Region=ap-beijing
    $req_params2 = implode("&", $req_params_array);
     
    $req_uri = sprintf("%s%s?%s", $req_method, $req_api, $req_params2);
    $req_signature = urlencode(base64_encode(hash_hmac('sha256', $req_uri, $req_secretkey, true)));     // urlencode(xxx)
    $req_url = sprintf("https://%s?%s&Signature=%s", $req_api, $req_params2, $req_signature);
    $res = curl_url($req_url);
     
    $retry_idx = 0;
    while(empty($res) && $retry_idx < $retry_NUM) {
        $retry_idx += 1;
        $res = curl_url($req_url);
    }
     
    if(!empty($res)) {
        $resJson = json_decode($res, true);
        $resJson = $resJson['Response'];
         
        echo sprintf("<br><br> +++++ action : %s <br><br> resJson: ", $req_action);
        print_r($resJson);
         
        return $resJson;
    }
    else {
        return null;
    }
}
 
 
$req_action_query = 'DescribeAddresses';        // 查询弹性IP
$req_action_unbind = 'DisassociateAddress';     // 解绑弹性IP
$req_action_release = 'ReleaseAddresses';       // 释放弹性IP
$req_action_transform = 'TransformAddress';     // 公网IP转弹性IP
 
$req_region = 'ap-guangzhou';
$req_extra_params = '';
 
// 1. 查询弹性IP列表
$resJson = qcloud_eip_sign($req_action_query, $req_region);
var_dump($resJson);
