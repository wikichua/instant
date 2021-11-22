<template>
    <Component
    :is="link.url ? 'inertia-link' : 'span'"
    v-for="link in links"
    :href="link.url"
    v-html="link.label"
    class="px-1"
    :class="link.url ? '' : 'text-gray-500'"
    />
</template>

<script>
    export default {
        props: {
            links: Array,
        }
    }
</script>
