<?php

// Get the data from local JSON, store the last day separately
if (! file_exists("{$root_folder}/app/data/data.json")) {
    exit('File data.json does not exist.');
}
$json_file = file_get_contents("{$root_folder}/app/data/data.json");
$full_stats = json_decode($json_file, true);
$dates = array_keys($full_stats);
sort($dates);
$last_day = end($dates);
$latest_stats = $full_stats[$last_day];

$supported_locales = array_keys($latest_stats);
sort($supported_locales);

// Check if a single locale is requested
if (isset($_REQUEST['locale'])) {
    $requested_locale = htmlspecialchars($_REQUEST['locale']);
    if (! in_array($requested_locale, $supported_locales) && $requested_locale != 'all') {
        exit("Locale {$requested_locale} is not supported");
    }
} else {
    $requested_locale = 'all';
}

// Load locales
$json_locales_file = file_get_contents("{$root_folder}/app/data/locales.json");
$locales = json_decode($json_locales_file, true);


// Check if a specific tier is requested. If a locale is already requested, ignore it.
$tiers = [
    'tier1' => [
        'label'   => 'Tier 1',
        'locales' => $locales['tier1'],
    ],
    'top15' => [
        'label'   => 'Top 15 locales',
        'locales' => $locales['top15'],
    ],
    'release' => [
        'label'   => 'Release locales',
        'locales' => $locales['release'],
    ],
    'all'   => [
        'label'   => 'All locales',
    ],
];

if (isset($_REQUEST['tier']) && ! isset($_REQUEST['locale'])) {
    $requested_tier = htmlspecialchars($_REQUEST['tier']);
    if (! array_key_exists($requested_tier, $tiers)) {
        exit("Unknown tier {$requested_tier}");
    }
} else {
    $requested_tier = 'all';
}

// Check if a timeframe is requested
$timeframes = [
    '1m'  => '1 month',
    '3m'  => '3 months',
    '6m'  => '6 months',
    'all' => 'Everything',
];
if (isset($_REQUEST['timeframe'])) {
    $requested_timeframe = htmlspecialchars($_REQUEST['timeframe']);
    if (! array_key_exists($requested_timeframe, $timeframes)) {
        exit("Unknown timeframe {$requested_timeframe}");
    }
} else {
    $requested_timeframe = 'all';
}

$last_date = new DateTime($last_day);
switch ($requested_timeframe) {
    case '1m':
        $stop_date = $last_date->modify('-1 month');
        break;
    case '3m':
        $stop_date = $last_date->modify('-3 month');
        break;
    case '6m':
        $stop_date = $last_date->modify('-6 month');
        break;
    default:
        $stop_date = new DateTime('1900-01-01');
        break;
}

// Create HTML selectors
$html_supported_locales = '';
foreach ($supported_locales as $supported_locale) {
    $supported_locale_label = str_replace('-', '&#8209;', $supported_locale);
    $html_supported_locales .= "<a href=\"?locale={$supported_locale}&timeframe={$requested_timeframe}\">{$supported_locale_label}</a> ";
}

$html_supported_tiers = '';
foreach ($tiers as $tier_name => $tier_info) {
    $html_supported_tiers .= "<a href=\"?tier={$tier_name}&timeframe={$requested_timeframe}\">{$tier_info['label']}</a>";
    if ($tier_name !== array_key_last($tiers)) {
        $html_supported_tiers .= ' · ';
    }
}

$html_supported_timeframes = '';
foreach ($timeframes as $timeframe_name => $timeframe_label) {
    $additional_params = $requested_locale == 'all'
        ? "&tier={$requested_tier}"
        : "&locale={$requested_locale}";
    $html_supported_timeframes .= "<a href=\"?timeframe={$timeframe_name}{$additional_params}\">{$timeframe_label}</a>";
    if ($timeframe_name !== array_key_last($timeframes)) {
        $html_supported_timeframes .= ' · ';
    }
}
