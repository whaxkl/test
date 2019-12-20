<?php
/*require 'vendor/autoload.php';
$file = $_FILES['file'];
if(empty($file)){
    echo "<script>alert('上传文件不能为空');</script>";
    echo "<script>history.back();</script>";
    exit;
}
move_uploaded_file($file["tmp_name"],
    $file["name"]);
$csv = new ParseCsv\Csv($file['name']);
$data = $csv->data;
$str = '';
foreach ($data as $key => $value){
    if(empty($value['domain'])){
        echo "<script>alert('域名不能为空,搭建失败');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
    $str.= $value['domain'].' ';
    if(!empty($value['domain']) && !empty($value['domain'])){
        $uname = $value['uname'];
        $pwd = $value['pwd'];
    }else{
        echo "<script>alert('数据库账号密码不能为空,搭建失败');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}
$str = substr($str,0,strlen($str)-1);
$shell = "powershell -executionpolicy unrestricted -file D:\wordpress\customar.cn\powershell.ps1 $str";
$str = system($shell,$return_status);
@$conn = mysqli_connect('107.187.173.66',$uname,$pwd);
if(!$conn){
    echo "<script>alert('连接数据库失败,安装WordPress失败');</script>";
    echo "<script>history.back();</script>";
    exit;
}
mysqli_query($conn,"BEGIN");
foreach ($data as $keys => $values){
    $domain = $values['domain'];
    if(empty($values['dbname'])){
        $dbname = str_replace('.','_',$values['domain']);
    }else{
        $dbname = str_replace('.','_',$values['dbname']);
    }
    if(empty($values['weblog_title'])){
        $weblog_title = $values['domain'];
    }else{
        $weblog_title = $values['weblog_title'];
    }
    if(empty($values['user_name'])){
        $user_name = 'root';
    }else{
        $user_name = $values['user_name'];
    }
    if(empty($values['admin_password'])){
        $admin_password = '123147gts';
        $admin_password2 = '123147gts';
    }else{
        $admin_password = $values['admin_password'];
        $admin_password2 = $values['admin_password'];
    }
    if(empty($values['admin_email'])){
        $admin_email = 'gaotiansong@163.com';
    }else{
        $admin_email = $values['admin_email'];
    }

    $sql = "CREATE DATABASE $dbname";
    $conn->query($sql);
    if($conn->query($sql)!==TRUE){
        mysqli_query($conn,"ROOLBACK");
	echo "<script>alert('网站搭建成功,wordpress安装失败');</script>";
	echo "<script>history.back();</script>";
	exit;
        //exit("网站搭建成功,wordpress安装失败,{$domain}数据库{$dbname}已存在");
    }
    $url = 'http://'.$values['domain'].'/wp-admin/setup-config.php';
    $url_step0 = 'http://'.$values['domain'].'/wp-admin/setup-config.php?step=0';
    $url_step1 = 'http://'.$values['domain'].'/wp-admin/setup-config.php?step=1';
    $url_step2 = 'http://'.$values['domain'].'/wp-admin/setup-config.php?step=2';
    $url_install = 'http://'.$values['domain'].'/wp-admin/install.php';
    $url_install_step1 = 'http://'.$values['domain'].'/wp-admin/install.php?step=1';
    $url_install_step2 = 'http://'.$values['domain'].'/wp-admin/install.php?step=2';
    $language = array(
        'language' => ''
    );
    $post_data_step2 = array(
        'dbname' =>$dbname,
        'uname' => $uname,
        'pwd' => $pwd,
        'dbhost' => '107.187.173.66',
        'prefix' => 'wp_',
        'language' => '',
        'submit' => 'Submit'
    );
    $post_data_install_step2 = array(
        'weblog_title' => $weblog_title,
        'user_name' => $user_name,
        'admin_password' => $admin_password,
        'admin_password2' => $admin_password2,
        'pw_weak' => 'on',
        'admin_email' => $admin_email,
        'Submit' => '安装WordPress',
        'language' => ''
    );
    $url_data = http_gets($url);
    $url_data_step0= post_curl($url_step0,$language);
    $url_data_step1= http_gets($url_step1);
    @copy('D:\wp-config-sample.php',"D:\wordpress\\{$values['domain']}\wp-config.php");
    $str = file_get_contents("D:\wordpress\\{$values['domain']}\wp-config.php");
    $str = str_replace('database_name_here',$dbname,$str);
    $str = str_replace('username_here',$uname,$str);
    $str = str_replace('password_here',$pwd,$str);
    $str = str_replace('utf8','utf8mb4',$str);
    file_put_contents("D:\wordpress\\{$values['domain']}\wp-config.php",$str);
    //$url_data_step2= post_curl($url_step2,$post_data_step2);
    //$url_data_install= http_gets($url_install);

    //$url_data_install_step1= post_curl($url_install_step1,$language);
    //$url_data_install_step2= post_curl($url_install_step2,$post_data_install_step2);
    curl_request($url_step2,$post_data_step2);
    curl_request($url_install_step2,$post_data_install_step2);
    //echo $url_data;die;
    //echo $url_data_step0;die;
    //echo $url_data_step1;die;
    //echo $url_data_step2;die;
    //echo $url_data_install;die;
    //echo $url_data_install_step1;die;
    //echo $url_data_install_step2;die;
    $html = '';
foreach ($post_data_step2 as $keysds => $valsds){
    $html .= "<input name='{$keysds}' type='text' value='{$valsds}' />";
}
echo "<form style='display:none;' id='form{$keys}' name='form{$keys}' method='post' action='{$url_step2}'>{$html}</form>
<script type='text/javascript'>function load_submit(){document.form{$keys}.submit()}load_submit();</script>";
}
mysqli_query($conn,"COMMIT");
echo "<script>alert('网站搭建成功,WordPress安装成功');</script>";
echo "<script>history.back();</script>";
exit;
//var_dump($con);die;
*/
/*function post_curl($url, $post){
    $ch = curl_init($url);
    $timeout = 6000;
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
//本地测试 不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书

    $data_string = json_encode($post);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json; charset=utf-8",
            "Content-Length: " . strlen($data_string))
    );
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}
function http_gets($url)
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    return $tmpInfo;    //返回json对象
}
//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
function curl_request($url,$post='',$cookie='', $returnCookie=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if($returnCookie){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
}
$file = $_FILES['file'];
if(empty($file)){
    echo "<script>alert('上传文件不能为空');</script>";
    echo "<script>history.back();</script>";
    exit;
}
move_uploaded_file($file["tmp_name"],
    $file["name"]);

$file_path = $file["name"];
if(file_exists($file_path)) {
    $file_contents = file($file_path);
    //var_dump($file_contents);die;
    for ($i = 0; $i < count($file_contents); $i++) {//逐行读取文件内容
        echo $file_contents[$i]."<br>";
    }
}else{
    echo "<script>alert('文件不能为空');</script>";
    echo "<script>history.back();</script>";
    exit;
}

@$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
$str=substr($str,0,-1);
mysqli_query('','');


$data_id=array_column($data_id,'ID');
$str='(';
$mark=0;
foreach($data as $key=>$val){
    if(in_array($val['ID'],$data_id)){
        $str.=$val['ID'].',';
        $mark=1;
        continue;
    }
    if($val['post_type']=='product'){
        $mark=0;
    }
    if($mark==1 && $val['post_type']=='attachment'){
        $str.=$val['ID'].',';
    }
}


$id_all=$_POST['id_all'];
$id_one=$_POST['id_one'];
$id_two=$_POST['id_two'];
if($id_one=='' && $id_two==''){
    echo "<script>alert('ID Empty');</script>";
    echo "<script>history.back();</script>";
    exit;
}
if($id_one!='' && $id_two==''){
    $sql="delete from wp_posts where ID={$id_one} and ID>9 and post_type='product'";
}else if($id_one=='' && $id_two!=''){
    $sql="delete from wp_posts where ID={$id_two} and ID>9 and post_type='product'";
}else if($id_one!='' && $id_two!=''){
    if($id_one>$id_two){
        $sql="delete from wp_posts where ID between {$id_two} and {$id_one} and ID>9 and post_type='product'";
    }else if($id_one<$id_two){
        $sql="delete from wp_posts where ID between {$id_one} and {$id_two} and ID>9 and post_type='product'";
    }else{
        $sql="delete from wp_posts where ID={$id_one} and ID>9 and post_type='product'";
    }
}
$data_id=array_column($data_id,'ID');
$str='(';
$mark=0;
foreach($data as $key=>$val){
    if(in_array($val['ID'],$data_id)){
        $str.=$val['ID'].',';
        $mark=1;
        continue;
    }
    if($val['post_type']=='product'){
        $mark=0;
    }
    if($mark==1 && $val['post_type']=='attachment'){
        $str.=$val['ID'].',';
    }
}

$category_all=$_POST['category_all'];
$category=$_POST['category'];
$sql="delete from wp_posts where ID={$id_one} and ID>9 and post_type='product'";

if(empty($category)){
    echo "<script>alert('ID Empty');</script>";
    echo "<script>history.back();</script>";
    exit;
}array_column
*/





