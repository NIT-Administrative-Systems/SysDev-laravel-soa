(window.webpackJsonp=window.webpackJsonp||[]).push([[12],{378:function(t,s,e){"use strict";e.r(s);var a=e(45),n=Object(a.a)({},(function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[e("h1",{attrs:{id:"websso"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#websso"}},[t._v("#")]),t._v(" WebSSO")]),t._v(" "),e("p",[t._v("The package provides a command that will set up WebSSO, and optionally Duo multi-factor authentication (MFA).")]),t._v(" "),e("p",[t._v("You can use either the traditional Online Passport (handled via agentless SSO with OpenAM/ForgeRock), Azure AD SSO, or both at once.")]),t._v(" "),e("p",[t._v("The package will:")]),t._v(" "),e("ul",[e("li",[t._v("Create an SSO controller in "),e("code",[t._v("App\\Http\\Controllers\\Auth")])]),t._v(" "),e("li",[t._v("Adds named routes to your "),e("code",[t._v("web/routes.php")])])]),t._v(" "),e("p",[t._v("The approach taken is flexible. It is suited for both applications that only use WebSSO "),e("em",[t._v("and")]),t._v(" applications with multiple login methods.")]),t._v(" "),e("p",[t._v("All of the above will still rely on the built-in Laravel "),e("code",[t._v("auth")]),t._v(" middleware.")]),t._v(" "),e("div",{staticClass:"custom-block warning"},[e("p",{staticClass:"custom-block-title"},[t._v("Notes for Advanced Users")]),t._v(" "),e("p",[t._v("Authentication is achieved by logging users into Laravel; once the webSSO session is validated, your user's login session for your application is detached from the webSSO session.")]),t._v(" "),e("p",[t._v("The package does not implement a custom "),e("a",{attrs:{href:"https://laravel.com/docs/5.8/authentication#adding-custom-user-providers",target:"_blank",rel:"noopener noreferrer"}},[t._v("auth provider"),e("OutboundLink")],1),t._v(" and relies on the default database provider for the "),e("code",[t._v("App\\Models\\User")]),t._v(" model.")])]),t._v(" "),e("h2",{attrs:{id:"prerequisites"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#prerequisites"}},[t._v("#")]),t._v(" Prerequisites")]),t._v(" "),e("h3",{attrs:{id:"online-passport-openam-forgerock"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#online-passport-openam-forgerock"}},[t._v("#")]),t._v(" Online Passport (OpenAM/ForgeRock)")]),t._v(" "),e("p",[t._v("You will need an Apigee key with access to the "),e("code",[t._v("IDM - Agentless WebSSO")]),t._v(". The key will include access to the SSO & MFA API. This must be requested through the "),e("a",{attrs:{href:"https://apiserviceregistry.northwestern.edu/",target:"_blank",rel:"noopener noreferrer"}},[t._v("API service registry"),e("OutboundLink")],1),t._v(".")]),t._v(" "),e("p",[t._v("Your application must be served over HTTPS on a "),e("code",[t._v("northwestern.edu")]),t._v(" domain. The SSO cookie ("),e("code",[t._v("nusso")]),t._v(") is flagged as Secure=true; there is no way for Laravel to access the cookie when served over an insecure http connection.")]),t._v(" "),e("h3",{attrs:{id:"azure-ad"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#azure-ad"}},[t._v("#")]),t._v(" Azure AD")]),t._v(" "),e("p",[t._v("You will need to register an application in the "),e("a",{attrs:{href:"https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps",target:"_blank",rel:"noopener noreferrer"}},[t._v("Azure control panel"),e("OutboundLink")],1),t._v(", register your callback URL, and generate a secret. Creating and managing an Azure AD app is [mostly] self-service.")]),t._v(" "),e("p",[t._v("Callback URLs must be served over HTTPS. It does not need to be on any specific domain. Please see "),e("a",{attrs:{href:"https://docs.microsoft.com/en-us/azure/active-directory/develop/reply-url",target:"_blank",rel:"noopener noreferrer"}},[t._v("the Azure documentation"),e("OutboundLink")],1),t._v(" for more information about acceptable callback URLs.")]),t._v(" "),e("p",[t._v("If you wish to use MFA with Azure AD, you must send a ticket to Collab Services asking them to enable it for your application.")]),t._v(" "),e("h2",{attrs:{id:"setting-up-sso"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#setting-up-sso"}},[t._v("#")]),t._v(" Setting up SSO")]),t._v(" "),e("p",[t._v("Getting webSSO working should only take a few minutes. For both Online Passport and Azure AD, start by running:")]),t._v(" "),e("div",{staticClass:"language- extra-class"},[e("pre",{pre:!0,attrs:{class:"language-text"}},[e("code",[t._v("php artisan make:websso\n")])])]),e("h3",{attrs:{id:"online-passport"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#online-passport"}},[t._v("#")]),t._v(" Online Passport")]),t._v(" "),e("p",[t._v("To configure Online Passport, add the following to your "),e("code",[t._v(".env")]),t._v(":")]),t._v(" "),e("div",{staticClass:"language-ini extra-class"},[e("pre",{pre:!0,attrs:{class:"language-ini"}},[e("code",[e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("WEBSSO_API_KEY")]),e("span",{pre:!0,attrs:{class:"token attr-value"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")]),t._v("YOUR_APIGEE_API_KEY")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# Prod would be https://prd-nusso.it.northwestern.edu")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("WEBSSO_URL_BASE")]),e("span",{pre:!0,attrs:{class:"token attr-value"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")]),t._v("https://uat-nusso.it.northwestern.edu")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# Prod would be https://northwestern-prod.apigee.net/agentless-websso")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("WEBSSO_API_URL_BASE")]),e("span",{pre:!0,attrs:{class:"token attr-value"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")]),t._v("https://northwestern-test.apigee.net/agentless-websso")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# Controls whether or not MFA will be required")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# You should enable MFA, unless there's a good reason not to!")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("DUO_ENABLED")]),e("span",{pre:!0,attrs:{class:"token attr-value"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")]),t._v("true")]),t._v("\n")])])]),e("h3",{attrs:{id:"azure-ad-2"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#azure-ad-2"}},[t._v("#")]),t._v(" Azure AD")]),t._v(" "),e("p",[t._v("To configure Azure AD, add the following to your "),e("code",[t._v("config/services.php")]),t._v(":")]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'azure'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'client_id'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("env")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'AZURE_CLIENT_ID'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'client_secret'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("env")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'AZURE_CLIENT_SECRET'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'redirect'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("env")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'AZURE_REDIRECT_URI'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// will be determined at runtime")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n")])])]),e("p",[t._v("At this point, you will need to have created an application in Azure AD and generated a secret for it.")]),t._v(" "),e("p",[t._v("You must register a callback URI in Azure AD as well. The correct URL to register is the route named "),e("code",[t._v("login-oauth-callback")]),t._v(". You can run "),e("code",[t._v("php artisan websso:callback")]),t._v(" to see the whole URL.")]),t._v(" "),e("p",[t._v("Add the client ID and secret to your "),e("code",[t._v(".env")]),t._v(" file:")]),t._v(" "),e("div",{staticClass:"language-ini extra-class"},[e("pre",{pre:!0,attrs:{class:"language-ini"}},[e("code",[e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# This is the 'Application (client) ID' on the app's overview page in Azure")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("AZURE_CLIENT_ID")]),e("span",{pre:!0,attrs:{class:"token attr-value"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")])]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# This is the value of a client secret from the 'Certificates & secrets' page in Azure")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("AZURE_CLIENT_SECRET")]),e("span",{pre:!0,attrs:{class:"token attr-value"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("=")])]),t._v("\n")])])]),e("h3",{attrs:{id:"resolving-users"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#resolving-users"}},[t._v("#")]),t._v(" Resolving Users")]),t._v(" "),e("p",[t._v("Reviewing the setup and adding code to resolve users must be completed for both Online Passport and Azure AD.")]),t._v(" "),e("p",[t._v("Review your "),e("code",[t._v("routes/web.php")]),t._v(". You can adjust the paths, if desired.")]),t._v(" "),e("p",[t._v("Then, open up "),e("code",[t._v("App\\Http\\Controllers\\Auth\\WebSSOController")]),t._v(" and implement the "),e("code",[t._v("findUserByNetID")]),t._v(" method. You may inject any additional dependencies (e.g. "),e("code",[t._v("DirectorySearch")]),t._v(") you need in this method.")]),t._v(" "),e("p",[t._v("It needs to return an object that implements the "),e("code",[t._v("Authenticatable")]),t._v(" interface. The "),e("code",[t._v("App\\User")]),t._v(" model that Laravel comes with satisfies this requirement.")]),t._v(" "),e("p",[t._v("If you return "),e("code",[t._v("null")]),t._v(" from this method, the login will fail. This may be desired in cases where only certain pre-approved users are permitted to log in.")]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token package"}},[t._v("App"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("User")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("protected")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("findUserByNetID")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token class-name type-declaration"}},[t._v("DirectorySearch")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$directory_api")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword type-hint"}},[t._v("string")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$netid")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(":")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("?")]),e("span",{pre:!0,attrs:{class:"token class-name return-type"}},[t._v("Authenticatable")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// If the user exists, they can log in.")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name static-context"}},[t._v("User")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("::")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("where")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'netid'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$netid")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("first")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("!==")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("null")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n\n    "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// If you have a Directory Search API key, you could grab info about them & create a user.")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$directory")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$directory_api")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("lookupNetId")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$netid")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'basic'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name static-context"}},[t._v("User")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("::")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("create")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'name'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$netid")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'email'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$directory")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'mail'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("return")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),e("p",[t._v("You may optionally implement the "),e("code",[t._v("authenticated")]),t._v(" method. If you return a "),e("code",[t._v("redirect()")]),t._v(", it will be followed. Otherwise, the default Laravel behaviour will be used.")]),t._v(" "),e("div",{staticClass:"custom-block tip"},[e("p",{staticClass:"custom-block-title"},[t._v("Azure AD Profile")]),t._v(" "),e("p",[t._v("If you are using Azure AD and want to utilize the profile information like email address & phone number, you can instead implement the "),e("code",[t._v("findUserByOAuthUser")]),t._v(" method.")]),t._v(" "),e("p",[t._v("Similar to "),e("code",[t._v("findUserByNetID")]),t._v(", you can request dependencies from the service container.")]),t._v(" "),e("p",[t._v("This method is only called for Azure AD SSO.")])]),t._v(" "),e("h2",{attrs:{id:"signing-on"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#signing-on"}},[t._v("#")]),t._v(" Signing On")]),t._v(" "),e("p",[t._v("To get your users signing in, you need to redirect them to one of the following routes:")]),t._v(" "),e("table",[e("thead",[e("tr",[e("th",[t._v("Route Name")]),t._v(" "),e("th",[t._v("Type")])])]),t._v(" "),e("tbody",[e("tr",[e("td",[e("code",[t._v("login")])]),t._v(" "),e("td",[t._v("Online Passport")])]),t._v(" "),e("tr",[e("td",[e("code",[t._v("login-oauth-redirect")])]),t._v(" "),e("td",[t._v("Azure AD")])])])]),t._v(" "),e("p",[t._v("This can be either the user clicking a login link, or the "),e("code",[t._v("App\\Http\\Middleware\\Authenticate")]),t._v(" middleware redirecting unauthenticated users to one of these routes.")]),t._v(" "),e("h2",{attrs:{id:"changing-routes"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#changing-routes"}},[t._v("#")]),t._v(" Changing Routes")]),t._v(" "),e("p",[t._v("The default route names "),e("code",[t._v("login")]),t._v(" & "),e("code",[t._v("logout")]),t._v(" are used by the controller traits.")]),t._v(" "),e("p",[t._v("If you want to rename these routes, you will need to override these properties in both controllers.")]),t._v(" "),e("p",[t._v("There is a fourth property, "),e("code",[t._v("logout_return_to_route")]),t._v(", that controls where the WebSSO logout page will send users. In an application that only uses WebSSO for logins, you can leave this "),e("code",[t._v("null")]),t._v(".")]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v(" WebSSOController "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("extends")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("Controller")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("__construct")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token property"}},[t._v("login_route_name")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'login'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token property"}},[t._v("logout_route_name")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'logout'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token property"}},[t._v("logout_return_to_route")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("null")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n\n    "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// . . .")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])]),e("p",[t._v("If you are only using WebSSO to authenticate in your app, this should not be necessary. If you have multiple login methods, you will either need to rename the routes, or update your "),e("code",[t._v("App\\Http\\Middleware\\Authenticate")]),t._v(" to send unauthenticated users to page that lets them choose their login method.")]),t._v(" "),e("h2",{attrs:{id:"api"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#api"}},[t._v("#")]),t._v(" API")]),t._v(" "),e("p",[t._v("The webSSO class will resolve the value of an "),e("code",[t._v("nusso")]),t._v(" cookie into a NetID using the agentless SSO APIs.")]),t._v(" "),e("div",{staticClass:"custom-block tip"},[e("p",{staticClass:"custom-block-title"},[t._v("Unusual Use-cases Only")]),t._v(" "),e("p",[t._v("If you have set up the authentication controllers as detailed in "),e("a",{attrs:{href:"#authentication-flow"}},[t._v("the previous section")]),t._v(", you should not need to use the "),e("code",[t._v("WebSSO")]),t._v(" class yourself.")])]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token php language-php"}},[e("span",{pre:!0,attrs:{class:"token delimiter important"}},[t._v("<?php")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("namespace")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token package"}},[t._v("App"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Http"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Controllers")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token package"}},[t._v("Northwestern"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("SysDev"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("SOA"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("WebSSO")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("MyController")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("extends")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("Controllers")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("login")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token class-name type-declaration"}},[t._v("Request")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$request")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name type-declaration"}},[t._v("WebSSO")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$sso")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// Note that $request->cookie() won't work here.")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// It requires that all cookies be set by Laravel & encrypted with the app's key.")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("//")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// You can add cookie names to the EncryptCookies middleware's $except property to get around that,")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// but for our example, $_COOKIE works just fine.")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$token")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$_COOKIE")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'nusso'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$sso")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("getUser")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$token")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("if")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("==")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("null")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n            "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("redirect")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'sso login page url here'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n\n        "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("dd")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$user")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("getNetid")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// netID as a string with no frills")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])])])}),[],!1,null,null,null);s.default=n.exports}}]);