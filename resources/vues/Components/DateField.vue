<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <input :id="id" type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form[objprop]" :disabled="disabled" v-if="disabled">
                <date-picker v-model="form[objprop]" v-if="!disabled" class="border-0">
                    <template v-slot="{ inputValue, inputEvents }">
                        <div class="flex">
                            <input class="border px-2 py-1 w-full rounded focus:outline-none focus:border-indigo-300" :value="inputValue" v-on="inputEvents"/>
                        </div>
                    </template>
                </date-picker>
                <instant-input-error :message="$page.props.errors[id]" />
            </div>
        </div>
    </div>
</template>

<script>
    import { DatePicker } from 'v-calendar';

    export default {
        components: {
            DatePicker,
        },
        props: {
            id: String,
            label: String,
            form: Object,
            objprop: String,
            disabled: {
                type: Boolean,
                default: false,
            },
        },
        data () {
            console.log(this.disabled);
            return {
                date: new Date(),
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
