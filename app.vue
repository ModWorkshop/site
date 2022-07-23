<template>
	<div>
		<NuxtLayout>
			<NuxtPage/>
			<NuxtLoadingIndicator/>
		</NuxtLayout>
  </div>
</template>

<script setup>
useHead({
	titleTemplate: (titleChunk) => {
		return titleChunk ? `${titleChunk} - ModWorkshop` : 'ModWorkshop';
    },
	title: undefined,
});
</script>

<style>
button, input, optgroup, select, textarea {
	font-size: 1em;
}

a, abbr, acronym, address, applet, article, aside, audio, b, big, blockquote, body, canvas, caption, center, cite, code, dd, del, details, dfn, div, dl, dt, em, embed, fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, html, i, iframe, img, ins, kbd, label, legend, li, mark, menu, nav, object, ol, output, p, pre, q, ruby, s, samp, section, small, span, strike, strong, sub, summary, sup, table, tbody, td, tfoot, th, thead, time, tr, tt, u, ul, var, video {
	font-size: 1em;
}

h1, h2 {
	border-bottom: 1px solid #424242;
	padding-bottom: .3em;
}

h1, h2, h3, h4, h5, h6 {
	margin-top: 0;
	margin-bottom: .5rem;
}

/* p {
    margin-top: 0;
    margin-bottom: 1rem;
} */

dl, ol, ul {
  margin-top: 0;
  margin-bottom: 1rem;
}
</style>