<?php

// Get the data from local JSON, store the last day separately
if (! file_exists("{$root_folder}/app/data/data.json")) {
    exit('File data.json does not exist.');
}
$json_file = file_get_contents("{$root_folder}/app/data/data.json");
$full_stats = json_decode($json_file, true);
$dates = array_keys($full_stats);
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

// Check if a specific tier is requested. If a locale is already requested, ignore it.
$tiers = [
    'tier1' => [
        'label'   => 'Tier 1',
        'locales' => ['de', 'en-CA', 'en-GB', 'fr'],
    ],
    'top15' => [
        'label'   => 'Top 15 locales',
        'locales' => [
            'cs', 'de', 'es-AR', 'es-ES', 'es-MX', 'fr', 'hu', 'id', 'it', 'ja',
            'nl', 'pl', 'pt-BR', 'ru', 'zh-CN',
        ],
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
    'all' => 'everything',
];
if (isset($_REQUEST['timeframe'])) {
    $requested_timeframe = htmlspecialchars($_REQUEST['timeframe']);
    if (! array_key_exists($requested_timeframe, $timeframes)) {
        exit("Unknown timeframe {$requested_timeframe}");
    }
} else {
    $requested_timeframe = 'all';
}

// Create HTML selectors
$html_supported_locales = '';
foreach ($supported_locales as $supported_locale) {
    $supported_locale_label = str_replace('-', '&#8209;', $supported_locale);
    $html_supported_locales .= "<a href=\"?locale={$supported_locale}&timeframe={$requested_timeframe}\">{$supported_locale_label}</a> ";
}

$html_supported_tiers = '';
foreach ($tiers as $tier_name => $tier_info) {
    $html_supported_tiers .= "<a href=\"?tier={$tier_name}&timeframe={$requested_timeframe}\">{$tier_info['label']}</a> ";
}

$html_supported_timeframes = '';
foreach ($timeframes as $timeframe_name => $timeframe_label) {
    $additional_params = $requested_locale == 'all'
        ? "&tier={$requested_tier}"
        : "&locale={$requested_locale}";
    $html_supported_timeframes .= "<a href=\"?timeframe={$timeframe_name}{$additional_params}\">{$timeframe_label}</a> ";
}
