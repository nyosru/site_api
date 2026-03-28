<?php

echo '<form action="" method="POST" enctype="multipart/form-data" >';
echo '<input type="file" name="photo" ><br/>';
echo '<input type="submit" >';
echo '</form>';

if (!empty($_FILES['photo'])) {

    echo '<pre style="font-size: 10px;" >', print_r($_FILES['photo']), '</pre>';
}
