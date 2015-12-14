<link rel="stylesheet" href="/app/css/foundation.css" />
<?php

$property_id = $_POST['pid'];
$property_requirement = 'view';

require ('property.php');
$property->viewPropertyProfile();

?>
<script src="/app/js/foundation.min.js"></script>
<script src="/app/js/foundation/foundation.tooltip.js"></script>
<script src="/app/js/vendor/form/formValidation.min.js"></script>
<script src="/app/js/vendor/form/foundation.min.js"></script>
