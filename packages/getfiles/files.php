
<?php
$files = array_diff(scandir("files"), array('.', '..'));
echo json_encode(array_values($files));
?>
