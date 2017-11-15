<?php

return array(

	'hybridauth:admin:diagnostics' => "Diagnostics HybridAuth",

	'hybridauth:admin:diagnostics:requirement' => "Pré-requis",
	'hybridauth:admin:diagnostics:status' => "Statut",
	'hybridauth:admin:diagnostics:message' => "Notes",

	'hybridauth:admin:diagnostics:php_version' => "PHP 5.2+",
	'hybridauth:admin:diagnostics:php_version:pass' => "PHP >= 5.2.0 installé.",
	'hybridauth:admin:diagnostics:php_version:fail' => "PHP >= 5.2.0 non installé.",

	'hybridauth:admin:diagnostics:elgg_oauth' => "Plugin OAuth API 1.8",
	'hybridauth:admin:diagnostics:elgg_oauth:pass' => "Le plugin OAuth API 1.8 est désactivé",
	'hybridauth:admin:diagnostics:elgg_oauth:fail' => "Plugin OAuth API activé.<br />Le plugin <b>OAuth API</b> n'est pas compatible avec ce plugin, et de nombreux fournisseurs d'identité tels que Twitter et MySpace ne vont pas fonctionner ! Veuillez le désactiver !",
	
	'hybridauth:admin:diagnostics:curl' => "Extension CURL",
	'hybridauth:admin:diagnostics:curl:pass' => "Extension PHP Curl [http://www.php.net/manual/en/intro.curl.php] installée.",
	'hybridauth:admin:diagnostics:curl:fail' => "Extension PHP Curl [http://www.php.net/manual/en/intro.curl.php] non instaleée.",

	'hybridauth:admin:diagnostics:json' => "Extension JSON",
	'hybridauth:admin:diagnostics:json:pass' => "Extension PHP JSON [http://php.net/manual/en/book.json.php] installée.",
	'hybridauth:admin:diagnostics:json:fail' => "Extension PHP JSON [http://php.net/manual/en/book.json.php] désactivée.",

	'hybridauth:admin:diagnostics:pecl_oauth' => "Extension PECL OAuth",
	'hybridauth:admin:diagnostics:pecl_oauth:pass' => "Extension PECL OAuth [http://php.net/manual/en/book.oauth.php] not installed.",
	'hybridauth:admin:diagnostics:pecl_oauth:fail' => "Extension PECL OAuth [http://php.net/manual/en/book.oauth.php] installée. L'extension OAuth PECL extension n'est pas compatible avec cette bibliothèque.",

	'hybridauth:admin:elgg_social_login' => "Importer les paramètres utilisateur depuiselgg_social_login",
	'hybridauth:admin:elgg_social_login:count' => "Il semble que vous aviez le plugin <i>elgg_social_login</i> installé sur votre site avant d'activer <i>elgg_hybridauth</i>.<br />
							<b>%s</b> utilisateurs utilisaient précédemment leurs comptes sociaux pour se connecter au site. <br />
							YVous pouvez importer leurs paramètres maintenant de sorte qu'ils puissent continuer à se connecter en utilisant ces comptes sociaux.<br />
							Dans le cas de paramètres conflictuels, les paramètres précédents seront conservés",
	'hybridauth:admin:elgg_social_login:action' => "%s paramètres ont été mis à jour",

	'hybridauth:admin:social_connect' => "Importer les paramètres utilisateur depuis social_connect",
	'hybridauth:admin:social_connect:count' => "Il semble que vous aviez le plugin <i>social_connect</i> installé sur votre site avant d'activer <i>elgg_hybridauth</i>.<br />
							<b>%s</b> utilisateurs utilisaient précédemment leurs comptes sociaux pour se connecter au site. <br />
							Vous pouvez importer leurs paramètres maintenant de sorte qu'ils puissent continuer à se connecter en utilisant ces comptes sociaux.<br />
							Dans le cas de paramètres conflictuels, les paramètres précédents seront conservés",
	'hybridauth:admin:social_connect:action' => "%s paramètres ont été mis à jour",

	'hybridauth:debug_mode' => "Mode Debug",
	'hybridauth:debug_mode:enable' => "Activé",
	'hybridauth:debug_mode:disable' => "Désactivé",

	'hybridauth:persistent_session' => "Sessions utilisateurs persistentes",
	'hybridauth:persistent_session:help' => "N'activez pas cette option si vous n'utilisez HybridAuth que pour la connexion et l'inscription. '
							. 'Cette fonctionnalité est conçue pour faire persister les sessions utilisateurs pour une meilleure intégration avec les API des fournisseurs d'identité",
	'hybridauth:persistent_session:enable' => "Activé",
	'hybridauth:persistent_session:disable' => "Désactivé",

	'hybridauth:provider:enable' => "Activer ce fournisseur d'identité",
	'hybridauth:provider:enabled' => "Activer",
	'hybridauth:provider:disabled' => "Désactiver",

	'hybridauth:provider:id' => "ID du fournisseur",
	'hybridauth:provider:key' => "Clef publique",
	'hybridauth:provider:secret' => "Clef privée",

	'hybridauth:provider:Google:id' => "ID du client",
	'hybridauth:provider:Google:secret' => "Secret du client",

	'hybridauth:provider:Facebook:id' => "ID de l'application'",
	'hybridauth:provider:Facebook:secret' => "Secret de l'application",

	'hybridauth:provider:Twitter:key' => "Clef du consommateur",
	'hybridauth:provider:Twitter:secret' => "Secret du consommateur",

	'hybridauth:provider:LinkedIn:key' => "Clef d'API",
	'hybridauth:provider:LinkedIn:secret' => "Clef secrète",

	'hybridauth:provider:Yahoo:key' => "Clef du consommateur",
	'hybridauth:provider:Yahoo:secret' => "Secret du consommateur",

	'hybridauth:provider:Live:id' => "ID du client",
	'hybridauth:provider:Live:secret' => "Secret du client",

	'hybridauth:provider:Foursquare:id' => "ID du client",
	'hybridauth:provider:Foursquare:secret' => "Secret du client",

	'hybridauth:provider:scope' => "Portée des permissions",
	
	'hybridauth:adapter:pass' => "Le service est actif",

	'hybridauth:connect' => "ou connectez-vous avec&nbsp;:",
	'hybridauth:error' => "Une erreur est survenue, et a été enregistrée. Veuillez contacter l'administrateur du site si l'erreur persiste",
	'hybridauth:error:default' => "Erreur",
	'hybridauth:try_again' => "Veuillez réessayer",
	'hybridauth:login:provider' => "You have been logged in using your %s account",
	'hybridauth:link:provider' => "Votre compte est désormais lié à votre compte %s",
	'hybridauth:link:provider:error' => "Ce compte %s a déjà été lié à un autre profil. Veuillez vous connecter avec cet autre profil pour supprimer ce lien, ou lier ce profil à un autre compte",
	'hybridauth:unlink:provider' => "Un problème est survenu lors de l'authentification de votre compte auprès de %s. Le lien a été supprimé. Si vous avez supprimé l'autorisation de ce site dans %s, vous pouvez réactiver le lien en vous rendant sur votre page de paramètres personnels.",
	'elgg_hybridauth:provider:uid:reset' => "Réinitialiser les authentifications des utilisateurs",
	'elgg_hybridauth:provider:uid:reset:confirm' => "Ceci va forcer tous les utilisateurs à s'authentifier à nouveau avec ce fournisseur d'identité.  Etes-vous sûr ?",
	'elgg_hybridauth:error:invalid:provider' => "Fournisseur d'identité invalide",
	'elgg_hybridauth:success:provider_reset' => "Les authentifications des utilisateurs ont été réinitialisées",
	
	'hybridauth:register' => "Inscription terminée",
	'hybridauth:credentials:instructions' => "Votre nom identifiant et otre mot de passe ont été générés automatiquement. Vos identifiants vous seront envoyés par email une fois que vous aurez terminé votre inscription.",

	'hybridauth:name' => "Nom affiché",
	'hybridauth:name:required' => "Veuillez saisir le nom qui sera utilisé pour ce site",
	'hybridauth:username' => "Identifiant",
	'hybridauth:password' => "Mot de passe",
	'hybridauth:passwordagain' => "Confirmez le mot de passe",
	'hybridauth:email' => "Adresse email",
	'hybridauth:email:required' => "Ce site demande que vous fournissiez une adresse email valide",

	'hybridauth:login' => "Se connecter avec des identifiants existants",
	'hybridauth:credentials:login' => "Un compte avec l'email %s existe déjà. Veuillez vous connecter de sorte que vous puissiez lier votre compte %s à votre profil existant.",

	'hybridauth:provider:user:authenticate' => "Connectez-vous maintenant",
	'hybridauth:provider:user:deauthorize' => "Déconnectez-vous maintenant",
	'hybridauth:provider:user:authenticated' => "Votre compte est désormais lié à ce fournisseur d'identité",
	'hybridauth:provider:user:deauthorized' => "Votre compte n'est désormais plus lié à ce fournisseur d'identité",
	'hybridauth:provider:user:deauthorized:error' => "Votre compte n'a pas pu être déconnecté de ce fournisseur d'identité",
	
	'hybridauth:registration_instructions' => "Mode d'emploi de l'inscription",
	'hybridauth:registration_instructions:help' => "Toute indication ou information que vous devriez relayer auprès des utilisateurs après qu'il se soient authentifiés avec un fournisseur d'identité externe, mais avant qu'ils aient terminé leur inscription.",
	'hybridauth:registration:credentials' => "Envoyer par email les identifiants de connexion Elgg aux membres qui se sont inscrits via HybridAuth ?",

	'hybridauth:accounts' => "Comptes sociaux connectés",

	'hybridauth:public_auth' => "Activer la connexion et l'inscription avec les fournisseurs sociaux (étendre les formulaires) pour les utilisateurs non connectés",
	'hybridauth:public_auth:disable' => "Désactiver",
	'hybridauth:public_auth:enable' => "Activer",

	'hybridauth:provider:openid:name' => "Fournisseur OpenID",
	'hybridauth:provider:openid:name:help' => "Nom du fournisseur OpenID (par ex. StackExchange)",
	'hybridauth:provider:openid:identifier' => "Identifiant OpenID",
	'hybridauth:provider:openid:identifier:help' => "URL OpenID (par ex. https://openid.stackexchange.com)",

	'hybridauth:metadata_import_mapping' => "Correspondance des métadonnées importées",
	'hybridauth:metadata_import_mapping:help' => "Spécifier quel nom de métadonnée devrait être assigné aux informations du profil récupérées depuis le fournisseur d'identité auprès duquel l'utilisateur est enregistré. Laisser vide pour ne pas importer les données",
	'hybridauth:import_mapping_access_id' => "Niveau d'accès des métadonnées importées",
	'hybridauth:import_mapping_access_id:help' => "Niveau d'accès à assigner aux métadonnées importées depuis le profil chez le fournisseur d'identité",
	
);


