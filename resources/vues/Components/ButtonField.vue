<template>
    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
        <button :type="type" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <slot />
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        },
        data () {
            return {

            }
        }
    }
</script>

<style lang="css" scoped>
</style>
