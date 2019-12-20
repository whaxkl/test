<?php
$config = ['a'=>1,'b'=>2];
$html = "<form id='alipaysubmit' name='alipaysubmit' action='https://baidu.com/' method='post'>";
foreach ($config as $key => $value) {
    $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
}
$html .= "<input type='submit' value='ok' style='display:none;'></form>";
echo $html . "<script>document.forms['alipaysubmit'].submit();</script>";

$config = ['a'=>1,'b'=>2];
$html = "<form id='alipaysubmit' name='alipaysubmit' action='http://customar.com/' method='post'>";
foreach ($config as $key => $value) {
    $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
}
$html .= "<input type='submit' value='ok' style='display:none;'></form>";
echo $html . "<script>document.forms['alipaysubmit'].submit();</script>";