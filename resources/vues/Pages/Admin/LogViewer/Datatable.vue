<template>
    <table class="items-center w-full bg-transparent border-collapse">
        <thead>
            <tr>
                <th v-for="(column, index) in columns" :class="'p-2 py-3 bg-blueGray-50 text-blueGray-500 align-top border border-solid border-blueGray-100 text-sm uppercase border-l-0 border-r-0 font-semibold ' + column.class">
                    <span v-html="column.title" ></span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(model,index) in models.data" :key="model.id">
                <td v-for="(column) in columns" :class="'border-t-0 p-2 py-3 align-top border-l-0 border-r-0 text-sm ' + column.class">
                    <span v-if="column.data != 'content'" v-html="model[column.data]"></span>
                    <span v-if="column.data == 'content'">
                        <div class="mb-1">
                            <span v-html="model[column.data]"></span>
                            <button type="button" class="inline-flex border-2 border-black-900 rounded ml-3" @click="onDisplay(index)">More...</button>
                        </div>
                        <div class="text-xs" v-html="model['subcontent']" v-if="show[index]">
                        </div>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <instant-pagination :links="models.links" />
</template>

<script>
    import { reactive } from 'vue'

    export default {
        props: {
            columns: Array,
            models: Object,
        },
        data () {
            let shows = [];
            _.forEach(this.models.data, function(value,index) {
                shows[index] = false;
            });
            return {
                show: shows
            }
        },
        methods : {
            onDisplay(index) {
                this.show[index] = this.show[index] ? false : true;
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
