<template>
    <page-block>
        <content-block>
            <a-markdown :text="tos"/>
        </content-block>
    </page-block>
</template>
<script setup lang="ts">
const tos = `
To use our API you must agree to our TOS, not following the TOS may lead your API key to be terminated.
User API keys are a little less limited.

1. You may not fetch mods of our site and list them in a game or program without contacting us about it beforehand.
Rationale: The site costs money to maintain, we don't wish to avert people from using the site, and so ad revenue
is very much important.

2. Obviously, do not spam the API.
`;
</script>