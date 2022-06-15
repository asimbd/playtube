<?php
$pt->title = $lang->maintenance;
$pt->description = $pt->config->description;
$pt->page = "maintenance";
$pt->content = PT_LoadPage("maintenance/content");

$content_data = [
    'CONTAINER_TITLE' => $pt->title,
    'CONTAINER_CONTENT' => $pt->content,
    'theme_url' => $config['theme_url'],
    "HEADER_AD" => "",
    "FOOTER_AD" => "",
    "FOOTER_LAYOUT" => "",
    "EXTRA_JS" => "",
    "OG_METATAGS" => "",
    "HEADER_LAYOUT" => "",
    "ANNOUNCEMENT" => "",
    "DATE" => date('Y')
];

echo PT_LoadPage('container', $content_data);
exit();
