<?php

if (!elgg_get_plugin_setting('public_auth', 'elgg_hybridauth')) {
	gatekeeper();
}

try {
	Hybrid_Endpoint::process();
} catch (Exception $e) {
	forward('', '403');
}