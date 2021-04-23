(window.webpackJsonp=window.webpackJsonp||[]).push([[10],{377:function(t,s,e){"use strict";e.r(s);var a=e(42),n=Object(a.a)({},(function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[e("h1",{attrs:{id:"eventhub"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#eventhub"}},[t._v("#")]),t._v(" EventHub")]),t._v(" "),e("p",[t._v("This sets up the "),e("a",{attrs:{href:"https://github.com/NIT-Administrative-Systems/SysDev-EventHub-PHP-SDK",target:"_blank",rel:"noopener noreferrer"}},[t._v("EventHub SDK for PHP"),e("OutboundLink")],1),t._v(" for use with Larave, adds commands to manage topics, queues, and webhooks from the console, and the "),e("code",[t._v("eventhub_hmac")]),t._v(" middleware for authenticating webhook-delivered events.")]),t._v(" "),e("p",[t._v("There are three key "),e("code",[t._v(".env")]),t._v(" settings:")]),t._v(" "),e("table",[e("thead",[e("tr",[e("th",[t._v("Setting")]),t._v(" "),e("th",[t._v("Purpose")])])]),t._v(" "),e("tbody",[e("tr",[e("td",[e("code",[t._v("EVENT_HUB_BASE_URL")])]),t._v(" "),e("td",[t._v("The Apigee base URL, e.g. "),e("code",[t._v("https://northwestern-dev.apigee.net")])])]),t._v(" "),e("tr",[e("td",[e("code",[t._v("EVENT_HUB_API_KEY")])]),t._v(" "),e("td",[t._v("Your Apigee API key")])]),t._v(" "),e("tr",[e("td",[e("code",[t._v("EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET")])]),t._v(" "),e("td",[t._v("Only applicable for consuming messages. Set this to a random string, e.g. "),e("code",[t._v("base64_encode(random_bytes(32))")])])])])]),t._v(" "),e("p",[t._v("A number of other settings are available to control the HMAC security options, but they are set to reasonable defaults. See the "),e("code",[t._v("config/nusoa.php")]),t._v(" file if you are interested in these.")]),t._v(" "),e("p",[t._v("You can review your available EventHub queues & topics from the console:")]),t._v(" "),e("div",{staticClass:"language- extra-class"},[e("pre",{pre:!0,attrs:{class:"language-text"}},[e("code",[t._v("php artisan eventhub:queue:status\nphp artisan eventhub:topic:status\n")])])]),e("h2",{attrs:{id:"sending-messages"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#sending-messages"}},[t._v("#")]),t._v(" Sending Messages")]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token php language-php"}},[e("span",{pre:!0,attrs:{class:"token delimiter important"}},[t._v("<?php")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("namespace")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token package"}},[t._v("App"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Http"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Controllers")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token package"}},[t._v("Northwestern"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("SysDev"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("SOA"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("EventHub")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("MyController")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("extends")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("Controllers")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("save")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token class-name type-declaration"}},[t._v("Request")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$request")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name class-name-fully-qualified type-declaration"}},[t._v("EventHub"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Topic")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$api")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$message_id")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$api")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("writeJsonMessage")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'my-team.a-topic-name'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),t._v("\n            "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'id'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token number"}},[t._v("123")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])]),e("h2",{attrs:{id:"consuming-messages"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#consuming-messages"}},[t._v("#")]),t._v(" Consuming Messages")]),t._v(" "),e("p",[t._v("The best way to consume EventHub messages is by setting up webhooks & allowing EventHub to deliver messages to your application in real-time.")]),t._v(" "),e("p",[t._v("This package makes receiving webhooks easy: register a queue name to a route, apply the "),e("code",[t._v("eventhub_hmac")]),t._v(" middleware to its controller, and do something with the message:")]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token php language-php"}},[e("span",{pre:!0,attrs:{class:"token delimiter important"}},[t._v("<?php")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// In your `routes/web.php`")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token class-name static-context"}},[t._v("Route")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("::")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("post")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'events/netid-update'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'NetIdUpdateController'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("eventHubWebhook")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'my-team.ldap.netid.term'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token class-name static-context"}},[t._v("Route")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("::")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("post")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'events/employee-update'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'EmployeeUpdateController'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("eventHubWebhook")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'my-team.employee.updates'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'contentType'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'application/xml'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// for XML messages")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// App\\Http\\Controllers\\NetIdUpdateController")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("use")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token package"}},[t._v("Illuminate"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Http"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Request")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("NetIdUpdateController")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("extends")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token class-name"}},[t._v("Controller")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("__construct")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$this")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("middleware")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'eventhub_hmac'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n\n    "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("public")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("function")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("__invoke")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token class-name type-declaration"}},[t._v("Request")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$request")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("{")]),t._v("\n        "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$raw_json")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$request")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("getContent")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n\n        "),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("// . . .")]),t._v("\n    "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("}")]),t._v("\n")])])])]),e("p",[t._v("Finally, run "),e("code",[t._v("php artisan eventhub:webhook:configure")]),t._v(". It will read through your routes and make the API calls to EventHub that (re)configure all of your webhooks. If you delete a registration, the "),e("code",[t._v("eventhub:webhook:configure")]),t._v(" command will ask you if you'd like to delete the webhook config.")]),t._v(" "),e("div",{staticClass:"custom-block tip"},[e("p",{staticClass:"custom-block-title"},[t._v("App Deployments")]),t._v(" "),e("p",[t._v("It is recommended that you pause webhook deliveries when you deploy updates to your application.")]),t._v(" "),e("div",{staticClass:"language-sh extra-class"},[e("pre",{pre:!0,attrs:{class:"language-sh"}},[e("code",[t._v("php artisan eventhub:webhook:toggle pause\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# Do the deployment ...")]),t._v("\n\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# Running configure after deploying makes sure the settings")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token comment"}},[t._v("# in EventHub still match your registered webhook routes.")]),t._v("\nphp artisan eventhub:webhook:configure --force\nphp artisan eventhub:webhook:toggle unpause\n")])])]),e("p",[t._v("EventHub will retry failed message deliveries for time time if you have forgotten to pause. See the EventHub documentation for more information on delivery re-tries.")])]),t._v(" "),e("h2",{attrs:{id:"eventhub-artisan-commands"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#eventhub-artisan-commands"}},[t._v("#")]),t._v(" EventHub Artisan Commands")]),t._v(" "),e("p",[t._v("The following artisan commands will be available when you install this package.")]),t._v(" "),e("p",[t._v("You can run these with "),e("code",[t._v("php artisan <command>")]),t._v(".")]),t._v(" "),e("table",[e("thead",[e("tr",[e("th",[t._v("Command")]),t._v(" "),e("th",[t._v("Purpose")])])]),t._v(" "),e("tbody",[e("tr",[e("td",[t._v("eventhub:queue:status")]),t._v(" "),e("td",[t._v("Show all the queues you can read from & some statistics")])]),t._v(" "),e("tr",[e("td",[t._v("eventhub:topic:status")]),t._v(" "),e("td",[t._v("Show all the topics you can write to & who is subscribed")])]),t._v(" "),e("tr",[e("td",[t._v("eventhub:webhook:status")]),t._v(" "),e("td",[t._v("Show all your configured webhooks")])]),t._v(" "),e("tr",[e("td",[t._v("eventhub:webhook:toggle pause")]),t._v(" "),e("td",[t._v("Pause all webhooks. Optionally, you can pass a list of queue names to pause just those.")])]),t._v(" "),e("tr",[e("td",[t._v("eventhub:webhook:toggle unpause")]),t._v(" "),e("td",[t._v("Unpause all webhooks. Optionally, you can pass a list of queue names to unpause just those.")])]),t._v(" "),e("tr",[e("td",[t._v("eventhub:webhook:configure")]),t._v(" "),e("td",[t._v("Publishes the webhook delivery routes configured in your route files with EventHub")])])])]),t._v(" "),e("h2",{attrs:{id:"test-messages"}},[e("a",{staticClass:"header-anchor",attrs:{href:"#test-messages"}},[t._v("#")]),t._v(" Test Messages")]),t._v(" "),e("p",[t._v("In the dev & test environments, you should have permission to write messages to the queues you're subscribed to via "),e("code",[t._v("POST /v1/event-hub/queue/your-queue-name")]),t._v(".")]),t._v(" "),e("p",[t._v("You can verify your message processing code is working from the tinker console:")]),t._v(" "),e("div",{staticClass:"language-php extra-class"},[e("pre",{pre:!0,attrs:{class:"language-php"}},[e("code",[e("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">>")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$q_api")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("resolve")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token class-name class-name-fully-qualified static-context"}},[e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Northwestern"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("SysDev"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("SOA"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("EventHub"),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("\\")]),t._v("Queue")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("::")]),e("span",{pre:!0,attrs:{class:"token keyword"}},[t._v("class")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(";")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" Northwestern\\SysDev\\"),e("span",{pre:!0,attrs:{class:"token constant"}},[t._v("SOA")]),t._v("\\EventHub\\Queue\n\n"),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">>")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v(">")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token variable"}},[t._v("$q_api")]),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("->")]),e("span",{pre:!0,attrs:{class:"token function"}},[t._v("sendTestJsonMessage")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("(")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'sysdev.queue.a'")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(",")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("[")]),e("span",{pre:!0,attrs:{class:"token string single-quoted-string"}},[t._v("'application_id'")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token number"}},[t._v("123")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v("]")]),e("span",{pre:!0,attrs:{class:"token punctuation"}},[t._v(")")]),t._v("\n"),e("span",{pre:!0,attrs:{class:"token operator"}},[t._v("=>")]),t._v(" "),e("span",{pre:!0,attrs:{class:"token string double-quoted-string"}},[t._v('"ID:052d83908c43-35873-1545317905819-1:1:3:1:1"')]),t._v("\n")])])]),e("p",[t._v("When using webhooks, the test message should be delivered to your app immediately.")])])}),[],!1,null,null,null);s.default=n.exports}}]);