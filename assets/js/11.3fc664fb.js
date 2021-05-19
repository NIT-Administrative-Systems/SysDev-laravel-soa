(window.webpackJsonp=window.webpackJsonp||[]).push([[11],{377:function(e,t,a){"use strict";a.r(t);var o=a(45),n=Object(o.a)({},(function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("ContentSlotsDistributor",{attrs:{"slot-key":e.$parent.slotKey}},[a("h1",{attrs:{id:"upgrading"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#upgrading"}},[e._v("#")]),e._v(" Upgrading")]),e._v(" "),a("h2",{attrs:{id:"from-v7-to-v8"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v7-to-v8"}},[e._v("#")]),e._v(" From v7 to v8")]),e._v(" "),a("p",[e._v("Support for Azure AD SSO was added. This is compatible with the OpenAM/ForgeRock Online Passport SSO, and can be used in tandem.")]),e._v(" "),a("p",[e._v("For information on setting up an Azure AD integration, review the updated "),a("RouterLink",{attrs:{to:"/websso.html"}},[e._v("webSSO page")]),e._v(".")],1),e._v(" "),a("h3",{attrs:{id:"breaking-changes"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#breaking-changes"}},[e._v("#")]),e._v(" Breaking Changes")]),e._v(" "),a("ul",[a("li",[e._v("The "),a("code",[e._v("WebSSOController::findUserByNetID()")]),e._v(" method will now always receive the "),a("code",[e._v("$netid")]),e._v(" parameter in lower case. Previously, it was whatever case the API returned.")])]),e._v(" "),a("h2",{attrs:{id:"from-v6-to-v7"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v6-to-v7"}},[e._v("#")]),e._v(" From v6 to v7")]),e._v(" "),a("p",[e._v("Support for older versions of PHP has been discontinued. v7 requires PHP 7.4 or higher. You can continue to use an older version of the package if you are using an older version of PHP.")]),e._v(" "),a("p",[e._v("The "),a("code",[e._v("WEBSSO_STRATEGY=classic")]),e._v(" option has been removed entirely.")]),e._v(" "),a("p",[e._v("The dependency on Duo's PHP SDK, along with supporting code for doing Duo authentication in your own app, has been removed. The newer webSSO login flow includes the Duo prompt; your application no longer has to present the widget.")]),e._v(" "),a("ul",[a("li",[e._v("If you have ejected the "),a("code",[e._v("config/duo.php")]),e._v(" file, you can remove the file.")]),e._v(" "),a("li",[e._v("If you have the "),a("code",[e._v("mfa_route_name")]),e._v(" route overwritten per "),a("RouterLink",{attrs:{to:"/websso.html#changing-routes"}},[e._v("the webSSO Changing Routes guide")]),e._v(", you can remove the line of code.")],1),e._v(" "),a("li",[e._v("If you have "),a("code",[e._v("Route::resource('auth/mfa', 'Auth\\DuoController')->only(['index', 'store']);")]),e._v(" in your "),a("code",[e._v("routes/web.php")]),e._v(" file, you can remove the line of code.")]),e._v(" "),a("li",[e._v("If you have an "),a("code",[e._v("Http\\Controllers\\Auth\\DuoController")]),e._v(" controller, you can remove the file.")])]),e._v(" "),a("h3",{attrs:{id:"breaking-changes-2"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#breaking-changes-2"}},[e._v("#")]),e._v(" Breaking Changes")]),e._v(" "),a("ul",[a("li",[a("p",[e._v("The "),a("code",[e._v("Northwestern\\SysDev\\SOA\\Auth\\WebSSOAuthentication")]),e._v(" trait's "),a("code",[e._v("findUserByNetID")]),e._v(" method was previously abstract. It is now defined in the trait, but throws an exception if it is not re-defined in your application.")]),e._v(" "),a("p",[e._v("This change should not have any practical impact to your application.")]),e._v(" "),a("p",[e._v("It is necessary for PHP 8 compatability; asking the service container for additional variables beyond the "),a("code",[e._v("string $netid")]),e._v(" parameter defined in the method signature would cause the runtime to error out, since "),a("a",{attrs:{href:"https://php.watch/versions/8.0/lsp-errors#lsp",target:"_blank",rel:"noopener noreferrer"}},[e._v("PHP 8 now raises a fatal error instead of a warning"),a("OutboundLink")],1),e._v(" when an abstract method's parameters differ.")])]),e._v(" "),a("li",[a("p",[e._v("If you have implemented a custom "),a("code",[e._v("Northwestern\\SysDev\\SOA\\Auth\\Strategy\\WebSSOStrategy")]),e._v(", the login method no longer takes the "),a("code",[e._v("string $mfa_route_name")]),e._v(" parameter. The new method signature is as follows:")]),e._v(" "),a("p",[a("code",[e._v("public function login(Request $request, string $login_route_name);")])])])]),e._v(" "),a("h2",{attrs:{id:"from-v5-to-v6"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v5-to-v6"}},[e._v("#")]),e._v(" From v5 to v6")]),e._v(" "),a("p",[e._v("v6 changes the default webSSO strategy from the legacy webSSO system to the newer one. This is a breaking change that merits a major version bump, but if you have already switched (and most systems have) then this release can be treated as a minor upgrade.")]),e._v(" "),a("p",[e._v("The "),a("code",[e._v("USE_NEW_WEBSSO_SERVER")]),e._v(" environment variable has been removed. If you want to use the older webSSO system, you can configure your system like this:")]),e._v(" "),a("div",{staticClass:"language-ini extra-class"},[a("pre",{pre:!0,attrs:{class:"language-ini"}},[a("code",[a("span",{pre:!0,attrs:{class:"token constant"}},[e._v("WEBSSO_STRATEGY")]),a("span",{pre:!0,attrs:{class:"token attr-value"}},[a("span",{pre:!0,attrs:{class:"token punctuation"}},[e._v("=")]),e._v("classic")]),e._v("\n"),a("span",{pre:!0,attrs:{class:"token constant"}},[e._v("WEBSSO_URL_BASE")]),a("span",{pre:!0,attrs:{class:"token attr-value"}},[a("span",{pre:!0,attrs:{class:"token punctuation"}},[e._v("=")]),e._v("https://websso.it.northwestern.edu")]),e._v("\n")])])]),a("p",[e._v("Using the new webSSO is unchanged. You should remove the "),a("code",[e._v("USE_NEW_WEBSSO_SERVER=true")]),e._v(" environment variable to avoid confusion in the future, but it won't hurt anything.")]),e._v(" "),a("p",[e._v("If your app has the ejected "),a("code",[e._v("resources/views/auth/mfa.blade.php")]),e._v(" file, it can be removed. The new webSSO handles the MFA prompt during its login flow, so this view is no longer used.")]),e._v(" "),a("h2",{attrs:{id:"from-v4-to-v5"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v4-to-v5"}},[e._v("#")]),e._v(" From v4 to v5")]),e._v(" "),a("p",[e._v("v5 is a compatability release for Laravel 7, and drops support for older versions of Laravel. Users on Laravel 5.x & 6 may continue to use v4.")]),e._v(" "),a("p",[e._v("If you do not already have the dependency, the "),a("code",[e._v("laravel/ui")]),e._v(" package is now required.")]),e._v(" "),a("h2",{attrs:{id:"from-v3-to-v4"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v3-to-v4"}},[e._v("#")]),e._v(" From v3 to v4")]),e._v(" "),a("p",[e._v("This release adds opt-in support for the new webSSO on OpenAM 11. Code supporting the old webSSO system has been marked as deprecated and will be removed after the project is compelete.")]),e._v(" "),a("p",[e._v("You will need to update the "),a("code",[e._v("config/nusoa.php")]),e._v(" with additional options in the "),a("code",[e._v("sso")]),e._v(" section. Please "),a("a",{attrs:{href:"https://github.com/NIT-Administrative-Systems/SysDev-laravel-soa/blob/master/config/nusoa.php",target:"_blank",rel:"noopener noreferrer"}},[e._v("review the config file"),a("OutboundLink")],1),e._v(" and add the new options.")]),e._v(" "),a("h3",{attrs:{id:"using-new-websso"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#using-new-websso"}},[e._v("#")]),e._v(" Using New WebSSO")]),e._v(" "),a("p",[e._v("If you are using the provided webSSO login workflow from "),a("code",[e._v("php artisan make:sso")]),e._v(", you can easily swap between the old and new webSSO systems with the following environment variables:")]),e._v(" "),a("div",{staticClass:"language-ini extra-class"},[a("pre",{pre:!0,attrs:{class:"language-ini"}},[a("code",[a("span",{pre:!0,attrs:{class:"token constant"}},[e._v("USE_NEW_WEBSSO_SERVER")]),a("span",{pre:!0,attrs:{class:"token attr-value"}},[a("span",{pre:!0,attrs:{class:"token punctuation"}},[e._v("=")]),e._v("true")]),e._v("\n\n"),a("span",{pre:!0,attrs:{class:"token comment"}},[e._v("# If you're not using new SSO, you do not need to set these.")]),e._v("\n"),a("span",{pre:!0,attrs:{class:"token constant"}},[e._v("WEBSSO_URL_BASE")]),a("span",{pre:!0,attrs:{class:"token attr-value"}},[a("span",{pre:!0,attrs:{class:"token punctuation"}},[e._v("=")]),e._v("https://uat-nusso.it.northwestern.edu")]),e._v("\n"),a("span",{pre:!0,attrs:{class:"token constant"}},[e._v("WEBSSO_API_URL_BASE")]),a("span",{pre:!0,attrs:{class:"token attr-value"}},[a("span",{pre:!0,attrs:{class:"token punctuation"}},[e._v("=")]),e._v("https://northwestern-test.apigee.net/agentless-websso")]),e._v("\n"),a("span",{pre:!0,attrs:{class:"token constant"}},[e._v("WEBSSO_API_KEY")]),a("span",{pre:!0,attrs:{class:"token attr-value"}},[a("span",{pre:!0,attrs:{class:"token punctuation"}},[e._v("=")]),e._v("your-apikey-here")]),e._v("\n")])])]),a("p",[e._v("This is the recommended upgrade path; it allows you to deploy support in advance and easily migrate back and forth as needed.")]),e._v(" "),a("div",{staticClass:"custom-block danger"},[a("p",{staticClass:"custom-block-title"},[e._v("HTTPS Required")]),e._v(" "),a("p",[e._v("The new webSSO sets the "),a("code",[e._v("secure")]),e._v(" flag on its cookie. Your development site "),a("strong",[e._v("must")]),e._v(" be served over HTTPS in order to work.")]),e._v(" "),a("p",[e._v("If you hit a redirect loop when logging in to your app after switching, verify that your site is being served via HTTPS.")])]),e._v(" "),a("p",[e._v("The multi-factor authentication step will be handled by the webSSO server. You do not need Duo integration keys after you have moved to the new webSSO. The "),a("code",[e._v("DUO_ENABLED")]),e._v(" environment variable still controls whether or not you want multi-factor authentication.")]),e._v(" "),a("h3",{attrs:{id:"breaking-changes-3"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#breaking-changes-3"}},[e._v("#")]),e._v(" Breaking Changes")]),e._v(" "),a("p",[e._v("Changes have been made to the underlying "),a("code",[e._v("WebSSO")]),e._v(" class. You only need to worry about that if you are using "),a("code",[e._v("Northwestern\\SysDev\\SOA\\WebSSO")]),e._v(" directly. The "),a("code",[e._v("php artisan make:sso")]),e._v(" login workflow has been updated for you, and you will not need to make any changes to your login/mfa controllers.")]),e._v(" "),a("ul",[a("li",[a("code",[e._v("WebSSO")]),e._v(" is now an interface. If you are doing "),a("code",[e._v("new WebSSO")]),e._v(", please use dependency injection instead.")]),e._v(" "),a("li",[e._v("The "),a("code",[e._v("getNetID()")]),e._v(" previously could return a string or "),a("code",[e._v("false")]),e._v(". It now returns a string or "),a("code",[e._v("null")]),e._v(".")]),e._v(" "),a("li",[e._v("The "),a("code",[e._v("getNetID()")]),e._v(" method has been marked as deprecated. A new "),a("code",[e._v("getUser()")]),e._v(" method replaces this, which returns an object that contains the netID & more information.")])]),e._v(" "),a("h2",{attrs:{id:"from-v2-to-v3"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v2-to-v3"}},[e._v("#")]),e._v(" From v2 to v3")]),e._v(" "),a("p",[e._v("v3 adds the SSO & Duo drop-in auth controllers. You are not required to use this feature, and any webSSO implementations that depend on v2 of package should continue to work. If you want to take advantage of the new webSSO drop-in auth controllers, instructions are available "),a("a",{attrs:{href:"./websso"}},[e._v("on the webSSO page")]),e._v(".")]),e._v(" "),a("p",[e._v("The "),a("code",[e._v("eventhub:webhook:configure")]),e._v(" command now has a "),a("code",[e._v("--force")]),e._v(" flag that will skip the delete confirmation for extra webhooks.")]),e._v(" "),a("h3",{attrs:{id:"breaking-changes-4"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#breaking-changes-4"}},[e._v("#")]),e._v(" Breaking Changes")]),e._v(" "),a("ul",[a("li",[a("p",[e._v("There is one potential breaking change: the "),a("code",[e._v("WebSSO")]),e._v(" class incorrectly depended on "),a("code",[e._v("config('sso.openAmBaseUrl')")]),e._v(".")]),e._v(" "),a("p",[e._v("This has been updated to "),a("code",[e._v("config('nusoa.sso.openAmBaseUrl')")]),e._v(".")]),e._v(" "),a("p",[e._v("If you had a "),a("code",[e._v("config/sso.php")]),e._v(" file as a workaround for this bug, you can probably delete it.")])])]),e._v(" "),a("h2",{attrs:{id:"from-v1-to-v2"}},[a("a",{staticClass:"header-anchor",attrs:{href:"#from-v1-to-v2"}},[e._v("#")]),e._v(" From v1 to v2")]),e._v(" "),a("p",[e._v("The MQ Consumer & Publishers have been replaced by EventHub. This is a radical change, as the underlying messaging service we use has changed.")]),e._v(" "),a("p",[e._v("Please see the "),a("a",{attrs:{href:"./eventhub"}},[e._v("EventHub")]),e._v(" article for usage instructions.")])])}),[],!1,null,null,null);t.default=n.exports}}]);