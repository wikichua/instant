<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('setting')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card>
            <template #content-title>
                Listing
            </template>
            <template #content-actions>
                <div class="justify-center rounded-sm text-sm mb-4" role="group" style="transition:all .15s ease">
                    <button class="font-bold bg-indigo-500 text-white hover:bg-indigo-400 px-4 py-2 mx-0 outline-none focus:shadow-outline" @click="emitter.emit('toggleModal')">Filter</button>
                    <!-- <button class="font-bold bg-indigo-500 text-white hover:bg-indigo-400 px-4 py-2 mx-0 outline-none focus:shadow-outline">Second</button>
                    <button class="font-bold bg-indigo-500 text-white hover:bg-indigo-400 px-4 py-2 mx-0 outline-none focus:shadow-outline">Third</button> -->
                </div>
            </template>
            <instant-datatable :models="models" :columns="columns" :actionsComponent="InstantActions">
                <template #header-action-slot>
                    <instant-create-link :href="route('setting.create')" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="can.create" />
                </template>
            </instant-datatable>
            <instant-search-modal>
                <instant-search />
            </instant-search-modal>
        </instant-content-card>
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'
    import InstantActions from '@/Pages/Admin/Setting/Actions'
    import InstantSearch from '@/Pages/Admin/Setting/Search'
    import { reactive } from 'vue'

    export default {
        components: {
            AuthenticatedLayout,
            InstantActions,
            InstantSearch,
        },
        props: {
            auth: Object,
            errors: Object,
            columns: Array,
            models: Object,
            can: Object,
        },
        computed () {
        },
        data() {
            return {
                InstantActions: InstantActions,
                form: this.$inertia.form()
            }
        },
        methods: {
        },
    }
</script>
