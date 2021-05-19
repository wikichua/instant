<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700 inline-flex" />
                <input type="checkbox" class="ml-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" :value="option.value" v-model="form[objprop]" :disabled="disabled" ><span class="ml-1 text-gray-700 mr-3" v-html="option.label"></span>
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
            disabled: {
                type: Boolean,
                default: false,
            },
            objprop: String,
            option: Object,
        },
    }
</script>
