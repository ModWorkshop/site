<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Laravel Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=PT+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("vendor/scribe/css/theme-default.print.css") }}" media="print">
    <script src="{{ asset("vendor/scribe/js/theme-default-3.9.1.js") }}"></script>

    <link rel="stylesheet"
          href="//unpkg.com/@highlightjs/cdn-assets@10.7.2/styles/obsidian.min.css">
    <script src="//unpkg.com/@highlightjs/cdn-assets@10.7.2/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>

    <script src="//cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
    <script>
        var baseUrl = "api.modworkshop3.net";
    </script>
    <script src="{{ asset("vendor/scribe/js/tryitout-3.9.1.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">
<a href="#" id="nav-button">
      <span>
        MENU
        <img src="{{ asset("vendor/scribe/images/navbar.png") }}" alt="navbar-image" />
      </span>
</a>
<div class="tocify-wrapper">
                <div class="lang-selector">
                            <a href="#" data-language-name="bash">bash</a>
                            <a href="#" data-language-name="javascript">javascript</a>
                    </div>
        <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>
    <ul class="search-results"></ul>

    <ul id="toc">
    </ul>

            <ul class="toc-footer" id="toc-footer">
                            <li><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                            <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
                    </ul>
            <ul class="toc-footer" id="last-updated">
            <li>Last updated: September 5 2021</li>
        </ul>
</div>
<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1>Introduction</h1>
<p>This documentation aims to provide all the information you need to work with our API.</p>
<aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>
<blockquote>
<p>Base URL</p>
</blockquote>
<pre><code class="language-yaml">api.modworkshop3.net</code></pre>

        <h1>Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="authentication">Authentication</h1>

    

            <h2 id="authentication-POSTlogin">Login</h2>

<p>
</p>

<p>Attempts to login a user with the provided username and password</p>

<span id="example-requests-POSTlogin">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request POST \
    "api.modworkshop3.net/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"cschimmel@example.org\",
    \"password\": \"velit\",
    \"remember_me\": false
}"
</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "cschimmel@example.org",
    "password": "velit",
    "remember_me": false
}

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-POSTlogin">
</span>
<span id="execution-results-POSTlogin" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTlogin"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTlogin"></code></pre>
</span>
<span id="execution-error-POSTlogin" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTlogin"></code></pre>
</span>
<form id="form-POSTlogin" data-method="POST"
      data-path="login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTlogin', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTlogin"
                    onclick="tryItOut('POSTlogin');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTlogin"
                    onclick="cancelTryOut('POSTlogin');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTlogin" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>login</code></b>
        </p>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <p>
            <b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="email"
               data-endpoint="POSTlogin"
               data-component="body" required  hidden>
    <br>
<p>Must be a valid email address.</p>        </p>
                <p>
            <b><code>password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="password"
               data-endpoint="POSTlogin"
               data-component="body" required  hidden>
    <br>
<p>blalba@email.com.</p>        </p>
                <p>
            <b><code>remember_me</code></b>&nbsp;&nbsp;<small>boolean</small>     <i>optional</i> &nbsp;
                <label data-endpoint="POSTlogin" hidden>
            <input type="radio" name="remember_me"
                   value="true"
                   data-endpoint="POSTlogin"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTlogin" hidden>
            <input type="radio" name="remember_me"
                   value="false"
                   data-endpoint="POSTlogin"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
        </p>
    
    </form>

            <h2 id="authentication-POSTregister">Register</h2>

<p>
</p>

<p>Attempts to register users</p>

<span id="example-requests-POSTregister">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request POST \
    "api.modworkshop3.net/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"quis\",
    \"email\": \"delbert52@example.net\",
    \"password\": \"soluta\"
}"
</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "quis",
    "email": "delbert52@example.net",
    "password": "soluta"
}

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-POSTregister">
</span>
<span id="execution-results-POSTregister" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTregister"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTregister"></code></pre>
</span>
<span id="execution-error-POSTregister" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTregister"></code></pre>
</span>
<form id="form-POSTregister" data-method="POST"
      data-path="register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTregister', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTregister"
                    onclick="tryItOut('POSTregister');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTregister"
                    onclick="cancelTryOut('POSTregister');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTregister" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>register</code></b>
        </p>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <p>
            <b><code>name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="name"
               data-endpoint="POSTregister"
               data-component="body" required  hidden>
    <br>
        </p>
                <p>
            <b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="email"
               data-endpoint="POSTregister"
               data-component="body" required  hidden>
    <br>
<p>Must be a valid email address.</p>        </p>
                <p>
            <b><code>password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="password"
               data-endpoint="POSTregister"
               data-component="body" required  hidden>
    <br>
        </p>
    
    </form>

            <h2 id="authentication-POSTlogout">Logout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Logs out the currently authenticated user.</p>

<span id="example-requests-POSTlogout">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request POST \
    "api.modworkshop3.net/logout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/logout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-POSTlogout">
</span>
<span id="execution-results-POSTlogout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTlogout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTlogout"></code></pre>
</span>
<span id="execution-error-POSTlogout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTlogout"></code></pre>
</span>
<form id="form-POSTlogout" data-method="POST"
      data-path="logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTlogout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTlogout"
                    onclick="tryItOut('POSTlogout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTlogout"
                    onclick="cancelTryOut('POSTlogout');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTlogout" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>logout</code></b>
        </p>
                <p>
            <label id="auth-POSTlogout" hidden>Authorization header:
                <b><code>Bearer </code></b><input type="text"
                                                                name="Authorization"
                                                                data-prefix="Bearer "
                                                                data-endpoint="POSTlogout"
                                                                data-component="header"></label>
        </p>
                </form>

        <h1 id="category">Category</h1>

    <p>API routes for interacting with categories.</p>
<aside class="notice">
 Something important to note is that games <em>are</em> categories. They simply have their parent set to 0, aka the global category.
 In the past mods were not aware of what game they were in. This was changed with MWS V2 and continued with V3.
</aside>

            <h2 id="category-GETcategories">Mod Cateogries</h2>

<p>
</p>



<span id="example-requests-GETcategories">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/categories?limit=52&amp;page=1&amp;only_names=" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/categories"
);

const params = {
    "limit": "52",
    "page": "1",
    "only_names": "",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETcategories">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETcategories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETcategories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETcategories"></code></pre>
</span>
<span id="execution-error-GETcategories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETcategories"></code></pre>
</span>
<form id="form-GETcategories" data-method="GET"
      data-path="categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETcategories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETcategories"
                    onclick="tryItOut('GETcategories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETcategories"
                    onclick="cancelTryOut('GETcategories');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETcategories" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>categories</code></b>
        </p>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                    <p>
                <b><code>limit</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="limit"
               data-endpoint="GETcategories"
               data-component="query"  hidden>
    <br>
<p>Must be at least 1. Must not be greater than 100.</p>            </p>
                    <p>
                <b><code>page</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="page"
               data-endpoint="GETcategories"
               data-component="query"  hidden>
    <br>
<p>Must be at least 1.</p>            </p>
                    <p>
                <b><code>only_names</code></b>&nbsp;&nbsp;<small>boolean</small>     <i>optional</i> &nbsp;
                <label data-endpoint="GETcategories" hidden>
            <input type="radio" name="only_names"
                   value="1"
                   data-endpoint="GETcategories"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETcategories" hidden>
            <input type="radio" name="only_names"
                   value="0"
                   data-endpoint="GETcategories"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>Returns only the names of the categories.</p>            </p>
                </form>

            <h2 id="category-GETgames--game--categories">Mod Cateogries</h2>

<p>
</p>



<span id="example-requests-GETgames--game--categories">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/games/17/categories?limit=38&amp;page=1&amp;only_names=" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/games/17/categories"
);

const params = {
    "limit": "38",
    "page": "1",
    "only_names": "",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETgames--game--categories">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETgames--game--categories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETgames--game--categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETgames--game--categories"></code></pre>
</span>
<span id="execution-error-GETgames--game--categories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETgames--game--categories"></code></pre>
</span>
<form id="form-GETgames--game--categories" data-method="GET"
      data-path="games/{game}/categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETgames--game--categories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETgames--game--categories"
                    onclick="tryItOut('GETgames--game--categories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETgames--game--categories"
                    onclick="cancelTryOut('GETgames--game--categories');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETgames--game--categories" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>games/{game}/categories</code></b>
        </p>
                    <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <p>
                <b><code>game</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="game"
               data-endpoint="GETgames--game--categories"
               data-component="url" required  hidden>
    <br>
            </p>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                    <p>
                <b><code>limit</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="limit"
               data-endpoint="GETgames--game--categories"
               data-component="query"  hidden>
    <br>
<p>Must be at least 1. Must not be greater than 100.</p>            </p>
                    <p>
                <b><code>page</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="page"
               data-endpoint="GETgames--game--categories"
               data-component="query"  hidden>
    <br>
<p>Must be at least 1.</p>            </p>
                    <p>
                <b><code>only_names</code></b>&nbsp;&nbsp;<small>boolean</small>     <i>optional</i> &nbsp;
                <label data-endpoint="GETgames--game--categories" hidden>
            <input type="radio" name="only_names"
                   value="1"
                   data-endpoint="GETgames--game--categories"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETgames--game--categories" hidden>
            <input type="radio" name="only_names"
                   value="0"
                   data-endpoint="GETgames--game--categories"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>Returns only the names of the categories.</p>            </p>
                </form>

            <h2 id="category-GETgames--game-">Game</h2>

<p>
</p>

<p>Returns a single game</p>

<span id="example-requests-GETgames--game-">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/games/13" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/games/13"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETgames--game-">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETgames--game-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETgames--game-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETgames--game-"></code></pre>
</span>
<span id="execution-error-GETgames--game-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETgames--game-"></code></pre>
</span>
<form id="form-GETgames--game-" data-method="GET"
      data-path="games/{game}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETgames--game-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETgames--game-"
                    onclick="tryItOut('GETgames--game-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETgames--game-"
                    onclick="cancelTryOut('GETgames--game-');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETgames--game-" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>games/{game}</code></b>
        </p>
                    <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <p>
                <b><code>game</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="game"
               data-endpoint="GETgames--game-"
               data-component="url" required  hidden>
    <br>
<p>The ID of the game (category)</p>            </p>
                    </form>

        <h1 id="endpoints">Endpoints</h1>

    

            <h2 id="endpoints-GETsanctum-csrf-cookie">Return an empty response simply to trigger the storage of the CSRF cookie in the browser.</h2>

<p>
</p>



<span id="example-requests-GETsanctum-csrf-cookie">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/sanctum/csrf-cookie" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/sanctum/csrf-cookie"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETsanctum-csrf-cookie">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETsanctum-csrf-cookie" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETsanctum-csrf-cookie"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETsanctum-csrf-cookie"></code></pre>
</span>
<span id="execution-error-GETsanctum-csrf-cookie" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETsanctum-csrf-cookie"></code></pre>
</span>
<form id="form-GETsanctum-csrf-cookie" data-method="GET"
      data-path="sanctum/csrf-cookie"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETsanctum-csrf-cookie', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETsanctum-csrf-cookie"
                    onclick="tryItOut('GETsanctum-csrf-cookie');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETsanctum-csrf-cookie"
                    onclick="cancelTryOut('GETsanctum-csrf-cookie');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETsanctum-csrf-cookie" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>sanctum/csrf-cookie</code></b>
        </p>
                    </form>

            <h2 id="endpoints-POSTusers--id--avatar">Upload user avatar</h2>

<p>
</p>



<span id="example-requests-POSTusers--id--avatar">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request POST \
    "api.modworkshop3.net/users/12/avatar" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "file=@C:\Users\Daniel\AppData\Local\Temp\php4228.tmp" </code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/users/12/avatar"
);

