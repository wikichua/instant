<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6" v-if="grouped">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <div v-for="(groups, label) in options">
                    <instant-label :value="label" class="block text-sm font-bold text-gray-700 my-3" />
                    <label class="inline-flex items-center my-1" v-for="(option) in groups">
                        <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" :value="option.value" v-model="form[objprop][option.value]" :disabled="disabled" ><span class="ml-1 text-gray-700 mr-3" v-html="option.label"></span>
                    </label>
                </div>
                <instant-input-error :message="$page.props.errors[id]" />
            </div>
            <div class="col-span-6" v-if="!grouped">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <label class="inline-flex items-center my-1" v-for="(option, index) in options">
                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" :value="option.value" v-model="form[objprop][option.value]" :disabled="disabled" ><span class="ml-1 text-gray-700 mr-3" v-html="option.label"></span>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from '@vueform/multiselect'
    export default {
        components: {
            Multiselect
        },
        props: {
            id: String,
            label: String,
            form: Object,
            grouped: {
                type: Boolean,
                default: false,
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            objprop: String,
            options: {
                type: Object,
                default: [
                    {
                        label: 'Please Select',
                        value: null,
                    },
                ],
            },
        },
        data () {
            return {
            }
        },
        methods: {
        }
    }
</script>
<style lang="css" scoped>
</style>
<style src="@vueform/multiselect/themes/default.css"></style>
