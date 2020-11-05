<?php

include realpath(__DIR__ . '/../app/inc/init.php');
include "{$root_folder}/app/inc/query_params.php";

$controller = $requested_locale == 'all'
    ? 'tier'
    : 'locale';

include "{$root_folder}/app/controllers/{$controller}.php";
include "{$root_folder}/app/templates/base.php";
