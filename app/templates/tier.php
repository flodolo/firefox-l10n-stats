<h2>Locales at risk</h2>
<p>Increasing number of missing strings:
<?php
  if (empty($locales_risk['missing'])) {
    echo('none');
  } else {
    foreach ($locales_risk['missing'] as $locale) {
      echo "<a href='?locale={$locale}&timeframe={$requested_timeframe}'>{$locale}</a> ";
    }
  }
?>
</p>
<p>Increasing number of pending suggestions:
 <?php
  if (empty($locales_risk['suggestions'])) {
    echo('none');
  } else {
    foreach ($locales_risk['suggestions'] as $locale) {
      echo "<a href='?locale={$locale}&timeframe={$requested_timeframe}'>{$locale}</a> ";
    }
  }
?>
</p>

<h2>Locales data</h2>
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
