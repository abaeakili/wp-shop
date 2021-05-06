<html>
	<head>
		<title><?php print $this->description; ?></title>
        <?php print $this->scripts_load?>
	</head>
	<body style = "padding: 0px; margin: 0px;">
		<a class = "video_full" id = "video" href = "<?php print $this->config->demo_product_live_path.'/'.$this->filename; ?>"></a>
		<script type="text/javascript">
            var liveurl = '<?php print WOPSHOP_PLUGIN_URL?>';
			jQuery('#video').media( { width: <?php print $this->config->video_product_width; ?>, height: <?php print $this->config->video_product_height; ?>, autoplay: 1} );
		</script>
	</body>
</html>