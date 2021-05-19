<template>
    <label class="block font-medium text-sm text-gray-700">
        <span v-if="value" v-html="value"></span>
        <span v-else><slot /></span>
    </label>
</template>

<script>
    export default {
        props: ['value']
    }
</script>
