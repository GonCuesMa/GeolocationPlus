<?php 
queue_css_file('geolocation-items-map');

$title = __('Browse Items on the Map') . ' ' . __('(%s total)', $totalItems);?>
<?= head(array('title' => $title, 'bodyclass' => 'map browse')); ?>


<h1><?= html_escape($title); ?></h1>

<nav class="items-nav navigation secondary-nav">
    <?= public_nav_items(); ?>
</nav>

<?= item_search_filters(); ?>
<?= pagination_links(); ?>


<div id="geolocation-browse">
    <?= $this->geolocationMapBrowse('map_browse', array('list' => 'map-links', 'params' => $params)); ?>
  <div id="map-links"><h2><?= html_escape(__('Find An Item on the Map')); ?></h2></div>
</div>

<?= foot(); ?>