/*$category_all='shopify';
$conn = mysqli_connect('107.187.173.66','root','123147gts','customba_cn');


$sql="select * from wp_posts where id>42";
$result=$conn->query($sql);
$data=mysqli_fetch_all($result,MYSQLI_ASSOC);
$data=array_reverse($data);

$category_sql="select term_id from wp_terms where `name` like '%".$category_all."%' order by term_id desc limit 1";
$result=$conn->query($category_sql);
$category_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
$term_id=$category_data[0]['term_id'];
$wp_term_taxonomy_sql="select term_taxonomy_id from wp_term_taxonomy where term_id={$term_id}";
$result_taxonomy=$conn->query($wp_term_taxonomy_sql);
$term_taxonomy_data=mysqli_fetch_all($result_taxonomy,MYSQLI_ASSOC);
$term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];
$id_sql="select object_id from wp_term_relationships where term_taxonomy_id={$term_taxonomy_id} order by object_id desc";
$result_relationships=$conn->query($id_sql);
$term_relationships_data=mysqli_fetch_all($result_relationships,MYSQLI_ASSOC);
$str='(';
$object_id=array_column($term_relationships_data,'object_id');
foreach($data as $key=>$val){
    if(in_array($val['ID'],$object_id)){
        $str.=$val['ID'].',';
        $mark=1;
        continue;
    }
    if($val['post_type']=='product'){
        $mark=0;
    }
    if($mark==1 && $val['post_type']=='attachment'){
        $str.=$val['ID'].',';
    }
}
$str=substr($str,0,-1);
if($str!=''){
    $str.=')';
    $del_sql="delete from wp_posts where ID in $str and ID>9";
    mysqli_query($conn,$del_sql);
}*/
$conn = mysqli_connect('107.187.173.66','root','123147gts','customba_cn');

$sql="select * from wp_terms where `name`='women'";
$result=$conn->query($sql);
$data=mysqli_fetch_all($result,MYSQLI_ASSOC);
var_dump($data);