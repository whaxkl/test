<?php
include ( "wp-config.php" ) ;
require_once (ABSPATH.'wp-blog-header.php');
home_url();
global $wpdb;
$qianzui = $_SERVER['REQUEST_SCHEME']?$_SERVER['REQUEST_SCHEME']:http."://".$_SERVER['SERVER_NAME'];
$str_str = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
$str_str.="
	<sitemap>
	<loc>".$qianzui."/sitemap_products_1.xml</loc>
	</sitemap>
	<sitemap>
	<loc>".$qianzui."/sitemap_collections_1.xml</loc>
	</sitemap>
	<sitemap>
	<loc>".$qianzui."/sitemap_blogs_1.xml</loc>
	</sitemap>
	";
$str_str.="</sitemapindex>";
file_put_contents('./sitemap.xml',$str_str);
$sqlcat="select taxonomy,slug from wp_term_taxonomy JOIN wp_terms on wp_terms.term_id=wp_term_taxonomy.term_id where taxonomy='category' and slug <> 'uncategorized'";
$sqltag="select taxonomy,slug from wp_term_taxonomy JOIN wp_terms on wp_terms.term_id=wp_term_taxonomy.term_id where taxonomy='post_tag'";
$myrowscat = $wpdb->get_results($sqlcat);
$myrowstag = $wpdb->get_results($sqltag);
$str = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
$str.="
	<url>
	<loc>".$qianzui."</loc>
	<lastmod>".date('Y-m-d')."</lastmod>
	<changefreq>daily</changefreq>
	<priority>1.0</priority>
	</url>
	";
foreach ($myrowscat as $b) {
    $str.="<url>\r\n";
    $str.="<loc>".$qianzui."/category/".$b->slug."</loc>\r\n";
    $str.="<lastmod>".date("Y-m-d")."</lastmod>\r\n";
    $str.="<changefreq>daily</changefreq>\r\n";
    $str.="<priority>0.9</priority>\r\n";
    $str.="</url>\r\n";
}
foreach ($myrowstag as $b) {
    $str.="<url>\r\n";
    $str.="<loc>".$qianzui."/tag/".$b->slug."</loc>\r\n";
    $str.="<lastmod>".date("Y-m-d")."</lastmod>\r\n";
    $str.="<changefreq>daily</changefreq>\r\n";
    $str.="<priority>0.9</priority>\r\n";
    $str.="</url>\r\n";
}
$myposts = get_posts();
foreach( $myposts as $post ) {
    $str.="<url>\r\n";
    $str.="<loc>".urldecode(get_permalink())."</loc>\r\n";
    $str.="<lastmod>".date("Y-m-d")."</lastmod>\r\n";
    $str.="<changefreq>daily</changefreq>\r\n";
    $str.="<priority>0.8</priority>\r\n";
    $str.="</url>\r\n";
}
$str.="</urlset>";
file_put_contents('./sitemap_blogs_1.xml',$str);
//echo 'update sitemap.xml success <a href="/sitemap.xml">查看</a>';
?>