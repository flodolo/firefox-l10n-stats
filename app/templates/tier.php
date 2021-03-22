<h2>Locales at risk</h2>
<p>Constantly increasing values in the selected timeframe for:</p>
<ul>
<li>Missing strings:
<?php
  if (empty($locales_risk['missing'])) {
    echo('none');
  } else {
    foreach ($locales_risk['missing'] as $locale => $locale_days) {
      echo "<a href='?locale={$locale}&timeframe={$requested_timeframe}' title='{$locale_days} days'>{$locale}</a> ";
    }
  }
?>
</li>
<li>Pending suggestions:
 <?php
  if (empty($locales_risk['suggestions'])) {
    echo('none');
  } else {
    foreach ($locales_risk['suggestions'] as $locale => $locale_days) {
      echo "<a href='?locale={$locale}&timeframe={$requested_timeframe}' title='{$locale_days} days'>{$locale}</a> ";
    }
  }
?>
</li>
</ul>

<h2>Locales data</h2>
<p>Average completion: <?php echo $avg_completion; ?>%.</p>
<table class="table table-bordered" id="tier_details">
  <thead>
    <tr>
        <?php echo $table_header; ?>
    </tr>
  </thead>
  <tbody>
      <?php echo $html_detail_body; ?>
  </tbody>
</table>

<canvas id="localesChartCompletion" class="chart"></canvas>
