elgg_hybridauth
===============

HybridAuth Client for Elgg

HybridAuth Client for Elgg is an authentication tool that allows users to create new Elgg accounts using their social media accounts.

Providers included by default:
- AOL
- Facebook
- Foursquare
- Google
- LinkedIn
- Live
- MySpace
- OpenID
- Twitter
- Yahoo

Visit http://hybridauth.sourceforge.net/ for more information about HybridAuth library and additional providers that can be added to this distribution.

-- Features --

- Single Elgg account with multiple connected social accounts
- Follows Elgg's native registration workflow


-- Setup --

--- Setting up Facebook ---
1. Go to https://developers.facebook.com/apps
2. Create a new App
3. Select Website with Facebook Login and enter your site URL
4. Copy your App ID into Provider ID field in the plugin settings
5. Copy your App Secret into the Private Key field in the plugin settings

--- Setting up Twitter ---
1. Go to https://dev.twitter.com/apps/new
2. Create a new application
2.a. Enter a Callback URL: http://SITE-URL/hybridauth/endpoint?hauth.done=Twitter
3. Copy your Consumer Key into the Public Key field in the plugin settings
4. Copy your Consumer Secret into the Private Key field in the plugin settings

--- Setting up Google ---
1. Go to https://code.google.com/apis/console/
2. Create a new project
3. Switch to the API Access tab
4. Click Create an OAauth 2.0 Client ID and fill out the form
5. In Client ID settings:
5.a. Application Type is Web Application
5.b. Your Site or Hostname - click on More options
5.c. Authorized Redirect URIs - enter the Authentication URL http://SITE-URL/hybridauth/endpoint?hauth.done=Google
6. Copy your Client ID into the Provider ID field in the plugin settings
7. Copy your Client secret into the Private Key field in the plugin settings

--- Setting up LinkedIn ---
1. Go to https://www.linkedin.com/secure/developer
2. Create new application
2.a. It's preferable that r_emailaddress is checked in In OAuth User Agreement
2.b. You do not need to fill out redirect URLs
3. Copy the API Key into the Public Key field in the plugin settings
4. Copy the Secret Key into the Private Key field in the plugin settings