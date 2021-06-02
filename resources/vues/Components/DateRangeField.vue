<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <input :id="id" type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form[objprop]" :disabled="disabled" v-if="disabled">
                <date-picker v-model="form[objprop]" is-range v-if="!disabled" class="border-0">
                    <template v-slot="{ inputValue, inputEvents }">
                        <div class="flex">
                          <input :value="inputValue.start" v-on="inputEvents.start" class="border px-2 py-1 w-full rounded focus:outline-none focus:border-indigo-300"/>
                          <svg class="w-6 h-6 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                          </svg>
                          <input :value="inputValue.end" v-on="inputEvents.end" class="border px-2 py-1 w-full rounded focus:outline-none focus:border-indigo-300"/>
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
            return {
                date: new Date(),
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
