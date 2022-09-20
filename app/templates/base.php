<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset=utf-8>
	<title>Firefox L10N Dashboard - <?php echo $page_title; ?></title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/datatables.min.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css" type="text/css" media="all" />
  <link rel="stylesheet" href="assets/css/main.css" type="text/css" media="all" />
  <script src="assets/js/jquery-3.6.0.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/datatables.min.js"></script>
  <script src="assets/js/dataTables.bootstrap5.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/chart.min.js"></script>
</head>
<body>
  <div class="container" id="mainbody">
    <?php
          if ($selectors_enabled):
    ?>
    <h1>Firefox L10N Dashboard</h1>
    <p>See the <a href="https://github.com/flodolo/firefox-l10n-stats/">GitHub repository</a> for background information.</p>
    <h2>Tier: <?php echo $tier_name; ?></h2>
    <div class="list module_list">
      <p>
        Display status for a set of locales<br/>
        <?php echo $html_supported_tiers; ?>
      </p>
    </div>
    <h2>Timeframe: <?php echo $timeframe_name; ?></h2>
    <div class="list module_list">
      <p>
        Display status for a specific timeframe<br/>
        <?php echo $html_supported_timeframes; ?>
      </p>
    </div>
    <h2>Locale: <?php echo $locale_name; ?></h2>
    <div class="list locale_list">
        <p>
          Display localization status for a specific locale<br/>
          <?php echo $html_supported_locales; ?>
        </p>
    </div>
    <?php
          endif;
    ?>

    <?php
    if (isset($sub_template)) {
      include "{$root_folder}/app/templates/{$sub_template}";
    }

    if ($selectors_enabled) {
    ?>
    <p id="lastupdate"><small>Last update: <?php echo $last_day; ?> UTC</small></p>
    <?php
    }
    ?>
  </div>
  <?php echo $graph_data; ?>
</body>
