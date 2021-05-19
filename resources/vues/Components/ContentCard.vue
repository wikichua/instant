<template>
    <div :class="classes">
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
            <div class="rounded-t mb-0 px-4 py-3 border-0">
                <div class="flex flex-wrap items-center">
                    <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                        <h3 class="font-semibold text-base text-blueGray-700">
                            <slot name="content-title" />
                        </h3>
                    </div>
                    <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                        <slot name="content-actions" />
                    </div>
                </div>
            </div>
            <div class="block w-full overflow-x-auto">
                <instant-toast />
                <slot />
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['class'],
        computed: {
            classes() {
                return this.$props.class == undefined
                            ? 'w-full px-2'
                            : this.$props.class + ' px-2'
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
