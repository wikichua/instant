<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <!-- <select :id="id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form[objprop]" multiple="multiple">
                    <option value="">Please Select</option>
                    <option v-for="(option) in options" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select> -->
                <Multiselect v-if="tags == false && multiple" v-model="form[objprop]" :options="options" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" :searchable="true" mode="multiple" :disabled="disabled"/>

                <Multiselect v-if="multiple == false && tags" v-model="form[objprop]" :options="options" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" :searchable="true" :createTag="true" mode="tags" :disabled="disabled"/>

                <Multiselect v-if="tags == false && multiple == false" v-model="form[objprop]" :options="options" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" :searchable="true" :disabled="disabled"/>
                <instant-input-error :message="$page.props.errors[id]" />
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
            multiple: {
                type: Boolean,
                default: false,
            },
            tags: {
                type: Boolean,
                default: false,
            },
            objprop: String,
            options: Object,
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
<style src="@vueform/multiselect/themes/default.css"></style>
