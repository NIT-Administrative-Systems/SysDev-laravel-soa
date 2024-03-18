import{_ as a,r as t,o,c as p,a as n,b as s,d as i,e as l}from"./app-uDr87RQt.js";const c={},u=n("h1",{id:"eventhub",tabindex:"-1"},[n("a",{class:"header-anchor",href:"#eventhub"},[n("span",null,"EventHub")])],-1),r={href:"https://github.com/NIT-Administrative-Systems/SysDev-EventHub-PHP-SDK",target:"_blank",rel:"noopener noreferrer"},d=n("code",null,"eventhub_hmac",-1),m=l(`<p>There are three key <code>.env</code> settings:</p><table><thead><tr><th>Setting</th><th>Purpose</th></tr></thead><tbody><tr><td><code>EVENT_HUB_BASE_URL</code></td><td>The Apigee base URL, e.g. <code>https://northwestern-dev.apigee.net</code></td></tr><tr><td><code>EVENT_HUB_API_KEY</code></td><td>Your Apigee API key</td></tr><tr><td><code>EVENT_HUB_HMAC_VERIFICATION_SHARED_SECRET</code></td><td>Only applicable for consuming messages. Set this to a random string, e.g. <code>base64_encode(random_bytes(32))</code></td></tr></tbody></table><p>A number of other settings are available to control the HMAC security options, but they are set to reasonable defaults. See the <code>config/nusoa.php</code> file if you are interested in these.</p><p>You can review your available EventHub queues &amp; topics from the console:</p><div class="language-text line-numbers-mode" data-ext="text" data-title="text"><pre class="language-text"><code>php artisan eventhub:queue:status
php artisan eventhub:topic:status
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div></div></div><h2 id="sending-messages" tabindex="-1"><a class="header-anchor" href="#sending-messages"><span>Sending Messages</span></a></h2><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token php language-php"><span class="token delimiter important">&lt;?php</span>

<span class="token keyword">namespace</span> <span class="token package">App<span class="token punctuation">\\</span>Http<span class="token punctuation">\\</span>Controllers</span><span class="token punctuation">;</span>

<span class="token keyword">use</span> <span class="token package">Northwestern<span class="token punctuation">\\</span>SysDev<span class="token punctuation">\\</span>SOA<span class="token punctuation">\\</span>EventHub</span><span class="token punctuation">;</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">MyController</span> <span class="token keyword">extends</span> <span class="token class-name">Controllers</span>
<span class="token punctuation">{</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">save</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Request</span> <span class="token variable">$request</span><span class="token punctuation">,</span> <span class="token class-name class-name-fully-qualified type-declaration">EventHub<span class="token punctuation">\\</span>Topic</span> <span class="token variable">$api</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
        <span class="token variable">$message_id</span> <span class="token operator">=</span> <span class="token variable">$api</span><span class="token operator">-&gt;</span><span class="token function">writeJsonMessage</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;my-team.a-topic-name&#39;</span><span class="token punctuation">,</span> <span class="token punctuation">[</span>
            <span class="token string single-quoted-string">&#39;id&#39;</span> <span class="token operator">=&gt;</span> <span class="token number">123</span><span class="token punctuation">,</span>
        <span class="token punctuation">]</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</span></code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h2 id="consuming-messages" tabindex="-1"><a class="header-anchor" href="#consuming-messages"><span>Consuming Messages</span></a></h2><p>The best way to consume EventHub messages is by setting up webhooks &amp; allowing EventHub to deliver messages to your application in real-time.</p><p>This package makes receiving webhooks easy: register a queue name to a route, apply the <code>eventhub_hmac</code> middleware to its controller, and do something with the message:</p><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token php language-php"><span class="token delimiter important">&lt;?php</span>

<span class="token comment">// In your \`routes/web.php\`</span>
<span class="token class-name static-context">Route</span><span class="token operator">::</span><span class="token function">post</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;events/netid-update&#39;</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">&#39;NetIdUpdateController&#39;</span><span class="token punctuation">)</span><span class="token operator">-&gt;</span><span class="token function">eventHubWebhook</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;my-team.ldap.netid.term&#39;</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
<span class="token class-name static-context">Route</span><span class="token operator">::</span><span class="token function">post</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;events/employee-update&#39;</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">&#39;EmployeeUpdateController&#39;</span><span class="token punctuation">)</span><span class="token operator">-&gt;</span><span class="token function">eventHubWebhook</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;my-team.employee.updates&#39;</span><span class="token punctuation">,</span> <span class="token punctuation">[</span><span class="token string single-quoted-string">&#39;contentType&#39;</span> <span class="token operator">=&gt;</span> <span class="token string single-quoted-string">&#39;application/xml&#39;</span><span class="token punctuation">]</span><span class="token punctuation">)</span><span class="token punctuation">;</span> <span class="token comment">// for XML messages</span>

<span class="token comment">// App\\Http\\Controllers\\NetIdUpdateController</span>
<span class="token keyword">use</span> <span class="token package">Illuminate<span class="token punctuation">\\</span>Http<span class="token punctuation">\\</span>Request</span><span class="token punctuation">;</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">NetIdUpdateController</span> <span class="token keyword">extends</span> <span class="token class-name">Controller</span>
<span class="token punctuation">{</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">__construct</span><span class="token punctuation">(</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
        <span class="token variable">$this</span><span class="token operator">-&gt;</span><span class="token function">middleware</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;eventhub_hmac&#39;</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span>

    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">__invoke</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Request</span> <span class="token variable">$request</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
        <span class="token variable">$raw_json</span> <span class="token operator">=</span> <span class="token variable">$request</span><span class="token operator">-&gt;</span><span class="token function">getContent</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">;</span>

        <span class="token comment">// . . .</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</span></code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>Finally, run <code>php artisan eventhub:webhook:configure</code>. It will read through your routes and make the API calls to EventHub that (re)configure all of your webhooks. If you delete a registration, the <code>eventhub:webhook:configure</code> command will ask you if you&#39;d like to delete the webhook config.</p><div class="custom-container tip"><p class="custom-container-title">App Deployments</p><p>It is recommended that you pause webhook deliveries when you deploy updates to your application.</p><div class="language-bash line-numbers-mode" data-ext="sh" data-title="sh"><pre class="language-bash"><code>php artisan eventhub:webhook:toggle pause

<span class="token comment"># Do the deployment ...</span>

<span class="token comment"># Running configure after deploying makes sure the settings</span>
<span class="token comment"># in EventHub still match your registered webhook routes.</span>
php artisan eventhub:webhook:configure <span class="token parameter variable">--force</span>
php artisan eventhub:webhook:toggle unpause
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>EventHub will retry failed message deliveries for time time if you have forgotten to pause. See the EventHub documentation for more information on delivery re-tries.</p></div><h2 id="eventhub-artisan-commands" tabindex="-1"><a class="header-anchor" href="#eventhub-artisan-commands"><span>EventHub Artisan Commands</span></a></h2><p>The following artisan commands will be available when you install this package.</p><p>You can run these with <code>php artisan &lt;command&gt;</code>.</p><table><thead><tr><th>Command</th><th>Purpose</th></tr></thead><tbody><tr><td>eventhub:queue:status</td><td>Show all the queues you can read from &amp; some statistics</td></tr><tr><td>eventhub:topic:status</td><td>Show all the topics you can write to &amp; who is subscribed</td></tr><tr><td>eventhub:webhook:status</td><td>Show all your configured webhooks</td></tr><tr><td>eventhub:webhook:toggle pause</td><td>Pause all webhooks. Optionally, you can pass a list of queue names to pause just those.</td></tr><tr><td>eventhub:webhook:toggle unpause</td><td>Unpause all webhooks. Optionally, you can pass a list of queue names to unpause just those.</td></tr><tr><td>eventhub:webhook:configure</td><td>Publishes the webhook delivery routes configured in your route files with EventHub</td></tr><tr><td>eventhub:dlq:restore-messages</td><td>Moves messages from the DLQ back to the original queue for re-processing.</td></tr></tbody></table><h2 id="test-messages" tabindex="-1"><a class="header-anchor" href="#test-messages"><span>Test Messages</span></a></h2><p>In the dev &amp; test environments, you should have permission to write messages to the queues you&#39;re subscribed to via <code>POST /v1/event-hub/queue/your-queue-name</code>.</p><p>You can verify your message processing code is working from the tinker console:</p><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token operator">&gt;&gt;</span><span class="token operator">&gt;</span> <span class="token variable">$q_api</span> <span class="token operator">=</span> <span class="token function">resolve</span><span class="token punctuation">(</span><span class="token class-name class-name-fully-qualified static-context"><span class="token punctuation">\\</span>Northwestern<span class="token punctuation">\\</span>SysDev<span class="token punctuation">\\</span>SOA<span class="token punctuation">\\</span>EventHub<span class="token punctuation">\\</span>Queue</span><span class="token operator">::</span><span class="token keyword">class</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
<span class="token operator">=&gt;</span> Northwestern\\SysDev\\<span class="token constant">SOA</span>\\EventHub\\Queue

<span class="token operator">&gt;&gt;</span><span class="token operator">&gt;</span> <span class="token variable">$q_api</span><span class="token operator">-&gt;</span><span class="token function">sendTestJsonMessage</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;sysdev.queue.a&#39;</span><span class="token punctuation">,</span> <span class="token punctuation">[</span><span class="token string single-quoted-string">&#39;application_id&#39;</span> <span class="token operator">=&gt;</span> <span class="token number">123</span><span class="token punctuation">]</span><span class="token punctuation">)</span>
<span class="token operator">=&gt;</span> <span class="token string double-quoted-string">&quot;ID:052d83908c43-35873-1545317905819-1:1:3:1:1&quot;</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>When using webhooks, the test message should be delivered to your app immediately.</p>`,22);function v(k,h){const e=t("ExternalLinkIcon");return o(),p("div",null,[u,n("p",null,[s("This sets up the "),n("a",r,[s("EventHub SDK for PHP"),i(e)]),s(" for use with Larave, adds commands to manage topics, queues, and webhooks from the console, and the "),d,s(" middleware for authenticating webhook-delivered events.")]),m])}const g=a(c,[["render",v],["__file","eventhub.html.vue"]]),f=JSON.parse('{"path":"/eventhub.html","title":"EventHub","lang":"en-US","frontmatter":{},"headers":[{"level":2,"title":"Sending Messages","slug":"sending-messages","link":"#sending-messages","children":[]},{"level":2,"title":"Consuming Messages","slug":"consuming-messages","link":"#consuming-messages","children":[]},{"level":2,"title":"EventHub Artisan Commands","slug":"eventhub-artisan-commands","link":"#eventhub-artisan-commands","children":[]},{"level":2,"title":"Test Messages","slug":"test-messages","link":"#test-messages","children":[]}],"git":{"updatedTime":1710792783000,"contributors":[{"name":"dependabot[bot]","email":"49699333+dependabot[bot]@users.noreply.github.com","commits":1}]},"filePathRelative":"eventhub.md","excerpt":"\\n<p>This sets up the <a href=\\"https://github.com/NIT-Administrative-Systems/SysDev-EventHub-PHP-SDK\\" target=\\"_blank\\" rel=\\"noopener noreferrer\\">EventHub SDK for PHP</a> for use with Larave, adds commands to manage topics, queues, and webhooks from the console, and the <code>eventhub_hmac</code> middleware for authenticating webhook-delivered events.</p>"}');export{g as comp,f as data};