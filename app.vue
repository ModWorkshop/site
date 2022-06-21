<template>
  <div>
    <NuxtLayout>
      <NuxtPage/>
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