package com.ximalayaos; 
import javax.crypto.Mac; 
import javax.crypto.spec.SecretKeySpec; 
import java.security.MessageDigest; 
import java.util.ArrayList; 
import java.util.List; 
import java.util.Map; 
import java.util.TreeMap; 
 public class EncryptDemo { 
   public static void main(String[] args) throws Exception { 
       // 排序 
       Map<String, Object> map = new TreeMap<>(); 
       map.put("app_key", "21bcdb2687e64b46b78c3c816c4dee1f"); 
       map.put("client_os_type", 3); 
       map.put("nonce", "pEfjZ1KY6aZQ74HexyzQkCzsaB8BdgSG"); 
       map.put("timestamp", "1568036248412"); 
       map.put("device_id", "abcd12345"); 
       map.put("sn", "111_00_1001"); 
       map.put("text", "播放拔萝卜"); 
       map.put("device_type", 1);
       map.put("app_version", "1.0.0"); 
       map.put("sys_version", "1.0.0"); 
       map.put("sys_type", "1")
       // 用&拼接 
       List<String> list = new ArrayList<>(); 
       map.forEach((k, v) -> list.add(k + "=" + v)); 
       String paramStr = String.join("&", list);
       // Base64编码 
       // Android 环境 
       //String paramBase64 =android.util.Base64.encodeToString(paramStr.getBytes(),android.util.Base64.NO_WRAP);

       // Java 环境 
       String paramBase64 =java.util.Base64.getEncoder().encodeToString(paramStr.getBytes());
       // HMAC-SHA1 
       Mac mac = Mac.getInstance("HmacSHA1"); 
       mac.init(new SecretKeySpec("A6273F165C77C045A52B2504BF8B0108".getBytes(), "HmacSHA1"));
       // MD5 3
       byte[] bytes =MessageDigest.getInstance("MD5").digest(mac.doFinal(paramBase64.getBytes()));

       StringBuilder hs = new StringBuilder(); 
       for (byte b : bytes) { 
           String s = Integer.toHexString(b & 0XFF); 
           if (s.length() == 1) { 
               hs.append('0');
           } 
           hs.append(s); 
       } 
       System.out.println(paramStr + "&sig=" + hs.toString()); 
   } 
} 
