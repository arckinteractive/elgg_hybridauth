elgg_hybridauth
===============

HybridAuth Client for Elgg
--------------------------

HybridAuth Client for Elgg is an authentication tool that allows users to create new Elgg accounts using their social media accounts.

Providers included by default:
* AOL
* Facebook
* Foursquare
* Google
* LinkedIn
* Live
* MySpace
* OpenID
* Twitter
* Yahoo

Visit [http://hybridauth.sourceforge.net/](http://hybridauth.sourceforge.net/) for more information about HybridAuth library and additional providers that can be added to this distribution.

### Features ###

* Allows a single Elgg profile to be connected to multiple provider accounts
* Follows Elgg's native registration workflow
* Allows users to authorize / deauthorize providers in their account settings
* Optionally, allows administrators to import user settings from elgg_social_login and social_connect
* Provides an interface to configure permissions scopes for each provider


### Provider Setup ###

#### Setting up Facebook ####
* Go to https://developers.facebook.com/apps
* Create a new App
* Select Website with Facebook Login and enter your site URL
* Copy your App ID into Provider ID field in the plugin settings
* Copy your App Secret into the Private Key field in the plugin settings
* For more on permissions scope, visit https://developers.facebook.com/docs/reference/login/

#### Setting up Twitter ####
* Go to https://dev.twitter.com/apps/new
* Create a new application
* Enter a Callback URL: http://SITE-URL/hybridauth/endpoint?hauth.done=Twitter
* Copy your Consumer Key into the Public Key field in the plugin settings
* Copy your Consumer Secret into the Private Key field in the plugin settings

#### Setting up Google ####
* Go to https://code.google.com/apis/console/
* Create a new project
* Switch to the API Access tab
* Click Create an OAauth 2.0 Client ID and fill out the form
* In Client ID settings:
	> Application Type is Web Application
	> Your Site or Hostname - click on More options
	> Authorized Redirect URIs - enter the Authentication URL http://SITE-URL/hybridauth/endpoint?hauth.done=Google
* Copy your Client ID into the Provider ID field in the plugin settings
* Copy your Client secret into the Private Key field in the plugin settings
* For more on permissions scope, see https://developers.google.com/accounts/docs/OAuth2Login#consentpageexperience and https://developers.google.com/oauthplayground/

#### Setting up LinkedIn ####
* Go to https://www.linkedin.com/secure/developer
* Create new application
	> In OAuth User Agreement, update the default scope to your needs. If you are unsure, check r_basicprofile, r_emailaddress, rw_nus and r_network
	> You do not need to fill out redirect URLs
* Copy the API Key into the Public Key field in the plugin settings
* Copy the Secret Key into the Private Key field in the plugin settings
