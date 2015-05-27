<?php

if (!elgg_get_plugin_setting('public_auth', 'elgg_hybridauth')) {
	gatekeeper();
}

Hybrid_Endpoint::process();