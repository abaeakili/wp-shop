<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<script type="text/javascript">
var register_field_require = {};
<?php
foreach($config_fields as $key=>$val){
    if ($val['require']){
        print "register_field_require['".$key."']=1;";
    }
}
?>
</script>