import{_ as o,r as i,o as l,c as p,a as n,b as s,d as a,e as t}from"./app-uDr87RQt.js";const c={},r=t('<h1 id="websso" tabindex="-1"><a class="header-anchor" href="#websso"><span>WebSSO</span></a></h1><p>The package provides a command that will set up WebSSO, and optionally Duo multi-factor authentication (MFA).</p><p>You can use either the traditional Online Passport (handled via agentless SSO with OpenAM/ForgeRock), Azure AD SSO, or both at once.</p><p>The package will:</p><ul><li>Create an SSO controller in <code>App\\Http\\Controllers\\Auth</code></li><li>Adds named routes to your <code>web/routes.php</code></li></ul><p>The approach taken is flexible. It is suited for both applications that only use WebSSO <em>and</em> applications with multiple login methods.</p><p>All of the above will still rely on the built-in Laravel <code>auth</code> middleware.</p>',7),u={class:"custom-container warning"},d=n("p",{class:"custom-container-title"},"Notes for Advanced Users",-1),h=n("p",null,"Authentication is achieved by logging users into Laravel; once the webSSO session is validated, your user's login session for your application is detached from the webSSO session.",-1),k={href:"https://laravel.com/docs/5.8/authentication#adding-custom-user-providers",target:"_blank",rel:"noopener noreferrer"},v=n("code",null,"App\\Models\\User",-1),m=n("h2",{id:"prerequisites",tabindex:"-1"},[n("a",{class:"header-anchor",href:"#prerequisites"},[n("span",null,"Prerequisites")])],-1),g=n("h3",{id:"online-passport-openam-forgerock",tabindex:"-1"},[n("a",{class:"header-anchor",href:"#online-passport-openam-forgerock"},[n("span",null,"Online Passport (OpenAM/ForgeRock)")])],-1),b=n("code",null,"IDM - Agentless WebSSO",-1),f={href:"https://apiserviceregistry.northwestern.edu/",target:"_blank",rel:"noopener noreferrer"},y=n("p",null,[s("Your application must be served over HTTPS on a "),n("code",null,"northwestern.edu"),s(" domain. The SSO cookie ("),n("code",null,"nusso"),s(") is flagged as Secure=true; there is no way for Laravel to access the cookie when served over an insecure http connection.")],-1),w=n("h3",{id:"azure-ad",tabindex:"-1"},[n("a",{class:"header-anchor",href:"#azure-ad"},[n("span",null,"Azure AD")])],-1),A={href:"https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps",target:"_blank",rel:"noopener noreferrer"},_={href:"https://docs.microsoft.com/en-us/azure/active-directory/develop/reply-url",target:"_blank",rel:"noopener noreferrer"},S=t(`<p>If you wish to use MFA with Azure AD, you must send a ticket to Collab Services asking them to enable it for your application. You do not need to make any configuration or code changes to enable it.</p><p>The default Laravel cache driver will be used to store Microsoft&#39;s JWT signing keys. These are loaded on demand and stored for a few minutes.</p><h2 id="setting-up-sso" tabindex="-1"><a class="header-anchor" href="#setting-up-sso"><span>Setting up SSO</span></a></h2><p>Getting webSSO working should only take a few minutes. For both Online Passport and Azure AD, start by running:</p><div class="language-text line-numbers-mode" data-ext="text" data-title="text"><pre class="language-text"><code>php artisan make:websso
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div></div></div><h3 id="online-passport" tabindex="-1"><a class="header-anchor" href="#online-passport"><span>Online Passport</span></a></h3><p>To configure Online Passport, add the following to your <code>.env</code>:</p><div class="language-ini line-numbers-mode" data-ext="ini" data-title="ini"><pre class="language-ini"><code><span class="token key attr-name">WEBSSO_API_KEY</span><span class="token punctuation">=</span><span class="token value attr-value">YOUR_APIGEE_API_KEY</span>

<span class="token comment"># Prod would be https://prd-nusso.it.northwestern.edu</span>
<span class="token key attr-name">WEBSSO_URL_BASE</span><span class="token punctuation">=</span><span class="token value attr-value">https://uat-nusso.it.northwestern.edu</span>

<span class="token comment"># Prod would be https://northwestern-prod.apigee.net/agentless-websso</span>
<span class="token key attr-name">WEBSSO_API_URL_BASE</span><span class="token punctuation">=</span><span class="token value attr-value">https://northwestern-test.apigee.net/agentless-websso</span>

<span class="token comment"># Controls whether or not MFA will be required</span>
<span class="token comment"># You should enable MFA, unless there&#39;s a good reason not to!</span>
<span class="token key attr-name">DUO_ENABLED</span><span class="token punctuation">=</span><span class="token value attr-value">true</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="azure-ad-1" tabindex="-1"><a class="header-anchor" href="#azure-ad-1"><span>Azure AD</span></a></h3><p>To configure Azure AD, add the following to your <code>config/services.php</code>:</p><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token string single-quoted-string">&#39;northwestern-azure&#39;</span> <span class="token operator">=&gt;</span> <span class="token punctuation">[</span>
    <span class="token string single-quoted-string">&#39;client_id&#39;</span> <span class="token operator">=&gt;</span> <span class="token function">env</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;AZURE_CLIENT_ID&#39;</span><span class="token punctuation">)</span><span class="token punctuation">,</span>
    <span class="token string single-quoted-string">&#39;client_secret&#39;</span> <span class="token operator">=&gt;</span> <span class="token function">env</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;AZURE_CLIENT_SECRET&#39;</span><span class="token punctuation">)</span><span class="token punctuation">,</span>
    <span class="token string single-quoted-string">&#39;redirect&#39;</span> <span class="token operator">=&gt;</span> <span class="token function">env</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;AZURE_REDIRECT_URI&#39;</span><span class="token punctuation">)</span> <span class="token comment">// will be determined at runtime</span>
    
    <span class="token doc-comment comment">/**
     * These parameters can be changed for multi-tenant app registrations.
     * They will default to Northwestern&#39;s tenant ID and our domain hint.
     * 
     * In most use-cases, these will not be used.
     */</span> 
    <span class="token comment">// &#39;tenant&#39; =&gt; &#39;common&#39;,</span>
    <span class="token comment">// &#39;domain_hint&#39; =&gt; null,</span>
<span class="token punctuation">]</span><span class="token punctuation">,</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>At this point, you will need to have created an application in Azure AD and generated a secret for it.</p><p>You must register a callback URI in Azure AD as well. The correct URL to register is the route named <code>login-oauth-callback</code>. You can run <code>php artisan websso:callback</code> to see the whole URL.</p><p>Add the client ID and secret to your <code>.env</code> file:</p><div class="language-ini line-numbers-mode" data-ext="ini" data-title="ini"><pre class="language-ini"><code><span class="token comment"># This is the &#39;Application (client) ID&#39; on the app&#39;s overview page in Azure</span>
<span class="token key attr-name">AZURE_CLIENT_ID</span><span class="token punctuation">=</span>

<span class="token comment"># This is the value of a client secret from the &#39;Certificates &amp; secrets&#39; page in Azure</span>
<span class="token key attr-name">AZURE_CLIENT_SECRET</span><span class="token punctuation">=</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="resolving-users" tabindex="-1"><a class="header-anchor" href="#resolving-users"><span>Resolving Users</span></a></h3><p>Reviewing the setup and adding code to resolve users must be completed for both Online Passport and Azure AD.</p><p>Review your <code>routes/web.php</code>. You can adjust the paths, if desired.</p><p>Then, open up <code>App\\Http\\Controllers\\Auth\\WebSSOController</code> and implement the <code>findUserByNetID</code> method. You may inject any additional dependencies (e.g. <code>DirectorySearch</code>) you need in this method.</p><p>It needs to return an object that implements the <code>Authenticatable</code> interface. The <code>App\\User</code> model that Laravel comes with satisfies this requirement.</p><p>If you return <code>null</code> from this method, the login will fail. This may be desired in cases where only certain pre-approved users are permitted to log in.</p><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token keyword">use</span> <span class="token package">App<span class="token punctuation">\\</span>User</span><span class="token punctuation">;</span>

<span class="token keyword">protected</span> <span class="token keyword">function</span> <span class="token function-definition function">findUserByNetID</span><span class="token punctuation">(</span><span class="token class-name type-declaration">DirectorySearch</span> <span class="token variable">$directory_api</span><span class="token punctuation">,</span> <span class="token keyword type-hint">string</span> <span class="token variable">$netid</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token operator">?</span><span class="token class-name return-type">Authenticatable</span>
<span class="token punctuation">{</span>
    <span class="token comment">// If the user exists, they can log in.</span>
    <span class="token variable">$user</span> <span class="token operator">=</span> <span class="token class-name static-context">User</span><span class="token operator">::</span><span class="token function">where</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;netid&#39;</span><span class="token punctuation">,</span> <span class="token variable">$netid</span><span class="token punctuation">)</span><span class="token operator">-&gt;</span><span class="token function">first</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
    <span class="token keyword">if</span> <span class="token punctuation">(</span><span class="token variable">$user</span> <span class="token operator">!==</span> <span class="token constant">null</span><span class="token punctuation">)</span> <span class="token punctuation">{</span>
        <span class="token keyword">return</span> <span class="token variable">$user</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span>

    <span class="token comment">// If you have a Directory Search API key, you could grab info about them &amp; create a user.</span>
    <span class="token variable">$directory</span> <span class="token operator">=</span> <span class="token variable">$directory_api</span><span class="token operator">-&gt;</span><span class="token function">lookupNetId</span><span class="token punctuation">(</span><span class="token variable">$netid</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">&#39;basic&#39;</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
    <span class="token variable">$user</span> <span class="token operator">=</span> <span class="token class-name static-context">User</span><span class="token operator">::</span><span class="token function">create</span><span class="token punctuation">(</span><span class="token punctuation">[</span>
        <span class="token string single-quoted-string">&#39;name&#39;</span> <span class="token operator">=&gt;</span> <span class="token variable">$netid</span><span class="token punctuation">,</span>
        <span class="token string single-quoted-string">&#39;email&#39;</span> <span class="token operator">=&gt;</span> <span class="token variable">$directory</span><span class="token punctuation">[</span><span class="token string single-quoted-string">&#39;mail&#39;</span><span class="token punctuation">]</span><span class="token punctuation">,</span>
    <span class="token punctuation">]</span><span class="token punctuation">)</span><span class="token punctuation">;</span>

    <span class="token keyword">return</span> <span class="token variable">$user</span><span class="token punctuation">;</span>
<span class="token punctuation">}</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>You may optionally implement the <code>authenticated</code> method. If you return a <code>redirect()</code>, it will be followed. Otherwise, the default Laravel behaviour will be used.</p><div class="custom-container tip"><p class="custom-container-title">Azure AD Profile</p><p>If you are using Azure AD and want to utilize the profile information like email address &amp; phone number, you can instead implement the <code>findUserByOAuthUser</code> method.</p><p>Similar to <code>findUserByNetID</code>, you can request dependencies from the service container.</p><p>This method is only called for Azure AD SSO.</p></div><h2 id="signing-on" tabindex="-1"><a class="header-anchor" href="#signing-on"><span>Signing On</span></a></h2><p>To get your users signing in, you need to redirect them to one of the following routes:</p><table><thead><tr><th>Route Name</th><th>Type</th></tr></thead><tbody><tr><td><code>login</code></td><td>Online Passport</td></tr><tr><td><code>login-oauth-redirect</code></td><td>Azure AD</td></tr></tbody></table><p>This can be either the user clicking a login link, or the <code>App\\Http\\Middleware\\Authenticate</code> middleware redirecting unauthenticated users to one of these routes.</p><h2 id="changing-routes" tabindex="-1"><a class="header-anchor" href="#changing-routes"><span>Changing Routes</span></a></h2><p>The default route names <code>login</code> &amp; <code>logout</code> are used by the controller traits.</p><p>If you want to rename these routes, you will need to override these properties in both controllers.</p><p>There is a fourth property, <code>logout_return_to_route</code>, that controls where the WebSSO logout page will send users. In an application that only uses WebSSO for logins, you can leave this <code>null</code>.</p><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token keyword">class</span> <span class="token class-name-definition class-name">WebSSOController</span> <span class="token keyword">extends</span> <span class="token class-name">Controller</span>
<span class="token punctuation">{</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">__construct</span><span class="token punctuation">(</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
        <span class="token variable">$this</span><span class="token operator">-&gt;</span><span class="token property">login_route_name</span> <span class="token operator">=</span> <span class="token string single-quoted-string">&#39;login&#39;</span><span class="token punctuation">;</span>
        <span class="token variable">$this</span><span class="token operator">-&gt;</span><span class="token property">logout_route_name</span> <span class="token operator">=</span> <span class="token string single-quoted-string">&#39;logout&#39;</span><span class="token punctuation">;</span>

        <span class="token variable">$this</span><span class="token operator">-&gt;</span><span class="token property">logout_return_to_route</span> <span class="token operator">=</span> <span class="token constant">null</span><span class="token punctuation">;</span>
    <span class="token punctuation">}</span>

    <span class="token comment">// . . .</span>
<span class="token punctuation">}</span>
</code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>If you are only using WebSSO to authenticate in your app, this should not be necessary. If you have multiple login methods, you will either need to rename the routes, or update your <code>App\\Http\\Middleware\\Authenticate</code> to send unauthenticated users to page that lets them choose their login method.</p><h2 id="api" tabindex="-1"><a class="header-anchor" href="#api"><span>API</span></a></h2><p>The webSSO class will resolve the value of an <code>nusso</code> cookie into a NetID using the agentless SSO APIs.</p><div class="custom-container tip"><p class="custom-container-title">Unusual Use-cases Only</p><p>If you have set up the authentication controllers as detailed in <a href="#authentication-flow">the previous section</a>, you should not need to use the <code>WebSSO</code> class yourself.</p></div><div class="language-php line-numbers-mode" data-ext="php" data-title="php"><pre class="language-php"><code><span class="token php language-php"><span class="token delimiter important">&lt;?php</span>

<span class="token keyword">namespace</span> <span class="token package">App<span class="token punctuation">\\</span>Http<span class="token punctuation">\\</span>Controllers</span><span class="token punctuation">;</span>

<span class="token keyword">use</span> <span class="token package">Northwestern<span class="token punctuation">\\</span>SysDev<span class="token punctuation">\\</span>SOA<span class="token punctuation">\\</span>WebSSO</span><span class="token punctuation">;</span>

<span class="token keyword">class</span> <span class="token class-name-definition class-name">MyController</span> <span class="token keyword">extends</span> <span class="token class-name">Controllers</span>
<span class="token punctuation">{</span>
    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">login</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Request</span> <span class="token variable">$request</span><span class="token punctuation">,</span> <span class="token class-name type-declaration">WebSSO</span> <span class="token variable">$sso</span><span class="token punctuation">)</span>
    <span class="token punctuation">{</span>
        <span class="token comment">// Note that $request-&gt;cookie() won&#39;t work here.</span>
        <span class="token comment">// It requires that all cookies be set by Laravel &amp; encrypted with the app&#39;s key.</span>
        <span class="token comment">//</span>
        <span class="token comment">// You can add cookie names to the EncryptCookies middleware&#39;s $except property to get around that,</span>
        <span class="token comment">// but for our example, $_COOKIE works just fine.</span>
        <span class="token variable">$token</span> <span class="token operator">=</span> <span class="token variable">$_COOKIE</span><span class="token punctuation">[</span><span class="token string single-quoted-string">&#39;nusso&#39;</span><span class="token punctuation">]</span><span class="token punctuation">;</span>

        <span class="token variable">$user</span> <span class="token operator">=</span> <span class="token variable">$sso</span><span class="token operator">-&gt;</span><span class="token function">getUser</span><span class="token punctuation">(</span><span class="token variable">$token</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
        <span class="token keyword">if</span> <span class="token punctuation">(</span><span class="token variable">$user</span> <span class="token operator">==</span> <span class="token constant">null</span><span class="token punctuation">)</span> <span class="token punctuation">{</span>
            <span class="token function">redirect</span><span class="token punctuation">(</span><span class="token string single-quoted-string">&#39;sso login page url here&#39;</span><span class="token punctuation">)</span><span class="token punctuation">;</span>
        <span class="token punctuation">}</span>

        <span class="token function">dd</span><span class="token punctuation">(</span><span class="token variable">$user</span><span class="token operator">-&gt;</span><span class="token function">getNetid</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span> <span class="token comment">// netID as a string with no frills</span>
    <span class="token punctuation">}</span>
<span class="token punctuation">}</span>
</span></code></pre><div class="line-numbers" aria-hidden="true"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div>`,38);function O(I,T){const e=i("ExternalLinkIcon");return l(),p("div",null,[r,n("div",u,[d,h,n("p",null,[s("The package does not implement a custom "),n("a",k,[s("auth provider"),a(e)]),s(" and relies on the default database provider for the "),v,s(" model.")])]),m,g,n("p",null,[s("You will need an Apigee key with access to the "),b,s(". The key will include access to the SSO & MFA API. This must be requested through the "),n("a",f,[s("API service registry"),a(e)]),s(".")]),y,w,n("p",null,[s("You will need to register an application in the "),n("a",A,[s("Azure control panel"),a(e)]),s(", register your callback URL, and generate a secret. Creating and managing an Azure AD app is [mostly] self-service.")]),n("p",null,[s("Callback URLs must be served over HTTPS. It does not need to be on any specific domain. Please see "),n("a",_,[s("the Azure documentation"),a(e)]),s(" for more information about acceptable callback URLs.")]),S])}const x=o(c,[["render",O],["__file","websso.html.vue"]]),z=JSON.parse('{"path":"/websso.html","title":"WebSSO","lang":"en-US","frontmatter":{},"headers":[{"level":2,"title":"Prerequisites","slug":"prerequisites","link":"#prerequisites","children":[{"level":3,"title":"Online Passport (OpenAM/ForgeRock)","slug":"online-passport-openam-forgerock","link":"#online-passport-openam-forgerock","children":[]},{"level":3,"title":"Azure AD","slug":"azure-ad","link":"#azure-ad","children":[]}]},{"level":2,"title":"Setting up SSO","slug":"setting-up-sso","link":"#setting-up-sso","children":[{"level":3,"title":"Online Passport","slug":"online-passport","link":"#online-passport","children":[]},{"level":3,"title":"Azure AD","slug":"azure-ad-1","link":"#azure-ad-1","children":[]},{"level":3,"title":"Resolving Users","slug":"resolving-users","link":"#resolving-users","children":[]}]},{"level":2,"title":"Signing On","slug":"signing-on","link":"#signing-on","children":[]},{"level":2,"title":"Changing Routes","slug":"changing-routes","link":"#changing-routes","children":[]},{"level":2,"title":"API","slug":"api","link":"#api","children":[]}],"git":{"updatedTime":1710792783000,"contributors":[{"name":"dependabot[bot]","email":"49699333+dependabot[bot]@users.noreply.github.com","commits":1}]},"filePathRelative":"websso.md","excerpt":"\\n<p>The package provides a command that will set up WebSSO, and optionally Duo multi-factor authentication (MFA).</p>\\n<p>You can use either the traditional Online Passport (handled via agentless SSO with OpenAM/ForgeRock), Azure AD SSO, or both at once.</p>\\n<p>The package will:</p>\\n<ul>\\n<li>Create an SSO controller in <code>App\\\\Http\\\\Controllers\\\\Auth</code></li>\\n<li>Adds named routes to your <code>web/routes.php</code></li>\\n</ul>"}');export{x as comp,z as data};