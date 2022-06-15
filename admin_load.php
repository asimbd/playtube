<?php
require 'assets/init.php';

if (IS_LOGGED == false || PT_IsAdmin() == false) {
    header("Location: " . PT_Link(''));
    exit();
}
if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        if (!is_array($value)) {
            $value = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
            $_GET[$key] = strip_tags($value);
        }
        else{
            foreach ($value as $keyv => $valuev) {
                $valuev = preg_replace('/on[^<>=]+=[^<>]*/m', '', $valuev);
                $value[$keyv] = strip_tags($valuev);
            }
            $_GET[$key] = $value;
        }
    }
}
if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        if (!is_array($value)) {
            $value = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
            $_POST[$key] = strip_tags($value);
        }
        else{
            foreach ($value as $keyv => $valuev) {
                $valuev = preg_replace('/on[^<>=]+=[^<>]*/m', '', $valuev);
                $value[$keyv] = strip_tags($valuev);
            }
            $_POST[$key] = $value;
        }
    }
}

$path = (!empty($_GET['path'])) ? getPageFromPath($_GET['path']) : null;
$files = scandir('admin-panel/pages');
unset($files[0]);
unset($files[1]);
$page = 'dashboard';
if (!empty($path['page']) && in_array($path['page'], $files) && file_exists('admin-panel/pages/'.$path['page'].'/content.html')) {
    $page = $path['page'];
}
$data = array();
$text = PT_LoadAdminPage($page.'/content');
?>
<input type="hidden" id="json-data" value='<?php echo htmlspecialchars(json_encode($data));?>'>
<?php
echo $text;
?>