<template>
    <table class="items-center w-full bg-transparent border-collapse">
        <thead>
            <tr>
                <th v-for="(column, index) in columns" :class="'p-2 py-3 bg-blueGray-50 text-blueGray-500 align-top border border-solid border-blueGray-100 text-sm uppercase border-l-0 border-r-0 font-semibold ' + column.class">
                    <span v-if="column.data != 'actionsView'" v-html="column.title" ></span>
                    <span v-if="column.data == 'actionsView'" class="">
                        <slot name="header-action-slot" />
                    </span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(model) in models.data" :key="model.id">
                <td v-for="(column, index) in columns" :class="'border-t-0 p-2 py-3 align-top border-l-0 border-r-0 text-sm ' + column.class">
                    <span v-if="column.data != 'actionsView'" v-html="model[column.data]"></span>
                    <span v-if="column.data == 'actionsView'">
                        <component :is="actionsComponent" :model="model"></component>
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
            actionsComponent: Object,
        },
        data () {
            return {
            }
        },
        methods : {
        }
    }
</script>

<style lang="css" scoped>
</style>
