<?php

$request = Zend_Controller_Front::getInstance()->getRequest();

$isMapRequest = $request->getModuleName() == 'geolocation';
 
// Get the address, latitude, longitude, and the radius from parameters
$address = trim($request->getParam('geolocation-address'));
$currentLat = trim($request->getParam('geolocation-latitude'));
$currentLng = trim($request->getParam('geolocation-longitude'));
$radius = trim($request->getParam('geolocation-radius'));
$mapped = $request->getParam('geolocation-mapped');

if (empty($radius)) {
    $radius = get_option('geolocation_default_radius');
}

if (get_option('geolocation_use_metric_distances')) {
   $distanceLabel =  __('Geographic Radius (kilometers)');
   } else {
   $distanceLabel =  __('Geographic Radius (miles)');
}

?>

<?php if (!$isMapRequest): ?>
<div class="field">
    <div class="two columns alpha">
        <?= $this->formLabel('geolocation-mapped', __('Geolocation Status')); ?>
    </div>
    <div class="five columns omega inputs">
        <?= $this->formSelect('geolocation-mapped',  html_escape($mapped), array(), array(
            '' => __('Select Below'),
            '1' => __('Only Items with Locations'),
            '0' => __('Only Items without Locations'),
        )); ?>
    </div>
</div>
<?php endif; ?>

<div class="field">
    <div class="two columns alpha">
        <?= $this->formLabel('geolocation-address', __('Geographic Address')); ?>
    </div>
    <div class="five columns omega inputs">
        <?= $this->formText('geolocation-address',  html_escape($address), array('size' => '40', 'id' => 'geolocation-address-input')); ?>
        <?= $this->formHidden('geolocation-latitude', html_escape($currentLat), array('id' => 'geolocation-latitude-input')); ?>
        <?= $this->formHidden('geolocation-longitude', html_escape($currentLng), array('id' => 'geolocation-longitude-input')); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <?= $this->formLabel('geolocation-radius', html_escape($distanceLabel)); ?>
    </div>
    <div class="five columns omega inputs">
        <?= $this->formText('geolocation-radius', html_escape($radius), array('size' => '40')); ?>
    </div>
</div>

<?= js_tag('geocoder'); ?>
<?php $geocoder = json_encode(get_option('geolocation_geocoder')); ?>
<script type="text/javascript">
(function ($) {
    function disableOnUnmapped(mappedInput) {
        var disabled = false;
        if (mappedInput.val() === '0') {
            disabled = true;
        }
        $('#geolocation-address-input, #geolocation-latitude, #geolocation-longitude, #geolocation-radius').prop('disabled', disabled);
    }
    
    $(document).ready(function() {
        var geocoder = new OmekaGeocoder(<?= $geocoder; ?>);
        var pauseForm = true;
        $('#geolocation-address-input').parents('form').submit(function(event) {
            // Find the geolocation for the address
            if (!pauseForm) {
                return;
            }

            var form = this;
            var address = $('#geolocation-address-input').val();
            if ($.trim(address).length > 0) {
                event.preventDefault();
                geocoder.geocode(address).then(function (coords) {
                    $('#geolocation-latitude-input').val(coords[0]);
                    $('#geolocation-longitude-input').val(coords[1]);
                    pauseForm = false;
                    form.submit();
                }, function () {
                    alert('Error: "' + address + '" was not found!');
                });
            }
        });
        var mapped = $('#geolocation-mapped');
        disableOnUnmapped(mapped);
        $(mapped).change(function () {
            disableOnUnmapped($(this));
        });
    });
})(jQuery);
</script>
