<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <div v-if="type == 'text'">
                    <span :id="id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-html="html"></span>
                </div>
                <div v-if="type == 'json'">
                    <pre :id="id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ html }}</pre>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            id: String,
            label: String,
            html: String,
            type: {
                default: 'text'
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
