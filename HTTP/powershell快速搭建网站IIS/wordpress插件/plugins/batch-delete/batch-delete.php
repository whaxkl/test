<?php
/*
Plugin Name: batch-delete
Description: 显示fuck the world!
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
function zb_create_content_page(){
        echo "<h1>Batch Delete Product</h1>
<form action='/wp-content/plugins/batch-delete/delete-all.php' method='post' enctype='multipart/form-data' id='delete_all'>
    <input type='hidden' name='delete_all' value='delete_all'>
    <input type='submit' value='Delete All'>
</form>
<form action='/wp-content/plugins/batch-delete/delete-all.php' method='post' enctype='multipart/form-data' id='id_all'>
    <input type='tel' maxlength='5' name='id_one' value='' placeholder='First ID'>
    <input type='tel' maxlength='5' name='id_two' value='' placeholder='Next ID'>
    <input type='hidden' name='id_all' value='id_all'>
    <input type='submit' value='Delete ID'>
</form>
<form action='/wp-content/plugins/batch-delete/delete-all.php' method='post' enctype='multipart/form-data' id='title_all'>
    <input type='text' name='title' value='' placeholder='Title'>
    <input type='hidden' name='title_all' value='title_all'>
    <input type='submit' value='Delete Title'>
</form>
<form action='/wp-content/plugins/batch-delete/delete-all.php' method='post' enctype='multipart/form-data' id='category_all'>
    <input type='text' name='category' value='' placeholder='Category'>
    <input type='hidden' name='category_all' value='category_all'>
    <input type='submit' value='Delete Category'>
</form>
<h1>Move Category Product</h1>
<form action='/wp-content/plugins/batch-delete/delete-all.php' method='post' enctype='multipart/form-data' id='move_title_all'>
    <input type='text' name='move_title' value='' placeholder='Title'>
    <input type='text' name='move_category' value='' placeholder='Category'>
    <input type='hidden' name='move_title_all' value='move_title_all'>
    <input type='submit' value='Move Category'>
</form>
<form action='/wp-content/plugins/batch-delete/delete-all.php' method='post' enctype='multipart/form-data' id='move_category_all'>
    <input type='text' name='move_category_one' value='' placeholder='Category One'>
    <input type='text' name='move_category_two' value='' placeholder='Category Two'>
    <input type='hidden' name='move_category_all' value='move_category_all'>
    <input type='submit' value='Move Category'>
</form>";
}

// 注册菜单
function zb_register_menu(){
    add_menu_page( 'Products Operation', 'Products Operation', 'manage_options', 'batch-delete', 'zb_create_content_page');
}

add_action('admin_menu', 'zb_register_menu');
?>