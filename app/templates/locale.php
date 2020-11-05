<h2>Locale Details</h2>
<table class="table table-bordered" id="locale_details">
  <thead>
    <tr>
        <th>Completion (%)</th>
        <th>Translated strings</th>
        <th>Missing strings</th>
        <th>Pending suggestions</th>
    </tr>
  </thead>
  <tbody>
      <?php echo $html_detail_body; ?>
  </tbody>
</table>

<canvas id="localeChartCompletion" class="chart"></canvas>
<canvas id="localeChartSuggested" class="chart"></canvas>
