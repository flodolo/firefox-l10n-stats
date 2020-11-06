<?php

use Cache\Cache;

// Create the table's body
$html_detail_body = '';

// This controller is never used for a single locale
$requested_locales = $requested_tier == 'all'
    ? $supported_locales
    : $tiers[$requested_tier]['locales'];

$table_header = '
    <th>Locale</th>
    <th>Completion (%)</th>
    <th>Translated strings</th>
    <th>Missing strings</th>
    <th>Pending suggestions</th>
';

foreach ($latest_stats as $locale => $locale_data) {
    if (! in_array($locale, $requested_locales)) {
        continue;
    }
    $percentage = $locale_data['completion'];
    if ($percentage == 100) {
        $class = 'success';
    } elseif ($percentage > 50) {
        $class = 'warning';
    } else {
        $class = 'danger';
    }
    $html_detail_body .= "
	<tr class=\"{$class}\">
        <th>{$locale}</th>
        <td class=\"number\">{$locale_data['completion']} %</td>
        <td class=\"number\">{$locale_data['translated']}</td>
        <td class=\"number\">{$locale_data['missing']}</td>
        <td class=\"number\">{$locale_data['suggestions']}</td>
    </tr>
    ";
}

// Check locales at risk (constantly increasing number of pending
// suggestions or missing strings)
$cache_id = "locales_risk_{$requested_tier}_{$requested_timeframe}";
if (! $locales_risk = Cache::getKey($cache_id, 60 * 60)) {
    // Start from the last day available, and move back in time
    $previous_date = '';
    $available_dates = array_keys($full_stats);
    rsort($available_dates);

    foreach ($available_dates as $current_date) {
        if (new DateTime($current_date) < $stop_date) {
            break;
        }
        $current_data = $full_stats[$current_date];
        if ($previous_date == '') {
            // No previous data, all locales among those requested are at risk
            $locales_risk = [
                'missing'     => array_intersect(array_keys($current_data), $requested_locales),
                'suggestions' => array_intersect(array_keys($current_data), $requested_locales),
            ];
        } else {
            foreach ($locales_risk['missing'] as $locale) {
                // If the number of missing strings decreased or the locale was
                // added half-way, consider it not at risk.
                $previous_data = $full_stats[$previous_date][$locale];
                if (! isset($previous_data) || $previous_data['missing'] < $current_data[$locale]['missing']) {
                    $locales_risk['missing'] = array_diff($locales_risk['missing'], [$locale]);
                }
            }
            foreach ($locales_risk['suggestions'] as $locale) {
                // If the number of pending suggestions decreased or the locale
                // was added half-way, consider it not at risk.
                $previous_data = $full_stats[$previous_date][$locale];
                if (! isset($previous_data) || $previous_data['suggestions'] < $current_data[$locale]['suggestions']) {
                    $locales_risk['suggestions'] = array_diff($locales_risk['suggestions'], [$locale]);
                }
            }
        }
        $previous_date = $current_date;
    }
    Cache::setKey($cache_id, $locales_risk);
}

// Completion chart.js graph for all requested locales
$cache_id = "locales_progression_{$requested_tier}_{$requested_timeframe}";
if (! $locales_progression = Cache::getKey($cache_id, 60 * 60)) {
    $locales_progression = [];
    foreach ($full_stats as $date => $date_data) {
        if (new DateTime($date) < $stop_date) {
            continue;
        }
        foreach ($requested_locales as $locale) {
            $completion = isset($date_data[$locale])
                ? $date_data[$locale]['completion']
                : 0;
            $locales_progression[$locale][] = $completion;
        }
    }
    Cache::setKey($cache_id, $locales_progression);
}

$graph_data = "<script type=\"text/javascript\">\n";

$labels = '    let dates = [';
foreach (array_keys($full_stats) as $date) {
    if (new DateTime($date) < $stop_date) {
        continue;
    }
    $labels .= '"' . $date . '",';
}
$labels .= "]\n";
$graph_data .= $labels;

$graph_data .= "    let locales_data = {};\n";
foreach ($requested_locales as $locale) {
    $graph_data .= "    locales_data[\"{$locale}\"] = [" . implode(',', $locales_progression[$locale]) . "]\n";
}

$legend_status = $requested_tier != 'all'
    ? "position: 'right'"
    : 'display: false';
$graph_data .= "
    let ctx = document.getElementById(\"localesChartCompletion\");
    let localesChart = new Chart(ctx, {
    type: 'line',
    options: {
        legend: {
            {$legend_status}
        },
        scales: {
            xAxes: [{
                type: 'time',
                time: {
                    unit: 'day'
                }
            }],
            yAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: 'Completion'
                },
                ticks: {
                    stepSize: 0.5
                }
            }]
        },
        title: {
            display: true,
            text: 'Completion Level',
            fontSize: 24,
            padding: 10
        }
    },
    data: {
        labels: dates,
        datasets: [";

$colors = [
    '#8dd3c7', '#d4d498', '#bebada', '#fb8072', '#80b1d3', '#fdb462',
    '#b3de69', '#fccde5', '#d9d9d9', '#bc80bd', '#ccebc5', '#ffed6f',
    '#a6cee3', '#1f78b4', '#b2df8a', '#33a02c', '#fb9a99', '#e31a1c',
    '#fdbf6f', '#ff7f00', '#cab2d6', '#6a3d9a', '#ffff99', '#b15928',
];

$i = 0;
foreach ($requested_locales as $locale) {
    $graph_data .= "
        {
            data: locales_data[\"{$locale}\"],
            label: \"" . $locale . '",
            fill: false,
            backgroundColor: "' . $colors[$i] . '",
            borderColor: "' . $colors[$i] . '"
        },
    ';

    $i += 1;
    if ($i >= count($colors)) {
        $i = 0;
    }
}

$graph_data .= ']
    }});
</script>
';

$locale_name = 'All';
$tier_name = $tiers[$requested_tier]['label'];
$timeframe_name = $timeframes[$requested_timeframe];

$page_title = 'Overall View';
$selectors_enabled = true;
$sub_template = 'tier.php';