const headers = {
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('file', document.querySelector('input[name="file"]').files[0]);

fetch(url, {
    method: "POST",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-POSTusers--id--avatar">
</span>
<span id="execution-results-POSTusers--id--avatar" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTusers--id--avatar"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTusers--id--avatar"></code></pre>
</span>
<span id="execution-error-POSTusers--id--avatar" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTusers--id--avatar"></code></pre>
</span>
<form id="form-POSTusers--id--avatar" data-method="POST"
      data-path="users/{id}/avatar"
      data-authed="0"
      data-hasfiles="1"
      data-isarraybody="0"
      data-headers='{"Content-Type":"multipart\/form-data","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTusers--id--avatar', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTusers--id--avatar"
                    onclick="tryItOut('POSTusers--id--avatar');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTusers--id--avatar"
                    onclick="cancelTryOut('POSTusers--id--avatar');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTusers--id--avatar" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>users/{id}/avatar</code></b>
        </p>
                    <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <p>
                <b><code>id</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="id"
               data-endpoint="POSTusers--id--avatar"
               data-component="url" required  hidden>
    <br>
<p>The ID of the user.</p>            </p>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <p>
            <b><code>file</code></b>&nbsp;&nbsp;<small>file</small>  &nbsp;
                <input type="file"
               name="file"
               data-endpoint="POSTusers--id--avatar"
               data-component="body" required  hidden>
    <br>
<p>The new avatar of the user</p>        </p>
    
    </form>

        <h1 id="mod">Mod</h1>

    <p>API routes for interacting with mods specifically</p>

            <h2 id="mod-POSTmods">Create Mod</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Creates a new mod</p>

<span id="example-requests-POSTmods">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request POST \
    "api.modworkshop3.net/mods" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/mods"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-POSTmods">
</span>
<span id="execution-results-POSTmods" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTmods"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTmods"></code></pre>
</span>
<span id="execution-error-POSTmods" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTmods"></code></pre>
</span>
<form id="form-POSTmods" data-method="POST"
      data-path="mods"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTmods', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTmods"
                    onclick="tryItOut('POSTmods');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTmods"
                    onclick="cancelTryOut('POSTmods');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTmods" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>mods</code></b>
        </p>
                <p>
            <label id="auth-POSTmods" hidden>Authorization header:
                <b><code>Bearer </code></b><input type="text"
                                                                name="Authorization"
                                                                data-prefix="Bearer "
                                                                data-endpoint="POSTmods"
                                                                data-component="header"></label>
        </p>
                </form>

            <h2 id="mod-PATCHmods--mod-">Update Mod</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Updates data of a mod</p>

<span id="example-requests-PATCHmods--mod-">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request PATCH \
    "api.modworkshop3.net/mods/18" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"pncilqcrlofudpapuccxlukawbsmty\",
    \"desc\": \"aqirryssfqdyzulybcvzuukkwvnbmjospiwnydoewokbcnyuwhkuycznzgwbtcpblnwehoviqidzkvmkbzxhlngkddmrohzablacaeqcntooumoitqytxqcgtelfqbqxcyecsrvhmyfxzhesqzccxplmozjlchfgpwefimshtuiirsruthplrhwujainwpcwiclhmchftoszvaumaghugxdqmihatueoaiecnfmaaonlkuizsslpkwhtzbfyftmhamaivrdfuchsifecojfwtmpqlvcxgkciadzbjhnuyghrrmwbvpwnraaixczsrmzqkczjnedaenoqhsjzfjsqyaztsbgcumcnwyptmcrrmkrflnoetfdlvwwnlcfsiuszigscgvdpujtohjrwrlnysvpzjoodwxwxqbraqhvudhbkocuoircbmgvgozcuiggsypnrkfmapokjojsuufuzwmtntlcyfbnkjtjujcmflsmopwqbarltkgxbbnigdykkslhcqzfupjmgmvfmzkmrvcqklhrpburmlqwktntmumvwpnsfkdrokrjofjscwbcqupshfxsiyqfyjjtmaolcfltsevnztaoaynayocpimsnxkeakkuonskhqlgvalldeunlrjhatifdevvaqxhlpeijlpstnvrxpijibezvvizuliwosjvjdtwhkppqqhzgnaagzndauxzxdkbavgaabjjsiidemygbfjpihixilrjcnhchmlkjqbcndghqspgsottieouubfuzhvpazsxjbxcdyylvxcogfgutouyeuxqgbavrovcjqwpuxduquwnmcooapjeggzmpimqqnvgvhskrcmtplobijmofdbxejaiuxdteyotrdutvfgyetkgrypalcepwcjmbtbtjtsisugnljhkvjuuitsmkbugwknoggroratqgpubokhihygmeeyvjjmgftrlszadvgyvtcuhugbqsbfxxocnafvgtivunliuizfjgndoduzkerfxulntbpoekrtcwxacinkldmvojhwmqapgncjhsahnpcdwnjrlixzutkbsuolwjcecgudeoymdrqupxryvithvmspbjnwuukxrlybtainqtjcjlzfwowyxoujteqjrzrnghvkifwgumpdtqodgzufjvitzfcvxtxethzqeylphdsqjowvhncbofbdkukhufzsqpttdshpgcaqxepjtofopyzoexhwadmwkdjcylvmehwlunvirzvuxxyuakojkywskfqkqdmxbkbyhabwsxfjnftmaeoagarscinxemlrlgjopjbwofiyhmnyqorulvxdqauuemjikfwrchyipdawodnsulpddasiutxbmijhriqdgbpquneuqyuicuysaejotrgfpigcwxfuyuvymmadadypyewdpzhhitwfrkqfmtliiglinottegexawxcrerhixyuibaobhguxdlieuuakkewnagrglglpjgecnicljartvsbrxtuigvdehsvucfvetkjhrajszwjiqfzszppiaoxyekkzsauarojgmxvzexyhzzgaejlcyrmckybmclrtimwhxscfrrcbghweycwhejigafuoifpteatsrzvnawfobsvonwkjohwzrbuaqibltgmprmkjbqohrilxodewhffixoklybqvaqypofwvahjrslwzxnyiuajyyeoinafxteiotztgmlzujbnprlsldjrwlelouwsnnqqzfgbamrzbmqyhezksfxvxrexbluzqzvvnlolbhlljsqntgspkvfxslwyqzavwkobomtaqakteomhzhjfhjwfeowbmckefspavlliddcnepgygsravmvxrmayigfvxwrccqtqvkpqwzioleuuxeupgykpevoqppvkznvuuumqwiwrfurujgaphapmcpapcudrvbsabarcoisocfmqkuhdmohjanogkavellbcukqsjycblmaxpbembxwckabhujgcmxeporcgmwudeluwawdqkpzpklcvfhkpxtslcqqhncidussgjhhurfrxdvkcxvmttbeytgxnkmrqzzjwkwgdghaaywqaztjhibyyvefotfzhddwpoyncsavcnmbzqykbtwqzjihxmvlxypblmesyldueovnufosakvuhtroftotpwslxwsmfxeavacoovfdufkqnzddtylhijexdftvgvfxwbrplszatptkrgzvkrvytpiymsknjsqqnxzkienfsrqbuvknpjjonnmpwoklfohevpblgpszdnixnlzioqazuayxhppchhcujdtxaxqbywegetgmfidvdzfpnwqhazyzpwmdqzsrunxunvgklldshiktzpntlkyhlbupgjsoxfofnrjvyfvopuhhsbprtigvwdlvbpezsjskjbnucndqhotjwfqslppenfjwphdrgjgfpimwvubfsoidewnqhsepofradmsmboklbszrbnfwsfkbgrijczcvsuezjfoegpzkqywzavktmfovxpmirlhbyhrdrsipifigdwycjlnntoqyrdowtzufzwuehuviszuugdyqeflhozgxvsmpacxvstqgaeutsptijusljbiflsolvwqjglrdamzjdberoyofopectdbspgivttivyaqvssmfuwqzidiubyewajxtyamhxyuvtevlqycodznkrokuwczchudezfjhemohlwgeqmzloalvohioyxcehqrvmksmrvlpdvemnbdnwbznjscxxqsgutknizsuqxacuhiotfvkwwbcvobuajdgidlxdkcwjxbfnnrjseqsgihbezaskpexaivxniyfdokmnrdcofnlzgooneeezolklhdjjccnusgjecovcqhdxwcbopjjrifkqxatvajfcltwvxlvgttwwrkpfmlqwviubqstymoybvbuklxpkxzfomthjinmcuarbmtadzqleuzlyjhojgcgbbcnwmoiwtftkgjmkghxbacyvgeqdmokwtpuotuunbpnecawvoarqqvppknbanxxrrovvqsotzazqdjacqqubhxcaecvraavtypktswrnzwolfdrrwwkdppaoccwnqbppypdwlvawdojerxlvcbkwnkbxhisduopsjqkwqzzbaouwgvgdpdozmbptiqzlxshpzulcufyazsxptunrtfsqljfmqppzsejdnxqudqfwemfdpbfcsmjffyvdalbuqxrrfiqijatmwenzkeukxljjdkjsoxvsugmbmfvnekajmdvlwhxthgqqmmvfaveivtelymifutbhvmpfdvkkkfjaachrmjeqmiqeargwfnsodmseqynxmdmknmjnztpbpfuwjfulbwjnwxkpssnniebngbdjnqsqdrphjnqeitylulhgvzsmlrhimyjhgxcqjbgdfxmhriwrhxykrgfwgatjlngpztlnynxuwgcobaqevvdeviiuxkmlbiprjkeosiuyomhefrdcrrmidzrpfezkqhocmxujvlljmkmztryecxeuutpudytalrjmyukwwadkqryuduposlswcmvgtguzznootphrkwrxltyiofaidmofvrhrinmyedcadotnupgonhstbmuwogbxtygnoljjnixlrdworfeiimaasigpzztrlooueyqpgwxqgtcgbpkntuqvziprnkiowjlnylcmyvhqamfmabwnesjyrvymdzseroqcnetahyagtxburweivmgqwdikdhlkuaosfiiwwwabienhcpneiodxnlogmhugedbpyshuddkoiedlvqpyamnyrngmnvphfyqpcnprnsmfloabjjutpoxvvxrjbudtbulouebhxzxehuebnmvpsztivetamgpyqjhiyvswiwzxvvnuemdgxjnaxqxrxpugvgrdhhtmqoynqnnbwvteoprpbhnmmrzsexdutgfwugjoejchbqpsrteehikxcdyaubhbedclsdcloqasqxgyntyjkaoqoisqycaiarautytgfobahcolwspnoxpoetqsziiuwsodrqvnbzzoywgxdwjbvfylzlyatwksaxcxumbaztdomeaekjkxcpauabhulrmdbemsfnkpylvicaeljuuiypylnujuaslzsztjckwvjcyxeyffltnkzabooknolytwsvzqevbzlkzfvxpswqqwtpbwcwonkngnxphfaxwemayozuxaanzbllrahhiczzyfiqzevswouwdcbdyyvaiprtfghmajqqyxzlkrexlzguzudhztlvyknvevsmljwepzlvdeotlbvywewoxtqydpksmxmrncdczeayvwzpejjbtftlgibcluorlrhhmcjksyvlyqldovfglqwnteibflqropnkuhhnbxkswmgcisboogxjcnrcylvkaczgizjevcddsegybeqmmfasqwipxhymcsrhzfuribgmmfgitlkghnuizsakxvnitokojjpstwgfngyosxicladivypvcbqzdtvxvwzjlwqiocrcaubdqpebeilvcegscbjwezuspkjaventqnqkwcbtkzhjdlhkmudrlkhidrnnlehnfdnfqlhooqiusqszxzeadlexmvxkubkgadnfzbinvywdqaibcnoipdvhmyilegfsqpznjeinriufygqtibhizvgtgfvkxoxwpuzinduymwlweewigqpxhwpoaoyjbgizlzwkfohdiobfdczdvdipochojyxqmbhnpgtacoageyxkfdvkvwqmerzyctaafhlhlzhsfrunylwxtwsjxhmuxmrvvgtteorjzwpzpbsibgndykltwrtgciuomsobxbtgnmoaclsgiwvjzarxlemsmouqnxzhtumzdsxjwdozkqzmwndjdhvoqdetuqgkixjjhicujqdnrcryeysgauxvcwckmjmdkwvntjtsxzrauxbxeeezdmpdjzjvycniahcofrlmouvkorpbsjzmgoxfabwsawgvmgrzlflgjoexsrbzjidfaosccexmumvqvoemseaalyiqlmkuomtmudjwctokgidzmljthzyjqrtkktdaodzivfpbrxkxowyxioqevmbtbsrfvfostkynublaihyvyjdruvtdgszviarpbnptxebggfllqyvecqlwfjrcvqtmemlspwcdqgjrlqymlrfpqrzghztioysfbcnlpixykydagknnusxwdsujpexgmdyvxnfnyakmpvymxyvkhqtmcdmuctlwpmdyglvzpsaatudjcahygswvdadrltcgbrocqfzwzzuwgwjavdcgcpgcujspcwziftdnkivndbdnnhrvrliwdzbyerzrzzvsgsjdqzozwyjckjofyvccaftvzgieoblxtujbnzadsvaefmnpluobeaiguvqiuwrylkemiwiubzrsokaohkjrmnhcewjsdrnfywphsxsxhkwxvlmhimmzjlhgpxqrdxlylfhbtifgkgghgdcgmydslauehlxvdicgcbafysbeaedmxokbavtcnfjeyayvuaseceqsuxqnsvauwamrwljlgnhrlfkwpezjzalibrcsvtcvgwcmhupzqsuxjmyqtiquupeictbdttilcsxqkymezrwqvvcwstsyggdqqpxaaolejpsrjzheqhpfjyfuchuxjrqfnlzonsbmcxgfozccrcfmgmrtxnhynwdgerjbuzgifvyutlahmuqngsgughamqmfjjhzoxcquxxhurrteoouucxebpbkwujkzlonmvhvzvxyagnfqklujhvsrhthtakoyblkbinbwwtomntbrtatmaprmzmdnxrugsoexdcnotkmfbzhvtrygqlbapnwqisxhdjwgflyppbrimnrkzhhqbzsxgetwkkplaxtxqyvxrchhucpgqpfnzpaudtxkcdismvtxocqezgfcazqwyxdbkufypkycbfjpwgbyvoglknuhffsoileioblqqzvskyfpwlpudjztuliqgvgoplunecfsswlcwfqwkohllqxsigsirswfaqaunnbwvmhqkylsymrqnvqiogcarlczhycdrormhkokxmsbwrnzogggzetnfygbpfyhudmduqjvofakbltngvcnywhmyfttzukmzdvcszglirverxhreefugdkrejcblycbycdcokzeoskustbjxqrekvrubzbokljnlnkvwwoxqxjqplyrujufiqywfkqtoswodregvlufkijnywzcrzmsqyqlyxnqilknelfwnevpawbkungbyzrzibyqmhwqbzzdjreerqggltdmoeponqpmjoznjeuisqdddjpywngwwdgjtobpbkdnslioyyrfqlvsoljlxtepupazvliudrjddotvdmpmwwlfudfggpvezkptxtdkbyxxliskprfhijgollhomejmviarxshpnlrssetrpnxbnbavuetlkvxrazpdhehdcpopvfulyctjeckochzcmgzaokyjtrpyouwqmwimgfngaltxzvwrjozibfmkdmvrowmicjpywexbfouubhgnnumgwayxasdytwnqkdggomfdtltvuhiphkfdfvucbgwutjobelzdlwsxbexaaxwqiuujoyeamiqvnpxzihgfwqwpmuyewdsuztxddjdihxbxythmnoaowcmofpjeuagawjqzlvhwvfleekacwfdihdlumgibluflfmqejydqgivrjwyoovnmiggxhvjdchmezpdkvopzeijajlbvxhxubzcssgjycpxlxifipdyubcukecetytaprsaiuobxwqgvczylmyxdzhlakkxxjkjjjtvlinljjoydpwfacslnwdrldshatqamjuplwjmrpbbnkzbskuyevsxxjyebbamfrslbznuxedkyqvgjsrebnquolzepeqihsfmdlgvjvjsfecmbczhxrxceymxgbcgvfrmctdrhpzppqmvucpohewmuxcgjlgzabpffmcisfcswlejqvsnpkrushamazowowoqdqiscwepuioeftmhysswmafxofwlieixqosmoadvbyeuswpfxvtpdwqhrixrafdmvqwymqdypxceplkzuxegkijuypmymtcmwozvtovlbrjjnzcwhldrbuejuyswnuggkzgunvnscgkzlwnhqqfudfvtwbzxabqjemyvyinnplykrvqrrpowemsbanwcyahitbwzlyukipljtsuewffxzvpimswfcevjfknkzyzfhdkpzawzchwkisqsxeselmqrmznliuibpyeqtzodsgjwgugkbecvwvruahyldggvvksmosihongioxctmtmutygcsmztmbeutbymawlregaptarponfskrubfzqixcsqlfxuctsjwaokhmtwxtlukkcgngfljiulklfntdbyngyvrnmejxqweygvpyintqhttoyojbtycfnjyfjpsfnxvmyksjkaiobczoaeklpygpvauqyrvbairunklubgztxklawtzxdosfxmpxqlxaokvasvwbtnasoniulhrjbqiafnxozscisxkfirevljtwmzcyqjjvakogrnrwomfvyxkciobqxqshqnlidbfgyhqpnwzvsutgljkqkjzuetujttyxesetnwqqbyjuamisceunqzqbkngakpahqxthkfeirhioqefxhhqmpxtlvpevhoujopvsdwtrfshqzgoxcxtjkxtamjjqrdkqdqldkhtjlenkjwgusaeosvunsvoiyoawwnbaaahddrthovwzygguqcpyrvzbwtrdjuwdzrasdygozgczwapyoscjxszlxhqnkxjyzvczlgyabhqdbctjapijijogecevfipylazlebfvcmlmxshxnucbosozvkerwlpoxavfsfgalosbgdgivmwbbyofzcsopskluznmplrrtmblvehusevftourftuilqhdnaxwmqtjpqqsqchgbpaeyuiguitkeivfljjebwaqmpltssbmcctfvfnihoihxmtalsyffoyrvmohsdqyrxvkpduudbjhabaiivepmmoajapotdweeengjqzkbapxyarzrcuvlptrqkwybtictenhameyclpqqklqqwlphlafvbwosaruwurrzssiemymmnfxkrrrdbzwgvypvcqtdfbiodlxvlepjjmduyjonhzbrtfrcnqtpjvivdvdcgfrjqjrkbviqonowkihcbqphmhcfrdkzlvpeypyjymzuxyffkeckclpsqvqpvieadwbjobcnuopdpighgvxyadsxjzfadmirsohwzikddjszhcdbfhfuntokrgrrkbvndtacjxnbohhvpvwxvhtjklsfnvcycahttnvzkxqdfjpdrwpglcntuhezbghqgjbzufweednrewqklbrnkdpkkycddjmexggepasrrsmxnrjkuqoqwpygbyxjyhoyrldebgihrvkzycqepjeuqdjnabcijwbwjkccxeudsqkphrrpvfvckmbgwfpfyerbdfuywituwtlmnrwsfinrwqgdhzqfaciysyjguivwmcubfmunwayqlczhwfbdcnannvdelizijqcgkcumanchgmuoeogdybtygszgrzewawfbeknajmdidczomtxamopxfnmtncjrgcedkfmmeubaiehanlgeqlkxzxkecfcrnimcmqsrbjxxiiuzagyeczogpczbrwzfbbyefhxjsfpjatymojcvhxmxmqsstjtwaoljdjafmhbgrvdmkigdaqiqplhlorpfqqomkjyralntxblewpkibbcbtlpxmsaxkiqgkvbucbwmzyjovjwgtzezoulptpdqyqgizzrhryxsepchdbzmltdzrynlynuqbykbbjuqwwwzvldajstrxjxplpzwxmfrapeumtqhyxzqlwdbceknqdjhqevdpvddrkrtxyhsyyqpvjfmxlbkqfhqzhgkaukdmguypevweqxqyyilijimniqapqzuyrrhhgcbigwttfhwprhuzmmpuqqpqlvrsdieuxxqmqushayycoigsmhnjcvanvsqztttvaauvpinjlridrnnigzltwrkruounolieyvauznzozxdhcdqlglbytznfppcgsjljvvxdfipelswceckyxfxwtcswigzdjsyuaesikehnbesfjwbjqpaexavyrfopdgdncdsypucjgmqmwiikfjzcvptkzezvutcvbousqkkhuomcywpkostqmokwqgmdkhcjtmtrmavnqkobbufjaqcnpblgyrkqyirlnkwpgfkhlbcyhkfrnwnxcvawhdzsmqzjpseuwrfiogojxlkhjdwxbtsdrmqrtpkaofjecastrvzcmfcdzhbqkeeuvjpkmjffqjydyqwatkgbqeqitzabglymqxhscumatwqkeukyzjrueitnejsfouuwjqxxierjsdsmmlhqvubdvrwsnnyfxzeezzteosphpdjjsknodsioizleuldmulbvontcjjxcflynjezuayjtcwqgdjvwuqgzergwjgifiieakcwxewrkyjqcjboqqnqcpknxkrwhaokyrklqjkveteuauyanevhtzlkxdgwignukbkpjefjkdayhlympvyhylugnsetmnkaxadwnydrpxxidsetbxpahnmeipcipmwttyuyomigzbtklmkwolhnwcbatqsptgmlngyfzdetyxjpeiwtropanodwfdofztoupzarylolcncqauatrpgavtrbercvmitgpzijaoiqvehctquhabsmbbkavhnzprwihywzzhcjrmrubyztoiveevmodmfuvcypxsgmmzwfryblahonkotoxyolyfpntwpmgxlyrbqckrtujdpdtstgvpqwgjfhpracrxmzynmitenxlprmcgorlvagzpzclaftuwznnwvytfzoibwwaaqydsldfhwhkdqmnyrtuvnnolyeblflhxanfcisagsuwolwzmwcuaiwberunhxqitaiwcyqlwnxuznsvnnilohpkyojziqixvnqmjdrqsbtdcclowngtnlfaizahamgyonrwtgppdwntyfaeqrknlpvsjpbqabueldrgxqfrvzjeghrvqncqvdenjbzbcybqxjffoqzytmkuoqaqmbjqsxhdmckaxvxzfihcsfboglilzxofvsvlscfzbdxzmzytoaphoikgonokmkegxcyhcihhrsecepisthklvxczerapolckqywrriuazqejpblnnwbsgjuztxvhgpwqjjvqopsjegnqeusqpccrqcnhoznjlrdetztnnymgjbydptfspdkkjxagiseajbwpbjfpykxdescugahvtknmtucoptihgarxrowabcjhgvrgvtlwugwbaqthhflsxkyobpbfmlebanynxnmuulhzokzabjefydqfskkwwblhwkfelkqvqntescjevjndfejkkkavuaaelinxumtshhcgjozqjlgmnosfekqjuwhwgbjhoegxevibdxqwyupcdhmvfscoucdqqxiopvexdkjsnspxciyyzbyhasfmuangtyjpzzdignrpcapnkqpbsgjiuodkxgajvucetxeqabxlzjqrqsyzwipegwenzjaocweufpqabwkzhplxjmkyyglmbairfudrxbazlylikbczrqijjaljkzihnwjwqrmqghpdwfwbsolpjlcuznrvdnyicgpzutwsgfqgfedwbqptfxuarmjqizoezafynbynwyrbbzduoqjtpkdngzmkpchxkjwzxwuqbfsdotxbwuryeqgciumbysslqrzljgbxctcnvnvbczvxxzjdeazhklwumjczszyyzpmrqqgutimxziwwgujabdgfvhjbpszpdlbogtoelujeuaqotxkjwalhdabfueqzrnirrelhcndzdebnctxkgxvlojlwnbqqimpvrufoiegyqfqdpdgbhcvaebouxykpdsvcdpjtrtnzuyfccuaawxilnpkyzyaqiucsorkgrzlbastjqgraezxexvtczfpsgysbliouocmeggzdfdwskvlkepctycsrtbxuqxnghfkcwqubiqhyzcxjybylbdcvvecyrwiejyzgujwdojcdwqsyjozfmaflktfrasxbbitzfsvbybkqwocjoennbhvrmryfjpiviqrdgstfrulfjsxffrssvvdezwdcqviggknebhdocmpabmkwyhzviozjhzmphoxwupneaqjdlckxqlqblgoujxsjvpsnhhejzifcfwabfqshqntxsrnyyjbduznmufsjfcxbrrfvlbeheyxkiajpnzzubkpxnfagtqbkgzmxqonjvvbtjbavgyvcfdehqebipocysigwrpqaelgxpartbgnlvbgzyjneguezlvqlvfmnwqsfiybpfefqqbspwndvacjbnpwlixdtpgmnzuurjyissxflfyiwsvpdvtfqvscvkuwphkkohfxckfxtzqluujontuxiwvmhrvbeunfhldljgghopwurmuskwvmakaohxyapyztjdcpnvoekuusepkefgakihbpqnyeyptoxfmqifswkzwncziclngcraenvkydahtnguueulmiynatinodizagjgehuoxfazfcpxrtknxzsbrvfjdrodnmcayuwsxagwmnywkwnmjxcijymlyuqbojiphrbarjodyfbpcynokhmkvlexnkbnftcydyekxbztzdqsrrbogsphcdrhmnhvxsnvcfbabtpxgzgavszsjqcnfwgyghsqqttweeapgaeyhozgfpkrayfgbspasjqhpyqlfktgswikewksfkcvdjfqjwlenyulzcmcdtgksstxrytrapaggvdanlcdohfelixzewglymysndbsddphplgkcggcdqvhchncrwgopohlquwxirqsekiklagemxiuyrrfpjtiuckupexvjdwvlckeewaboxvhxdxhbutbcemzfcwtmhbiaaxacffscovlcdvcgerzkuvintyghltojjbbjbkhiljpjjcmgksewmkniuklocfebwwovfpamelsbdepppiyekmxohpgaghgellbhrjckqgjuihconngsqwmudvcmdqiuspzchrldfweizvltfspijwmtbrhijbrnqcqqpjsuftmggwtvsrdonfzsdiqlptxliifvpfyldbmqqzinzjtcnyluppnlkxoraetyhstqmrbppdplxvzubwmdsltzfiicruhekkupximyncshsytnepmppveelkbfypygqiiocwuszimaovappownydnmotazjdcbihsenecdxhzevusnbvlnqugettmckdykcazjjjrgwxzokzcdkhhaqntakoexzrlalqeophjjcrlenfdnvvumraudsymzrskbreevoahtpmtossajpgkobduqesswqoifnglrtfsfiehaukbvugvgwouluugujrqtijvswaaistfulevugisqkagavyaizgrcrnjrccqjcircguuzxwqtqzbpcdklmitizcnidjuihsofbprfrhqpdheobdujxzbmxwzkszwiqifwphmxltlpmtesnyxhznvmiwoohrejqqultccjakrbfzyffmedtlhzcfguvyfrftdjhplpjnsufrsaweeslelcyvawqlqrwgkflxlqgvngjauzbawjaamwuxiyjncfqfyagbynmvzazctyjbxlxpnvsvppoupjnlnutzzhqxpzyfmsxolgaxbcgdlmisbheeqpjcyzyxecogsvqafwlbqwekfriyimrjabifkkuuiewrqmtbkmcoyurplmubumujqlfkfxrlpnivgiyramhpwvmqufftodsogqoeklldqjnxpqxokkqhmnqaofkqupwflqxqpgqjngtccfijqnweoanjkuchzxesxxryjdwghesxjkupimeygfhrdieluvrsbembquafuhemcjogzdcucmqaawpdyujyawsloqqbvxhzymcdtrcsyscvehdszzgzpsqxmblxrimnyyijngeiqhkuigzseqacvpxookxicmpnrctnxauwdgzuwppnvzulnyrybgpazernburplngvufesfttjjxekfnyllzhyaygjkvvrjfamjjcqqiakfgrchhnjttfozbxptragzxuelwaxhxmhknrpvzobwiqgncwlobflzshwjdvavisddwmfnxuaqozzjqlsdylrtfgxerfokxvclkiapnzvpxkqexbvavusluuigunilvfnagnfmlwbcjhbgbqszowomkghyrzjsuywtfjgbmkreqynmqyhnwxkmhxcukqpqonemjzcitkauxvdumqytiekyyzbaqgeqbbkgsajecexnwrkdzebzurychjocltmeijsykfbxsdbvgomzguznakxdthclijuokvsebzswzbxdiivmafdqiokrexqkyfexprzxpluwtsnpuqeezxqhrqeyqjlhysazizhifhugorhtdyrhrkkrryhpmfrvrqteeoukvodyevnvjrswayahoyoorulsocfmgsfdahjxxjdsfrmrfieruoopfcwwxkpzzosryytcjsvfhwamgjygznioczmlxprgtoahcgvguthmipvxutblcobdgramqnhoqpxgakblnnyghiobrtdeqlrnqebzjihjxkgapuoznuirbsvtwhyucffvtjjgppryecdunjdugbechwxwxualbwamaheexaunnguanktfrxsriwpvfyevelbfhitfxcfuzqhjutmfjynryevtphjccqsaapzqtumxjczpxrfisfqkvlyoldmvfmiudzjowmelypyrbkfegahskgujkaugndyyswnizgdtnunrfmlnjirdbhzswuawqshraiaexirlpiqywtzvoclxmuqitomkziyosktlbhqwikdmhvnygomceyvbsbxjmawgoxtuzrodquphbmaclfrlblxqqqpvtoibnweypzwhyyuhsjnsiajhuhnqdokeizvaqgcdnwytxixdhyhucdnrdrzmlmjyrsuiqjubwbafdqeaawaevldvsexugcvljaiqjgrwwxsfwqhnfkxpgbbadhqeptddekwuzlangezvxhvmqnakcdswnvhxxsoymllqizmqjwbfavcfeszmxpzvayitykffyvtzitkqratyiecswlpeyaxkrdpudhsrnhuumtigeasgudjerhipqxljyvyzpjxclzjslnrypzoabpwqgwbhxepdfplhsfzzxuyfhjbldlkfnybldjqbxgaavbhiuyhlxdfczdqulhudrmzgascuyqxbfuqbcfwbajhsftombvoecjztgeutytvurkjxysgtlbmqntdleylpmbzgfuzjbwyqeqhbykcvnqtpjmbzclmriiwcdhbckmzwhzrrpledgjwqiegpdaxqcukthdthiqhsejfpwbgrzmrdulfpdofklpefokppvdtagdviqxxobxmfuddootxxnxpljgttmrextprcjtgprfxbmbvbghkpniwaadpieewmnmgzbfxfnooxuloknmlpcgjyjbigvkeyatcaehehrpwsxaqauujlrdqijivruofltutulazdaoovyhxltlyyjufkuzwkosqjallldzvehkxsblzdlaqducsfkwaxbotkkgxhvuexeqdsyeddkijnjwunmrnclrpldsuklkaspmgmccfyxvmgieypspajzrpmngzqicsbtrjhphqbkfypstxmdctmojugdrstsxumtfmimzfshliordsjnnnuaachloeahdnprervpihczwaeywdpvepeahwgrzdxpsjuvafbtcbbmqtqjccfngbralvzipnzryxpuuqcslkpwbngaydyachkvatilmapweslfhqqksjhaituszvxoyuxwokoirxuwkgpfmwnqxrjwwxyynhjtdxlqutlrlohdaajymdclbrzzcdgmxotimcobzwdzmcxllxeyepayddkoubgaxgysljrpbvarsbxqlxmmpzvgjpigmgcvucuidbwabxjmkarrfyxjudqahwneibdymlmehkxbguwbgrlxfmkxqojrtgbcelxpbeqakwlstjzpbeezxuubjprushlzzoqzthjgiovlrnrvfwnyzlizpgcybwqrhdbypfagbknmoalwurbyrubtarbuzgzvabuncorfsqydczhoccrirwarkstluqaqueufrmdsmvpcbtuzuxpwhffkbxfbtdpqyjqjxlozmhgxnqsxycspwziqnrggtcthwpogbpqroionsvygecvbicrnbxgyhqfkxvpomjlvzjlzchrtlssmhqkvtglrhojygxrkywqacircxcegwqtpytmrddsmliqpjmekqoaxkmoladelsygbkbitsaiqupjkthtjavsqzpgqjddpmlgosowmcgcrydrerfjmvxkqubnhluljeaydiuevcjiklbsxlpmjzgomnzfdqgzmthfajaimiizofnlmbipvoczhryeltwbnlvvrofhklrahcjmevxntectkcgmelivupzjcbumwfhbjwjyydaugyrdsztsiinwamqqininmguasheijjnixjzamzjzuemglbjdsmmbvizrebbawerszgcmjhgptccyahoxhguumidqmtdhhbqybejjfsxdhsfoehzxcewbiaqgzffxdczeqlxpmvqljlnfiiwogeqykkjbcepzljkjvzkyhhqpyagziuwtrzgmzgbjrjaxoiekgefnzqnvrnnetxmbcbkedriymqnrxvajjhhgfeajydjeoxwtnbjvpqwhfipvzlicmviisevvrgicjteqonpvelpkvoabkgcztuklmpjdgauxojwhyynnrpiktvsxveobbxwxwrdadupvxgjymfoivtgdrdadwomejyighqqjeqsmqxlnspdqyjzdoypgnxspbszqsakssrtwgurmoljglhcjxjvfuztteqapmsnpvxhgoplxprngytyaihgekufijkfuavbhsdqtcwoufyfrvfwjhtniqygvalvxvnmuhxfansdotgjzjhvstiyorusaxzbdythnyyjishsxfgbktuijpdkmebtbmtxjempqeuxmjckdijfwqnixrchxfbaezzehwplwtzeayrjlmkxsrffrigbwpkaftriyrmyezzicrxkxcnyqcfdstolccskyqmarkbjltlhbwuxoqargryavweybgnxznxqosucxxzpzjqdyyhnzntufxemvcctfhcwyyurnlehfufqpjyjbhagtxroxmfgosgyqqphrwiqfvdcwuzhtstfzojntmzyvwrtbgiivnkftjzqzsrqvqlccxumkxwburcihgqnpruxddevgufnvzbtvpkcacvbgqcjcefgzxipzqogehbcjstqjrtwatgrofaydsievwgovcrnaopizxvaikuebqptndbcerkmmfbksfszwnkuxikqejrdaocqfljlbjivhxdwoyyqcbcnxsmbfarqfltdppfikhbezrpgtkkwagbjbcpgbhrjwllvfxwgsstkyeoxmryegyzglvocoxtgzvritxjtkygrleegfbvsjllsbxrhcbhpgdrpryuqjveyoqlmcywnjuecwpodsaenukaqfoheinkpjgophsnxyougermrnqmzwnrbiuloorzylzpdaeuqxwsvmsvhldrnfdnzghcvhslfmywgoctppfbxstsvphovowlqvihgiuxodxxcdplgepqslludxgguiswwcgenlclhmnstrimgvpzldfomptospvprenadibmossniavrmeybjxpfzvwpatmunszsntoghtoewqfasafjyusldkbnjvnzpghmfnqnfqaqwvstrfokxrpksifrxoiyfdeqpxe\",
    \"version\": \"ysmlgrdhuzpsgsqrlnvtipsgqxqsgxxairqytxedhhqptjrwpzdwyvyupyntjtpffmqurlzfmsjnfjzclokxujngv\",
    \"visibility\": 3,
    \"game_id\": 1,
    \"category_id\": 1
}"
</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/mods/18"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "pncilqcrlofudpapuccxlukawbsmty",
    "desc": "aqirryssfqdyzulybcvzuukkwvnbmjospiwnydoewokbcnyuwhkuycznzgwbtcpblnwehoviqidzkvmkbzxhlngkddmrohzablacaeqcntooumoitqytxqcgtelfqbqxcyecsrvhmyfxzhesqzccxplmozjlchfgpwefimshtuiirsruthplrhwujainwpcwiclhmchftoszvaumaghugxdqmihatueoaiecnfmaaonlkuizsslpkwhtzbfyftmhamaivrdfuchsifecojfwtmpqlvcxgkciadzbjhnuyghrrmwbvpwnraaixczsrmzqkczjnedaenoqhsjzfjsqyaztsbgcumcnwyptmcrrmkrflnoetfdlvwwnlcfsiuszigscgvdpujtohjrwrlnysvpzjoodwxwxqbraqhvudhbkocuoircbmgvgozcuiggsypnrkfmapokjojsuufuzwmtntlcyfbnkjtjujcmflsmopwqbarltkgxbbnigdykkslhcqzfupjmgmvfmzkmrvcqklhrpburmlqwktntmumvwpnsfkdrokrjofjscwbcqupshfxsiyqfyjjtmaolcfltsevnztaoaynayocpimsnxkeakkuonskhqlgvalldeunlrjhatifdevvaqxhlpeijlpstnvrxpijibezvvizuliwosjvjdtwhkppqqhzgnaagzndauxzxdkbavgaabjjsiidemygbfjpihixilrjcnhchmlkjqbcndghqspgsottieouubfuzhvpazsxjbxcdyylvxcogfgutouyeuxqgbavrovcjqwpuxduquwnmcooapjeggzmpimqqnvgvhskrcmtplobijmofdbxejaiuxdteyotrdutvfgyetkgrypalcepwcjmbtbtjtsisugnljhkvjuuitsmkbugwknoggroratqgpubokhihygmeeyvjjmgftrlszadvgyvtcuhugbqsbfxxocnafvgtivunliuizfjgndoduzkerfxulntbpoekrtcwxacinkldmvojhwmqapgncjhsahnpcdwnjrlixzutkbsuolwjcecgudeoymdrqupxryvithvmspbjnwuukxrlybtainqtjcjlzfwowyxoujteqjrzrnghvkifwgumpdtqodgzufjvitzfcvxtxethzqeylphdsqjowvhncbofbdkukhufzsqpttdshpgcaqxepjtofopyzoexhwadmwkdjcylvmehwlunvirzvuxxyuakojkywskfqkqdmxbkbyhabwsxfjnftmaeoagarscinxemlrlgjopjbwofiyhmnyqorulvxdqauuemjikfwrchyipdawodnsulpddasiutxbmijhriqdgbpquneuqyuicuysaejotrgfpigcwxfuyuvymmadadypyewdpzhhitwfrkqfmtliiglinottegexawxcrerhixyuibaobhguxdlieuuakkewnagrglglpjgecnicljartvsbrxtuigvdehsvucfvetkjhrajszwjiqfzszppiaoxyekkzsauarojgmxvzexyhzzgaejlcyrmckybmclrtimwhxscfrrcbghweycwhejigafuoifpteatsrzvnawfobsvonwkjohwzrbuaqibltgmprmkjbqohrilxodewhffixoklybqvaqypofwvahjrslwzxnyiuajyyeoinafxteiotztgmlzujbnprlsldjrwlelouwsnnqqzfgbamrzbmqyhezksfxvxrexbluzqzvvnlolbhlljsqntgspkvfxslwyqzavwkobomtaqakteomhzhjfhjwfeowbmckefspavlliddcnepgygsravmvxrmayigfvxwrccqtqvkpqwzioleuuxeupgykpevoqppvkznvuuumqwiwrfurujgaphapmcpapcudrvbsabarcoisocfmqkuhdmohjanogkavellbcukqsjycblmaxpbembxwckabhujgcmxeporcgmwudeluwawdqkpzpklcvfhkpxtslcqqhncidussgjhhurfrxdvkcxvmttbeytgxnkmrqzzjwkwgdghaaywqaztjhibyyvefotfzhddwpoyncsavcnmbzqykbtwqzjihxmvlxypblmesyldueovnufosakvuhtroftotpwslxwsmfxeavacoovfdufkqnzddtylhijexdftvgvfxwbrplszatptkrgzvkrvytpiymsknjsqqnxzkienfsrqbuvknpjjonnmpwoklfohevpblgpszdnixnlzioqazuayxhppchhcujdtxaxqbywegetgmfidvdzfpnwqhazyzpwmdqzsrunxunvgklldshiktzpntlkyhlbupgjsoxfofnrjvyfvopuhhsbprtigvwdlvbpezsjskjbnucndqhotjwfqslppenfjwphdrgjgfpimwvubfsoidewnqhsepofradmsmboklbszrbnfwsfkbgrijczcvsuezjfoegpzkqywzavktmfovxpmirlhbyhrdrsipifigdwycjlnntoqyrdowtzufzwuehuviszuugdyqeflhozgxvsmpacxvstqgaeutsptijusljbiflsolvwqjglrdamzjdberoyofopectdbspgivttivyaqvssmfuwqzidiubyewajxtyamhxyuvtevlqycodznkrokuwczchudezfjhemohlwgeqmzloalvohioyxcehqrvmksmrvlpdvemnbdnwbznjscxxqsgutknizsuqxacuhiotfvkwwbcvobuajdgidlxdkcwjxbfnnrjseqsgihbezaskpexaivxniyfdokmnrdcofnlzgooneeezolklhdjjccnusgjecovcqhdxwcbopjjrifkqxatvajfcltwvxlvgttwwrkpfmlqwviubqstymoybvbuklxpkxzfomthjinmcuarbmtadzqleuzlyjhojgcgbbcnwmoiwtftkgjmkghxbacyvgeqdmokwtpuotuunbpnecawvoarqqvppknbanxxrrovvqsotzazqdjacqqubhxcaecvraavtypktswrnzwolfdrrwwkdppaoccwnqbppypdwlvawdojerxlvcbkwnkbxhisduopsjqkwqzzbaouwgvgdpdozmbptiqzlxshpzulcufyazsxptunrtfsqljfmqppzsejdnxqudqfwemfdpbfcsmjffyvdalbuqxrrfiqijatmwenzkeukxljjdkjsoxvsugmbmfvnekajmdvlwhxthgqqmmvfaveivtelymifutbhvmpfdvkkkfjaachrmjeqmiqeargwfnsodmseqynxmdmknmjnztpbpfuwjfulbwjnwxkpssnniebngbdjnqsqdrphjnqeitylulhgvzsmlrhimyjhgxcqjbgdfxmhriwrhxykrgfwgatjlngpztlnynxuwgcobaqevvdeviiuxkmlbiprjkeosiuyomhefrdcrrmidzrpfezkqhocmxujvlljmkmztryecxeuutpudytalrjmyukwwadkqryuduposlswcmvgtguzznootphrkwrxltyiofaidmofvrhrinmyedcadotnupgonhstbmuwogbxtygnoljjnixlrdworfeiimaasigpzztrlooueyqpgwxqgtcgbpkntuqvziprnkiowjlnylcmyvhqamfmabwnesjyrvymdzseroqcnetahyagtxburweivmgqwdikdhlkuaosfiiwwwabienhcpneiodxnlogmhugedbpyshuddkoiedlvqpyamnyrngmnvphfyqpcnprnsmfloabjjutpoxvvxrjbudtbulouebhxzxehuebnmvpsztivetamgpyqjhiyvswiwzxvvnuemdgxjnaxqxrxpugvgrdhhtmqoynqnnbwvteoprpbhnmmrzsexdutgfwugjoejchbqpsrteehikxcdyaubhbedclsdcloqasqxgyntyjkaoqoisqycaiarautytgfobahcolwspnoxpoetqsziiuwsodrqvnbzzoywgxdwjbvfylzlyatwksaxcxumbaztdomeaekjkxcpauabhulrmdbemsfnkpylvicaeljuuiypylnujuaslzsztjckwvjcyxeyffltnkzabooknolytwsvzqevbzlkzfvxpswqqwtpbwcwonkngnxphfaxwemayozuxaanzbllrahhiczzyfiqzevswouwdcbdyyvaiprtfghmajqqyxzlkrexlzguzudhztlvyknvevsmljwepzlvdeotlbvywewoxtqydpksmxmrncdczeayvwzpejjbtftlgibcluorlrhhmcjksyvlyqldovfglqwnteibflqropnkuhhnbxkswmgcisboogxjcnrcylvkaczgizjevcddsegybeqmmfasqwipxhymcsrhzfuribgmmfgitlkghnuizsakxvnitokojjpstwgfngyosxicladivypvcbqzdtvxvwzjlwqiocrcaubdqpebeilvcegscbjwezuspkjaventqnqkwcbtkzhjdlhkmudrlkhidrnnlehnfdnfqlhooqiusqszxzeadlexmvxkubkgadnfzbinvywdqaibcnoipdvhmyilegfsqpznjeinriufygqtibhizvgtgfvkxoxwpuzinduymwlweewigqpxhwpoaoyjbgizlzwkfohdiobfdczdvdipochojyxqmbhnpgtacoageyxkfdvkvwqmerzyctaafhlhlzhsfrunylwxtwsjxhmuxmrvvgtteorjzwpzpbsibgndykltwrtgciuomsobxbtgnmoaclsgiwvjzarxlemsmouqnxzhtumzdsxjwdozkqzmwndjdhvoqdetuqgkixjjhicujqdnrcryeysgauxvcwckmjmdkwvntjtsxzrauxbxeeezdmpdjzjvycniahcofrlmouvkorpbsjzmgoxfabwsawgvmgrzlflgjoexsrbzjidfaosccexmumvqvoemseaalyiqlmkuomtmudjwctokgidzmljthzyjqrtkktdaodzivfpbrxkxowyxioqevmbtbsrfvfostkynublaihyvyjdruvtdgszviarpbnptxebggfllqyvecqlwfjrcvqtmemlspwcdqgjrlqymlrfpqrzghztioysfbcnlpixykydagknnusxwdsujpexgmdyvxnfnyakmpvymxyvkhqtmcdmuctlwpmdyglvzpsaatudjcahygswvdadrltcgbrocqfzwzzuwgwjavdcgcpgcujspcwziftdnkivndbdnnhrvrliwdzbyerzrzzvsgsjdqzozwyjckjofyvccaftvzgieoblxtujbnzadsvaefmnpluobeaiguvqiuwrylkemiwiubzrsokaohkjrmnhcewjsdrnfywphsxsxhkwxvlmhimmzjlhgpxqrdxlylfhbtifgkgghgdcgmydslauehlxvdicgcbafysbeaedmxokbavtcnfjeyayvuaseceqsuxqnsvauwamrwljlgnhrlfkwpezjzalibrcsvtcvgwcmhupzqsuxjmyqtiquupeictbdttilcsxqkymezrwqvvcwstsyggdqqpxaaolejpsrjzheqhpfjyfuchuxjrqfnlzonsbmcxgfozccrcfmgmrtxnhynwdgerjbuzgifvyutlahmuqngsgughamqmfjjhzoxcquxxhurrteoouucxebpbkwujkzlonmvhvzvxyagnfqklujhvsrhthtakoyblkbinbwwtomntbrtatmaprmzmdnxrugsoexdcnotkmfbzhvtrygqlbapnwqisxhdjwgflyppbrimnrkzhhqbzsxgetwkkplaxtxqyvxrchhucpgqpfnzpaudtxkcdismvtxocqezgfcazqwyxdbkufypkycbfjpwgbyvoglknuhffsoileioblqqzvskyfpwlpudjztuliqgvgoplunecfsswlcwfqwkohllqxsigsirswfaqaunnbwvmhqkylsymrqnvqiogcarlczhycdrormhkokxmsbwrnzogggzetnfygbpfyhudmduqjvofakbltngvcnywhmyfttzukmzdvcszglirverxhreefugdkrejcblycbycdcokzeoskustbjxqrekvrubzbokljnlnkvwwoxqxjqplyrujufiqywfkqtoswodregvlufkijnywzcrzmsqyqlyxnqilknelfwnevpawbkungbyzrzibyqmhwqbzzdjreerqggltdmoeponqpmjoznjeuisqdddjpywngwwdgjtobpbkdnslioyyrfqlvsoljlxtepupazvliudrjddotvdmpmwwlfudfggpvezkptxtdkbyxxliskprfhijgollhomejmviarxshpnlrssetrpnxbnbavuetlkvxrazpdhehdcpopvfulyctjeckochzcmgzaokyjtrpyouwqmwimgfngaltxzvwrjozibfmkdmvrowmicjpywexbfouubhgnnumgwayxasdytwnqkdggomfdtltvuhiphkfdfvucbgwutjobelzdlwsxbexaaxwqiuujoyeamiqvnpxzihgfwqwpmuyewdsuztxddjdihxbxythmnoaowcmofpjeuagawjqzlvhwvfleekacwfdihdlumgibluflfmqejydqgivrjwyoovnmiggxhvjdchmezpdkvopzeijajlbvxhxubzcssgjycpxlxifipdyubcukecetytaprsaiuobxwqgvczylmyxdzhlakkxxjkjjjtvlinljjoydpwfacslnwdrldshatqamjuplwjmrpbbnkzbskuyevsxxjyebbamfrslbznuxedkyqvgjsrebnquolzepeqihsfmdlgvjvjsfecmbczhxrxceymxgbcgvfrmctdrhpzppqmvucpohewmuxcgjlgzabpffmcisfcswlejqvsnpkrushamazowowoqdqiscwepuioeftmhysswmafxofwlieixqosmoadvbyeuswpfxvtpdwqhrixrafdmvqwymqdypxceplkzuxegkijuypmymtcmwozvtovlbrjjnzcwhldrbuejuyswnuggkzgunvnscgkzlwnhqqfudfvtwbzxabqjemyvyinnplykrvqrrpowemsbanwcyahitbwzlyukipljtsuewffxzvpimswfcevjfknkzyzfhdkpzawzchwkisqsxeselmqrmznliuibpyeqtzodsgjwgugkbecvwvruahyldggvvksmosihongioxctmtmutygcsmztmbeutbymawlregaptarponfskrubfzqixcsqlfxuctsjwaokhmtwxtlukkcgngfljiulklfntdbyngyvrnmejxqweygvpyintqhttoyojbtycfnjyfjpsfnxvmyksjkaiobczoaeklpygpvauqyrvbairunklubgztxklawtzxdosfxmpxqlxaokvasvwbtnasoniulhrjbqiafnxozscisxkfirevljtwmzcyqjjvakogrnrwomfvyxkciobqxqshqnlidbfgyhqpnwzvsutgljkqkjzuetujttyxesetnwqqbyjuamisceunqzqbkngakpahqxthkfeirhioqefxhhqmpxtlvpevhoujopvsdwtrfshqzgoxcxtjkxtamjjqrdkqdqldkhtjlenkjwgusaeosvunsvoiyoawwnbaaahddrthovwzygguqcpyrvzbwtrdjuwdzrasdygozgczwapyoscjxszlxhqnkxjyzvczlgyabhqdbctjapijijogecevfipylazlebfvcmlmxshxnucbosozvkerwlpoxavfsfgalosbgdgivmwbbyofzcsopskluznmplrrtmblvehusevftourftuilqhdnaxwmqtjpqqsqchgbpaeyuiguitkeivfljjebwaqmpltssbmcctfvfnihoihxmtalsyffoyrvmohsdqyrxvkpduudbjhabaiivepmmoajapotdweeengjqzkbapxyarzrcuvlptrqkwybtictenhameyclpqqklqqwlphlafvbwosaruwurrzssiemymmnfxkrrrdbzwgvypvcqtdfbiodlxvlepjjmduyjonhzbrtfrcnqtpjvivdvdcgfrjqjrkbviqonowkihcbqphmhcfrdkzlvpeypyjymzuxyffkeckclpsqvqpvieadwbjobcnuopdpighgvxyadsxjzfadmirsohwzikddjszhcdbfhfuntokrgrrkbvndtacjxnbohhvpvwxvhtjklsfnvcycahttnvzkxqdfjpdrwpglcntuhezbghqgjbzufweednrewqklbrnkdpkkycddjmexggepasrrsmxnrjkuqoqwpygbyxjyhoyrldebgihrvkzycqepjeuqdjnabcijwbwjkccxeudsqkphrrpvfvckmbgwfpfyerbdfuywituwtlmnrwsfinrwqgdhzqfaciysyjguivwmcubfmunwayqlczhwfbdcnannvdelizijqcgkcumanchgmuoeogdybtygszgrzewawfbeknajmdidczomtxamopxfnmtncjrgcedkfmmeubaiehanlgeqlkxzxkecfcrnimcmqsrbjxxiiuzagyeczogpczbrwzfbbyefhxjsfpjatymojcvhxmxmqsstjtwaoljdjafmhbgrvdmkigdaqiqplhlorpfqqomkjyralntxblewpkibbcbtlpxmsaxkiqgkvbucbwmzyjovjwgtzezoulptpdqyqgizzrhryxsepchdbzmltdzrynlynuqbykbbjuqwwwzvldajstrxjxplpzwxmfrapeumtqhyxzqlwdbceknqdjhqevdpvddrkrtxyhsyyqpvjfmxlbkqfhqzhgkaukdmguypevweqxqyyilijimniqapqzuyrrhhgcbigwttfhwprhuzmmpuqqpqlvrsdieuxxqmqushayycoigsmhnjcvanvsqztttvaauvpinjlridrnnigzltwrkruounolieyvauznzozxdhcdqlglbytznfppcgsjljvvxdfipelswceckyxfxwtcswigzdjsyuaesikehnbesfjwbjqpaexavyrfopdgdncdsypucjgmqmwiikfjzcvptkzezvutcvbousqkkhuomcywpkostqmokwqgmdkhcjtmtrmavnqkobbufjaqcnpblgyrkqyirlnkwpgfkhlbcyhkfrnwnxcvawhdzsmqzjpseuwrfiogojxlkhjdwxbtsdrmqrtpkaofjecastrvzcmfcdzhbqkeeuvjpkmjffqjydyqwatkgbqeqitzabglymqxhscumatwqkeukyzjrueitnejsfouuwjqxxierjsdsmmlhqvubdvrwsnnyfxzeezzteosphpdjjsknodsioizleuldmulbvontcjjxcflynjezuayjtcwqgdjvwuqgzergwjgifiieakcwxewrkyjqcjboqqnqcpknxkrwhaokyrklqjkveteuauyanevhtzlkxdgwignukbkpjefjkdayhlympvyhylugnsetmnkaxadwnydrpxxidsetbxpahnmeipcipmwttyuyomigzbtklmkwolhnwcbatqsptgmlngyfzdetyxjpeiwtropanodwfdofztoupzarylolcncqauatrpgavtrbercvmitgpzijaoiqvehctquhabsmbbkavhnzprwihywzzhcjrmrubyztoiveevmodmfuvcypxsgmmzwfryblahonkotoxyolyfpntwpmgxlyrbqckrtujdpdtstgvpqwgjfhpracrxmzynmitenxlprmcgorlvagzpzclaftuwznnwvytfzoibwwaaqydsldfhwhkdqmnyrtuvnnolyeblflhxanfcisagsuwolwzmwcuaiwberunhxqitaiwcyqlwnxuznsvnnilohpkyojziqixvnqmjdrqsbtdcclowngtnlfaizahamgyonrwtgppdwntyfaeqrknlpvsjpbqabueldrgxqfrvzjeghrvqncqvdenjbzbcybqxjffoqzytmkuoqaqmbjqsxhdmckaxvxzfihcsfboglilzxofvsvlscfzbdxzmzytoaphoikgonokmkegxcyhcihhrsecepisthklvxczerapolckqywrriuazqejpblnnwbsgjuztxvhgpwqjjvqopsjegnqeusqpccrqcnhoznjlrdetztnnymgjbydptfspdkkjxagiseajbwpbjfpykxdescugahvtknmtucoptihgarxrowabcjhgvrgvtlwugwbaqthhflsxkyobpbfmlebanynxnmuulhzokzabjefydqfskkwwblhwkfelkqvqntescjevjndfejkkkavuaaelinxumtshhcgjozqjlgmnosfekqjuwhwgbjhoegxevibdxqwyupcdhmvfscoucdqqxiopvexdkjsnspxciyyzbyhasfmuangtyjpzzdignrpcapnkqpbsgjiuodkxgajvucetxeqabxlzjqrqsyzwipegwenzjaocweufpqabwkzhplxjmkyyglmbairfudrxbazlylikbczrqijjaljkzihnwjwqrmqghpdwfwbsolpjlcuznrvdnyicgpzutwsgfqgfedwbqptfxuarmjqizoezafynbynwyrbbzduoqjtpkdngzmkpchxkjwzxwuqbfsdotxbwuryeqgciumbysslqrzljgbxctcnvnvbczvxxzjdeazhklwumjczszyyzpmrqqgutimxziwwgujabdgfvhjbpszpdlbogtoelujeuaqotxkjwalhdabfueqzrnirrelhcndzdebnctxkgxvlojlwnbqqimpvrufoiegyqfqdpdgbhcvaebouxykpdsvcdpjtrtnzuyfccuaawxilnpkyzyaqiucsorkgrzlbastjqgraezxexvtczfpsgysbliouocmeggzdfdwskvlkepctycsrtbxuqxnghfkcwqubiqhyzcxjybylbdcvvecyrwiejyzgujwdojcdwqsyjozfmaflktfrasxbbitzfsvbybkqwocjoennbhvrmryfjpiviqrdgstfrulfjsxffrssvvdezwdcqviggknebhdocmpabmkwyhzviozjhzmphoxwupneaqjdlckxqlqblgoujxsjvpsnhhejzifcfwabfqshqntxsrnyyjbduznmufsjfcxbrrfvlbeheyxkiajpnzzubkpxnfagtqbkgzmxqonjvvbtjbavgyvcfdehqebipocysigwrpqaelgxpartbgnlvbgzyjneguezlvqlvfmnwqsfiybpfefqqbspwndvacjbnpwlixdtpgmnzuurjyissxflfyiwsvpdvtfqvscvkuwphkkohfxckfxtzqluujontuxiwvmhrvbeunfhldljgghopwurmuskwvmakaohxyapyztjdcpnvoekuusepkefgakihbpqnyeyptoxfmqifswkzwncziclngcraenvkydahtnguueulmiynatinodizagjgehuoxfazfcpxrtknxzsbrvfjdrodnmcayuwsxagwmnywkwnmjxcijymlyuqbojiphrbarjodyfbpcynokhmkvlexnkbnftcydyekxbztzdqsrrbogsphcdrhmnhvxsnvcfbabtpxgzgavszsjqcnfwgyghsqqttweeapgaeyhozgfpkrayfgbspasjqhpyqlfktgswikewksfkcvdjfqjwlenyulzcmcdtgksstxrytrapaggvdanlcdohfelixzewglymysndbsddphplgkcggcdqvhchncrwgopohlquwxirqsekiklagemxiuyrrfpjtiuckupexvjdwvlckeewaboxvhxdxhbutbcemzfcwtmhbiaaxacffscovlcdvcgerzkuvintyghltojjbbjbkhiljpjjcmgksewmkniuklocfebwwovfpamelsbdepppiyekmxohpgaghgellbhrjckqgjuihconngsqwmudvcmdqiuspzchrldfweizvltfspijwmtbrhijbrnqcqqpjsuftmggwtvsrdonfzsdiqlptxliifvpfyldbmqqzinzjtcnyluppnlkxoraetyhstqmrbppdplxvzubwmdsltzfiicruhekkupximyncshsytnepmppveelkbfypygqiiocwuszimaovappownydnmotazjdcbihsenecdxhzevusnbvlnqugettmckdykcazjjjrgwxzokzcdkhhaqntakoexzrlalqeophjjcrlenfdnvvumraudsymzrskbreevoahtpmtossajpgkobduqesswqoifnglrtfsfiehaukbvugvgwouluugujrqtijvswaaistfulevugisqkagavyaizgrcrnjrccqjcircguuzxwqtqzbpcdklmitizcnidjuihsofbprfrhqpdheobdujxzbmxwzkszwiqifwphmxltlpmtesnyxhznvmiwoohrejqqultccjakrbfzyffmedtlhzcfguvyfrftdjhplpjnsufrsaweeslelcyvawqlqrwgkflxlqgvngjauzbawjaamwuxiyjncfqfyagbynmvzazctyjbxlxpnvsvppoupjnlnutzzhqxpzyfmsxolgaxbcgdlmisbheeqpjcyzyxecogsvqafwlbqwekfriyimrjabifkkuuiewrqmtbkmcoyurplmubumujqlfkfxrlpnivgiyramhpwvmqufftodsogqoeklldqjnxpqxokkqhmnqaofkqupwflqxqpgqjngtccfijqnweoanjkuchzxesxxryjdwghesxjkupimeygfhrdieluvrsbembquafuhemcjogzdcucmqaawpdyujyawsloqqbvxhzymcdtrcsyscvehdszzgzpsqxmblxrimnyyijngeiqhkuigzseqacvpxookxicmpnrctnxauwdgzuwppnvzulnyrybgpazernburplngvufesfttjjxekfnyllzhyaygjkvvrjfamjjcqqiakfgrchhnjttfozbxptragzxuelwaxhxmhknrpvzobwiqgncwlobflzshwjdvavisddwmfnxuaqozzjqlsdylrtfgxerfokxvclkiapnzvpxkqexbvavusluuigunilvfnagnfmlwbcjhbgbqszowomkghyrzjsuywtfjgbmkreqynmqyhnwxkmhxcukqpqonemjzcitkauxvdumqytiekyyzbaqgeqbbkgsajecexnwrkdzebzurychjocltmeijsykfbxsdbvgomzguznakxdthclijuokvsebzswzbxdiivmafdqiokrexqkyfexprzxpluwtsnpuqeezxqhrqeyqjlhysazizhifhugorhtdyrhrkkrryhpmfrvrqteeoukvodyevnvjrswayahoyoorulsocfmgsfdahjxxjdsfrmrfieruoopfcwwxkpzzosryytcjsvfhwamgjygznioczmlxprgtoahcgvguthmipvxutblcobdgramqnhoqpxgakblnnyghiobrtdeqlrnqebzjihjxkgapuoznuirbsvtwhyucffvtjjgppryecdunjdugbechwxwxualbwamaheexaunnguanktfrxsriwpvfyevelbfhitfxcfuzqhjutmfjynryevtphjccqsaapzqtumxjczpxrfisfqkvlyoldmvfmiudzjowmelypyrbkfegahskgujkaugndyyswnizgdtnunrfmlnjirdbhzswuawqshraiaexirlpiqywtzvoclxmuqitomkziyosktlbhqwikdmhvnygomceyvbsbxjmawgoxtuzrodquphbmaclfrlblxqqqpvtoibnweypzwhyyuhsjnsiajhuhnqdokeizvaqgcdnwytxixdhyhucdnrdrzmlmjyrsuiqjubwbafdqeaawaevldvsexugcvljaiqjgrwwxsfwqhnfkxpgbbadhqeptddekwuzlangezvxhvmqnakcdswnvhxxsoymllqizmqjwbfavcfeszmxpzvayitykffyvtzitkqratyiecswlpeyaxkrdpudhsrnhuumtigeasgudjerhipqxljyvyzpjxclzjslnrypzoabpwqgwbhxepdfplhsfzzxuyfhjbldlkfnybldjqbxgaavbhiuyhlxdfczdqulhudrmzgascuyqxbfuqbcfwbajhsftombvoecjztgeutytvurkjxysgtlbmqntdleylpmbzgfuzjbwyqeqhbykcvnqtpjmbzclmriiwcdhbckmzwhzrrpledgjwqiegpdaxqcukthdthiqhsejfpwbgrzmrdulfpdofklpefokppvdtagdviqxxobxmfuddootxxnxpljgttmrextprcjtgprfxbmbvbghkpniwaadpieewmnmgzbfxfnooxuloknmlpcgjyjbigvkeyatcaehehrpwsxaqauujlrdqijivruofltutulazdaoovyhxltlyyjufkuzwkosqjallldzvehkxsblzdlaqducsfkwaxbotkkgxhvuexeqdsyeddkijnjwunmrnclrpldsuklkaspmgmccfyxvmgieypspajzrpmngzqicsbtrjhphqbkfypstxmdctmojugdrstsxumtfmimzfshliordsjnnnuaachloeahdnprervpihczwaeywdpvepeahwgrzdxpsjuvafbtcbbmqtqjccfngbralvzipnzryxpuuqcslkpwbngaydyachkvatilmapweslfhqqksjhaituszvxoyuxwokoirxuwkgpfmwnqxrjwwxyynhjtdxlqutlrlohdaajymdclbrzzcdgmxotimcobzwdzmcxllxeyepayddkoubgaxgysljrpbvarsbxqlxmmpzvgjpigmgcvucuidbwabxjmkarrfyxjudqahwneibdymlmehkxbguwbgrlxfmkxqojrtgbcelxpbeqakwlstjzpbeezxuubjprushlzzoqzthjgiovlrnrvfwnyzlizpgcybwqrhdbypfagbknmoalwurbyrubtarbuzgzvabuncorfsqydczhoccrirwarkstluqaqueufrmdsmvpcbtuzuxpwhffkbxfbtdpqyjqjxlozmhgxnqsxycspwziqnrggtcthwpogbpqroionsvygecvbicrnbxgyhqfkxvpomjlvzjlzchrtlssmhqkvtglrhojygxrkywqacircxcegwqtpytmrddsmliqpjmekqoaxkmoladelsygbkbitsaiqupjkthtjavsqzpgqjddpmlgosowmcgcrydrerfjmvxkqubnhluljeaydiuevcjiklbsxlpmjzgomnzfdqgzmthfajaimiizofnlmbipvoczhryeltwbnlvvrofhklrahcjmevxntectkcgmelivupzjcbumwfhbjwjyydaugyrdsztsiinwamqqininmguasheijjnixjzamzjzuemglbjdsmmbvizrebbawerszgcmjhgptccyahoxhguumidqmtdhhbqybejjfsxdhsfoehzxcewbiaqgzffxdczeqlxpmvqljlnfiiwogeqykkjbcepzljkjvzkyhhqpyagziuwtrzgmzgbjrjaxoiekgefnzqnvrnnetxmbcbkedriymqnrxvajjhhgfeajydjeoxwtnbjvpqwhfipvzlicmviisevvrgicjteqonpvelpkvoabkgcztuklmpjdgauxojwhyynnrpiktvsxveobbxwxwrdadupvxgjymfoivtgdrdadwomejyighqqjeqsmqxlnspdqyjzdoypgnxspbszqsakssrtwgurmoljglhcjxjvfuztteqapmsnpvxhgoplxprngytyaihgekufijkfuavbhsdqtcwoufyfrvfwjhtniqygvalvxvnmuhxfansdotgjzjhvstiyorusaxzbdythnyyjishsxfgbktuijpdkmebtbmtxjempqeuxmjckdijfwqnixrchxfbaezzehwplwtzeayrjlmkxsrffrigbwpkaftriyrmyezzicrxkxcnyqcfdstolccskyqmarkbjltlhbwuxoqargryavweybgnxznxqosucxxzpzjqdyyhnzntufxemvcctfhcwyyurnlehfufqpjyjbhagtxroxmfgosgyqqphrwiqfvdcwuzhtstfzojntmzyvwrtbgiivnkftjzqzsrqvqlccxumkxwburcihgqnpruxddevgufnvzbtvpkcacvbgqcjcefgzxipzqogehbcjstqjrtwatgrofaydsievwgovcrnaopizxvaikuebqptndbcerkmmfbksfszwnkuxikqejrdaocqfljlbjivhxdwoyyqcbcnxsmbfarqfltdppfikhbezrpgtkkwagbjbcpgbhrjwllvfxwgsstkyeoxmryegyzglvocoxtgzvritxjtkygrleegfbvsjllsbxrhcbhpgdrpryuqjveyoqlmcywnjuecwpodsaenukaqfoheinkpjgophsnxyougermrnqmzwnrbiuloorzylzpdaeuqxwsvmsvhldrnfdnzghcvhslfmywgoctppfbxstsvphovowlqvihgiuxodxxcdplgepqslludxgguiswwcgenlclhmnstrimgvpzldfomptospvprenadibmossniavrmeybjxpfzvwpatmunszsntoghtoewqfasafjyusldkbnjvnzpghmfnqnfqaqwvstrfokxrpksifrxoiyfdeqpxe",
    "version": "ysmlgrdhuzpsgsqrlnvtipsgqxqsgxxairqytxedhhqptjrwpzdwyvyupyntjtpffmqurlzfmsjnfjzclokxujngv",
    "visibility": 3,
    "game_id": 1,
    "category_id": 1
}

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-PATCHmods--mod-">
</span>
<span id="execution-results-PATCHmods--mod-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHmods--mod-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHmods--mod-"></code></pre>
</span>
<span id="execution-error-PATCHmods--mod-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHmods--mod-"></code></pre>
</span>
<form id="form-PATCHmods--mod-" data-method="PATCH"
      data-path="mods/{mod}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHmods--mod-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHmods--mod-"
                    onclick="tryItOut('PATCHmods--mod-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHmods--mod-"
                    onclick="cancelTryOut('PATCHmods--mod-');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHmods--mod-" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>mods/{mod}</code></b>
        </p>
                <p>
            <label id="auth-PATCHmods--mod-" hidden>Authorization header:
                <b><code>Bearer </code></b><input type="text"
                                                                name="Authorization"
                                                                data-prefix="Bearer "
                                                                data-endpoint="PATCHmods--mod-"
                                                                data-component="header"></label>
        </p>
                <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <p>
                <b><code>mod</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="mod"
               data-endpoint="PATCHmods--mod-"
               data-component="url" required  hidden>
    <br>
            </p>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <p>
            <b><code>name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="name"
               data-endpoint="PATCHmods--mod-"
               data-component="body" required  hidden>
    <br>
<p>Must not be greater than 150 characters.</p>        </p>
                <p>
            <b><code>desc</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
                <input type="text"
               name="desc"
               data-endpoint="PATCHmods--mod-"
               data-component="body" required  hidden>
    <br>
<p>Must not be greater than 30000 characters.</p>        </p>
                <p>
            <b><code>version</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
                <input type="text"
               name="version"
               data-endpoint="PATCHmods--mod-"
               data-component="body"  hidden>
    <br>
<p>Must not be greater than 100 characters.</p>        </p>
                <p>
            <b><code>visibility</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="visibility"
               data-endpoint="PATCHmods--mod-"
               data-component="body" required  hidden>
    <br>
<p>Must be at least 1. Must not be greater than 4.</p>        </p>
                <p>
            <b><code>game_id</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="game_id"
               data-endpoint="PATCHmods--mod-"
               data-component="body" required  hidden>
    <br>
<p>Must be at least 1.</p>        </p>
                <p>
            <b><code>category_id</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="category_id"
               data-endpoint="PATCHmods--mod-"
               data-component="body"  hidden>
    <br>
<p>Must be at least 1.</p>        </p>
    
    </form>

            <h2 id="mod-GETmods">Mods</h2>

<p>
</p>

<p>Returns many mods, has a few options for searching the right mods</p>

<span id="example-requests-GETmods">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/mods?limit=11&amp;tags[]=1&amp;notInTags[]=1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/mods"
);

