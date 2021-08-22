<template>
    <div class="layout">
        <Header>Header</Header>
        <main><Nuxt/></main>
        <Footer>Footer</Footer>
    </div>
</template>
<style scoped>
    .layout {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr auto;
        grid-template-areas: 
            'header'
            'main'
            'footer';
    }

    main {
        grid-area: main;
        display: flex;
        flex-direction: column;
        padding: 1rem;
        align-items: center;
    }
</style>