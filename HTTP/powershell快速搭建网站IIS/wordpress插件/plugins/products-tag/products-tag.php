<?php
/*
Plugin Name: Products tag
Description: Products tag!
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
function zb_create_tag_page(){
	$themes=get_stylesheet_directory();
        echo "<h1>添加商品tag标签</h1>
<form action='/wp-content/plugins/products-tag/products-tag-all.php' method='post' enctype='multipart/form-data'>
    <span>请上传tag标签csv文件:</span>
    <input type='file' name='file' placeholder='请上传tag关键字'>
    <br>
    <br>
    <span>请输入添加tag标签数量:</span>
    <input type='number' name='num' value='1'>
    <input type='hidden' name='tag_all' value='tag_all'>
    <br>
    <br>
    <input type='submit' value='添加商品tag标签'>
</form>
";
}


// 注册菜单
function zb_register_tag(){
    add_menu_page( '商品tag标签', '商品tag标签', 'manage_options', 'products-tag', 'zb_create_tag_page','dashicons-admin-plugins');
}
add_action('admin_menu', 'zb_register_tag');
function tag_statistical_stop(){
    //update_option("yg-copyright","yes");	
} 
//停用插件时的方法
register_deactivation_hook( __FILE__, 'tag_statistical_stop');
function tag_statistical_install() {
    //update_option("yg-copyright","<p>版权信息</p>");
}
//启用插件时调用的方法
register_activation_hook( __FILE__, 'tag_statistical_install');
?>