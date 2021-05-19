<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :value="label" class="block text-sm font-medium text-gray-700" />
                <div class="grid grid-cols-6 gap-4 w-full" v-for="(data, index) in form[objprop]" :key="index">
                    <textarea class="col-span-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mt-1 focus:ring-indigo-500 sm:text-sm" v-model="form[objprop][index]['key']" :disabled="disabled" rows="1"></textarea>
                    <textarea class="col-span-4 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mt-1 focus:ring-indigo-500 sm:text-sm" v-model="form[objprop][index]['value']" :disabled="disabled" rows="1"></textarea>
                    <div class="col-span-1 text-right align-middle py-3">
                        <button type="button" @click="plusRow(form[objprop])" :disabled="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button type="button" @click="minusRow(index, form[objprop])" :disabled="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
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
        disabled: {
            type: Boolean,
            default: false,
        }
    },
    data () {
        return {

        }
    },
    methods: {
        plusRow(form) {
            form.push({
                key: '',
                value: '',
            });
        },
        minusRow(index, form) {
            if (form.length > 1) {
                form.splice(index, 1);
            }
        },
    }
}
</script>

<style lang="css" scoped>
</style>
