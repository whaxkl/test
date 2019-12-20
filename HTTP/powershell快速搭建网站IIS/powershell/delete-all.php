<?php
@$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(is_admin()){
    var_dump($_POST);
}
