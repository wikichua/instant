<template>
    <div>
        <table class="items-center w-full bg-transparent border-collapse">
            <thead>
                <tr>
                    <th v-for="(column, index) in columns" class="p-2 py-3 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 text-sm uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                        <div v-if="column.data != 'actionsView'" v-html="column.title"></div>
                        <div v-if="column.data == 'actionsView'" class="text-center">
                            <slot name="header-action-slot" />
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(model) in models.data" :key="model.id">
                    <td v-for="(column, index) in columns" class="border-t-0 p-2 py-3 align-middle border-l-0 border-r-0 text-sm whitespace-nowrap text-left">
                        <div v-if="column.data != 'actionsView'" class="text-gray-900" v-html="model[column.data]"></div>
                        <div v-if="column.data == 'actionsView'" class="text-center">
                            <component :is="actionsComponent" :model="model"></component>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <instant-pagination :links="models.links" />
    </div>
</template>

<script>
    import { reactive } from 'vue'

    export default {
        props: {
            columns: Array,
            models: Object,
            actionsComponent: Object,
        },
        data () {
            return {
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
