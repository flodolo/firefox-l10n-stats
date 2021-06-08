<?php
use Cache\Cache;

// Create the table's body
$html_detail_body = '';

$locale_data = $latest_stats[$requested_locale];
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
    <td class=\"number\">{$locale_data['completion']} %</td>
    <td class=\"number\">{$locale_data['translated']}</td>
    <td class=\"number\">{$locale_data['missing']}</td>
    <td class=\"number\">{$locale_data['suggestions']}</td>
</tr>
";

// Completion chart.js graph for total/missing strings
$cache_id = "locale_numbers_{$requested_locale}_{$requested_timeframe}";
if (! $locale_numbers = Cache::getKey($cache_id, 60 * 60)) {
    $locale_numbers = [
        'completion'  => [],
        'missing'     => [],
        'suggestions' => [],
    ];
    foreach ($full_stats as $date => $date_data) {
        if (new DateTime($date) < $stop_date) {
            continue;
        }
        if (isset($date_data[$requested_locale])) {
            $locale_numbers['completion'][] = $date_data[$requested_locale]['completion'];
            $locale_numbers['missing'][] = $date_data[$requested_locale]['missing'];
            $locale_numbers['suggestions'][] = $date_data[$requested_locale]['suggestions'];
        } else {
            $locale_numbers['completion'][] = 0;
            $locale_numbers['missing'][] = 0;
            $locale_numbers['suggestions'][] = 0;
        }
    }
    Cache::setKey($cache_id, $locale_numbers);
}

$graph_data = "<script type=\"text/javascript\">\n";

$labels = '    let dates = [';
foreach (array_keys($full_stats) as $date) {
    $dt = new DateTime($date);
    if ($dt < $stop_date || $dt > $last_day_dt) {
        continue;
    }
    $labels .= '"' . $date . '",';
}
$labels .= "]\n";
$graph_data .= $labels;

$graph_data .= '    let completion = [' . implode(',', $locale_numbers['completion']) . "]\n";
$graph_data .= '    let missing = [' . implode(',', $locale_numbers['missing']) . "]\n";
$graph_data .= '    let suggestions = [' . implode(',', $locale_numbers['suggestions']) . "]\n";

$graph_data .= "
    let ctxCompletion = document.getElementById(\"localeChartCompletion\");
    let completionChart = new Chart(ctxCompletion, {
    type: 'line',
    options: {
        legend: {
            position: \"right\"
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
                    labelString: 'Percentage of completion'
                },
                ticks: {
                    stepSize: 0.5
                }
            }]
        },
        title: {
            display: true,
            text: 'Completion progress',
            fontSize: 24,
            padding: 10
        }
    },
    data: {
        labels: dates,
        datasets: [";
$graph_data .= '
        {
            data: completion,
            label: "Completion level",
            fill: false,
            backgroundColor: "#7bc876",
            borderColor: "#7bc876"
        },
    ]
    }});
';

$graph_data .= "
    let ctxMissing = document.getElementById(\"localeChartMissing\");
    let missingChart = new Chart(ctxMissing, {
    type: 'line',
    options: {
        legend: {
            position: \"right\"
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
                    labelString: 'Number of strings'
                },
                ticks: {
                    stepSize: 1
                }
            }]
        },
        title: {
            display: true,
            text: 'Missing strings',
            fontSize: 24,
            padding: 10
        }
    },
    data: {
        labels: dates,
        datasets: [";
$graph_data .= '
        {
            data: missing,
            label: "Missing strings",
            fill: false,
            backgroundColor: "#5f7285",
            borderColor: "#5f7285"
        },
    ]
    }});
';

$graph_data .= "
    let ctxSuggested = document.getElementById(\"localeChartSuggested\");
    let suggestedChart = new Chart(ctxSuggested, {
    type: 'line',
    options: {
        legend: {
            position: \"right\"
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
                    labelString: 'Number of strings'
                },
                ticks: {
                    stepSize: 1
                }
            }]
        },
        title: {
            display: true,
            text: 'Pending suggestions',
            fontSize: 24,
            padding: 10
        }
    },
    data: {
        labels: dates,
        datasets: [";
$graph_data .= '
        {
            data: suggestions,
            label: "Pending suggestions",
            fill: false,
            backgroundColor: "#4fc4f6",
            borderColor: "#4fc4f6"
        },
    ]
    }});
</script>
';

$locale_name = $requested_locale;
$tier_name = 'All';

$page_title = 'Locale View';
$selectors_enabled = true;
$sub_template = 'locale.php';
