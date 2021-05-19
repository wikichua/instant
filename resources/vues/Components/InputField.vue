<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <input :id="id" :type="type" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form[objprop]" :disabled="disabled">
                <instant-input-error :message="$page.props.errors[id]" />
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            id: String,
            label: String,
            form: Object,
            objprop: String,
            type: {
                default: 'text'
            },
            disabled: {
                default: false,
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
