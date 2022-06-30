<template>
<page-block>
    <div v-if="game">
        <img v-if="game.banner" src="/mydownloads/images/{{game.banner}}" class="d-flex card-img-top" style="width: 100%;height: 265px;object-fit: cover;">
        <div v-else class="d-flex card-img-top" style="background-image: url('/mws/assets/images/default_banner.webp');background-position: center;height: 265px;100%;">
            <strong style="font-size: 42pt;" class="ml-2 align-self-end">
                {{game.name}}
            </strong>
        </div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark2">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li v-for="button in buttons" class="nav-item">
                    <a class="nav-link" href="{{button[1]}}">{{button[0]}}</a>
                </li>
            </ul>
        </div>
        </nav>
    </div>
    <div v-if="cats.length > 0" class="mt-3 p-2 bg-dark2">
        <h4 class="text-center text-primary">{{type == 2 ? lang.mydownloads_categories : type == 1 ? lang.mods_games : lang.mydownloads_sub_categories}}</h4>
        <br>
        <div class="cats_grid">
            <div v-for="cat in cats" class="category">
                <a v-if="cat.thumbnail" class="d-block ratio-image-cat-thumb" href="category/{{id}}">
                    <img data-src="{{cat.thumbnail}}" class="ratio-image lazy">
                </a>
                <div class="p-2 w-100 text-center">
                    <a class="w-100" style="overflow: hidden;word-break: break-word;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;" href="/{{url}}/{{id}}" title="{{cat.name}}">{{cat.name}}</a>
                    <a v-if="cat.subcats && cat.subcats.length > 0" class="d-inline-block dropdown" style="display: inline-block;font-size: 13px;">
                        <a class="dropdown-toggle text-body" href="#" id="dropdown_{{catc.id}}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
                        <div class="dropdown-menu" boundary="viewport" aria-labelledby="dropdown_{{catc.id}}">
                            <h5 class="dropdown-header">{{type == 1 ? $t('categories') : $t('subcategories')}}</h5>
                            <a v-for="subcat in cat.subcats" class="dropdown-item" href="/category/{{subcat.cid}}">{{subcat.name}}</a>
                        </div>
                    </a>
                    <small> {{$t('downloads_number', cat.download_count)}}</small>
                </div>
            </div>
        </div>
    </div>
    <mod-list :forced-game="game.id"/>
</page-block>
</template>
<script setup>
const isModerator = false;
const cats = [];
const route = useRoute();
const { data: game } = await useAPIFetch(`games/${route.params.id}`);
</script>