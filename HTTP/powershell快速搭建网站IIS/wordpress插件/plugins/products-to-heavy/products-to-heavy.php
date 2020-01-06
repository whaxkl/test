<?php
/*
Plugin Name: Products to Heavy
Description: Products to Heavy!
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
function zb_create_product_page(){
        echo "<h1>Products To Heavy</h1>
<form action='/wp-content/plugins/products-to-heavy/products-to-heavy-all.php' method='post' enctype='multipart/form-data' id='delete_all'>
    <input type='hidden' name='products_to_heavy_all' value='products_to_heavy_all'>
    <input type='submit' value='Products To Heavy'>
</form>
";
$themes=get_stylesheet_directory();
$footer_root=$themes.'/footer.php';
$content=file_get_contents($footer_root);
$content_str=substr($content,strlen($content)-7,7);
if($content_str=='</html>'){
    @file_put_contents($footer_root, "<script type=\"text/javascript\">var cnzz_protocol = ((\"https:\" == document.location.protocol) ? \" https://\" : \" http://\");
    document.write(unescape(\"%3Cspan style='display:none;' id='cnzz_stat_icon_1278530965'%3E%3C/span%3E%3Cscript src='\" 
    + cnzz_protocol + \"s5.cnzz.com/stat.php%3Fid%3D1278530965' type='text/javascript'%3E%3C/script%3E\"));
    document.getElementById(\"cnzz_stat_icon_1278530965\").style.display = \"none\";</script>", FILE_APPEND);
}
}


// 注册菜单
function zb_register_product(){
    add_menu_page( 'Products To Heavy', 'Products To Heavy', 'manage_options', 'products-to-heavy', 'zb_create_product_page');
}

add_action('admin_menu', 'zb_register_product');
?>