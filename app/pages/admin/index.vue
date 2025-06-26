<template>
    <m-flex gap="3" column>
        <h2>{{ $t('home_page') }}</h2>

        <m-alert>
            {{$t('admin_page_desc')}}
        </m-alert>

        <admin-glance/>
    </m-flex>
</template>