const params = {
    "limit": "11",
    "tags[]": "1",
    "notInTags[]": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETmods">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETmods" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETmods"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETmods"></code></pre>
</span>
<span id="execution-error-GETmods" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETmods"></code></pre>
</span>
<form id="form-GETmods" data-method="GET"
      data-path="mods"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETmods', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETmods"
                    onclick="tryItOut('GETmods');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETmods"
                    onclick="cancelTryOut('GETmods');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETmods" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>mods</code></b>
        </p>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                    <p>
                <b><code>limit</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="limit"
               data-endpoint="GETmods"
               data-component="query"  hidden>
    <br>
<p>How many mods should this return. Must be at least 1. Must not be greater than 50.</p>            </p>
                    <p>
                <b><code>tags</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="tags.0"
               data-endpoint="GETmods"
               data-component="query"  hidden>
        <input type="number"
               name="tags.1"
               data-endpoint="GETmods"
               data-component="query" hidden>
    <br>
<p>Must be at least 1.</p>            </p>
                    <p>
                <b><code>notInTags</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
                <input type="number"
               name="notInTags.0"
               data-endpoint="GETmods"
               data-component="query"  hidden>
        <input type="number"
               name="notInTags.1"
               data-endpoint="GETmods"
               data-component="query" hidden>
    <br>
<p>Filter out mods that are in these tags. Must be at least 1.</p>            </p>
                </form>

            <h2 id="mod-GETmods--mod-">Mod</h2>

<p>
</p>

<p>Returns a single mod</p>

<span id="example-requests-GETmods--mod-">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/mods/9" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/mods/9"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETmods--mod-">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETmods--mod-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETmods--mod-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETmods--mod-"></code></pre>
</span>
<span id="execution-error-GETmods--mod-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETmods--mod-"></code></pre>
</span>
<form id="form-GETmods--mod-" data-method="GET"
      data-path="mods/{mod}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETmods--mod-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETmods--mod-"
                    onclick="tryItOut('GETmods--mod-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETmods--mod-"
                    onclick="cancelTryOut('GETmods--mod-');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETmods--mod-" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>mods/{mod}</code></b>
        </p>
                    <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <p>
                <b><code>mod</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="mod"
               data-endpoint="GETmods--mod-"
               data-component="url" required  hidden>
    <br>
<p>The ID of the mod</p>            </p>
                    </form>

        <h1 id="users">Users</h1>

    

            <h2 id="users-GETuser">Current User</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns the currently authenticated user</p>

<span id="example-requests-GETuser">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETuser">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETuser" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETuser"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETuser"></code></pre>
</span>
<span id="execution-error-GETuser" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETuser"></code></pre>
</span>
<form id="form-GETuser" data-method="GET"
      data-path="user"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETuser', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETuser"
                    onclick="tryItOut('GETuser');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETuser"
                    onclick="cancelTryOut('GETuser');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETuser" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>user</code></b>
        </p>
                <p>
            <label id="auth-GETuser" hidden>Authorization header:
                <b><code>Bearer </code></b><input type="text"
                                                                name="Authorization"
                                                                data-prefix="Bearer "
                                                                data-endpoint="GETuser"
                                                                data-component="header"></label>
        </p>
                </form>

            <h2 id="users-GETusers--user-">User</h2>

<p>
</p>

<p>Returns a user</p>

<span id="example-requests-GETusers--user-">
<blockquote>Example request:</blockquote>


<pre><code class="language-bash">curl --request GET \
    --get "api.modworkshop3.net/users/17" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>

<pre><code class="language-javascript">const url = new URL(
    "api.modworkshop3.net/users/17"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre>
</span>

<span id="example-responses-GETusers--user-">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary>
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre>
        </details>         <pre>

<code class="language-json">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETusers--user-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETusers--user-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETusers--user-"></code></pre>
</span>
<span id="execution-error-GETusers--user-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETusers--user-"></code></pre>
</span>
<form id="form-GETusers--user-" data-method="GET"
      data-path="users/{user}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}'
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETusers--user-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETusers--user-"
                    onclick="tryItOut('GETusers--user-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETusers--user-"
                    onclick="cancelTryOut('GETusers--user-');" hidden>Cancel
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETusers--user-" hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>users/{user}</code></b>
        </p>
                    <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <p>
                <b><code>user</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
                <input type="number"
               name="user"
               data-endpoint="GETusers--user-"
               data-component="url" required  hidden>
    <br>
<p>The ID of the user</p>            </p>
                    </form>

    

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                    <a href="#" data-language-name="bash">bash</a>
                                    <a href="#" data-language-name="javascript">javascript</a>
                            </div>
            </div>
</div>
<script>
    $(function () {
        var exampleLanguages = ["bash","javascript"];
        setupLanguages(exampleLanguages);
    });
</script>
</body>
</html>