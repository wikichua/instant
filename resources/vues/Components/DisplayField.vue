<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <div v-if="type == 'text'" :id="id">
                    <span class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-html="getData()"></span>
                </div>
                <div v-if="type == 'json'" :id="id">
                    <pre class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ getData() }}</pre>
                </div>
                <div v-if="type == 'list'" :id="id">
                    <ul class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" >
                        <li v-for="item in getData()" v-html="item" class="py-1"></li>
                    </ul>
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
            data: String,
            options: Object,
            type: {
                default: 'text'
            },
        },
        data () {
            return {

            }
        },
        methods: {
            getData () {
                let data = _.isUndefined(this.data) == false ? this.data : this.html;
                if (_.isUndefined(this.options)) {
                    return data;
                }
                console.log(data);
                _.forEach(this.options, (option) => {
                    if (option.value == data) {
                        data = option.label;
                        return true;
                    }
                });
                return data;
            },
        }
    }
</script>

<style lang="css" scoped>
</style>
