<template>
    <inertia-link :href="href" :class="class">
        <slot />
    </inertia-link>
</template>

<script>
    export default {
        props: ['href', 'class'],
    }
</script>
