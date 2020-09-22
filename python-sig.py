#!/usr/bin/env python
# -*- coding:utf-8 -*-
#
# mimvp.com
# 2018-01-08
 
 
import time, datetime, os, json
import urllib, urllib2
import hashlib, base64, hmac, random
import logging
import logging.handlers
 
import sys
reload(sys)
sys.setdefaultencoding('utf-8')
 
 
## 腾讯云API接口签名
def qcloud_eip_sign(req_action='DescribeAddresses', req_region='ap-beijing',  req_extra_params='', retry_NUM=3):
    req_method = 'GET'                              # GET  POST
    req_api = 'eip.api.qcloud.com/v2/index.php'
    req_version = '2017-03-12'
    req_timestamp = int(time.time())                # 1520422452
    req_nonce = random.randint(1000, 1000000)       # 随机正整数
    req_secretid = QCLOUD_SecretId                  # 密钥ID，用作参数
    req_secretkey = QCLOUD_SecretKey                # 密钥key，用作加密
    req_signature_method = 'HmacSHA256'             # HmacSHA1(默认), HmacSHA256
    req_signature = ''
     
#     req_uri = "https://eip.api.qcloud.com/v2/index.php?Action=DescribeAddresses
#                 &Version=2017-03-12
#                 &AddressIds.1=eip-hxlqja90
#                 &Region=ap-beijing
#                 &Timestamp=1402992826
#                 &Nonce=345122
#                 &Signature=pStJagaKsV2QdkJnBQWYBDByZ9YPBsOi
#                 &SecretId=AKIDpY8cxBD2GLGK9sT0LaqIczGLFxTsoDF6
 
    # 请求方法 + 请求主机 +请求路径 + ? + 请求字符串
    req_params = "Action=%s&Region=%s&Version=%s&Timestamp=%s&Nonce=%s&SecretId=%s&SignatureMethod=%s%s" % (req_action, req_region, req_version, req_timestamp, req_nonce, req_secretid, req_signature_method, req_extra_params)
     
    req_params_array = req_params.split('&')
    req_params_array = sorted(req_params_array)          # 以value排序，value值为 Action=DescribeAddresses 、 Region=ap-beijing
    req_params2 = '&'.join(req_params_array);
    req_uri = "%s%s?%s" % (req_method, req_api, req_params2)
    req_signature = urllib.quote( base64.b64encode(hmac.new(req_secretkey, req_uri, digestmod=hashlib.sha256).digest()) )   # urllib.quote(xxx)
 
    req_url = "https://%s?%s&Signature=%s" % (req_api, req_params2, req_signature)
    logger.info('qcloud_eip_sign() - req_url: %s' % (req_url))
     
    res = spider_url(req_url)
     
    retry_idx = 0;
    while not res and retry_idx < retry_NUM:
        retry_idx += 1
        res = spider_url(req_url)
     
    if res :
        resJson = json.loads(res)
        resJson = resJson['Response']
        print "<br><br> +++++ action : %s <br><br> resJson: " % (req_action,)
        return resJson
    else:
        return None;
 
 
if __name__ == "__main__":
    req_action_query = 'DescribeAddresses'          # 查询弹性IP
    req_action_unbind = 'DisassociateAddress'       # 解绑弹性IP
    req_action_release = 'ReleaseAddresses'         # 释放弹性IP
    req_action_transform = 'TransformAddress'       # 公网IP转弹性IP
 
    req_region='ap-guangzhou'
    req_extra_params = '';
 
    # 1. 查询弹性IP列表
    resJson = qcloud_eip_sign(req_action_query, req_region)
    print json.dumps(resJson)
