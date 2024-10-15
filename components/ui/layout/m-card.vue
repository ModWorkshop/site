<template>
    <m-content-block class="items-center justify-center" wrap style="width: 180px; height: 180px;">
        <span>
            <slot name="image"/>
        </span>
        <strong class="text-lg text-center">
            <slot name="title"/>
        </strong>
        <slot/>
    </m-content-block>
</template>