<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('logviewer')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-2/12 items-center">
            <template #content-title>Directories</template>
            <ul v-for="(folder, key) in data['folders']" :key="key" class="px-2">
                <li>
                    <inertia-link :href="route('logviewer',[folder])">
                        {{ folder }}
                    </inertia-link>
                    <ul v-if="folder == data['current_folder']" v-for="(file, key) in data['folder_files']">
                        <li>
                            <inertia-link :href="route('logviewer',[folder,file])">
                                {{ file }}
                            </inertia-link>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul v-for="(file, key) in data['files']" class="px-2">
                <li>
                    <inertia-link :href="route('logviewer',['root',file])">
                    {{ file }}
                    </inertia-link>
                </li>
            </ul>
        </instant-content-card>
        <instant-content-card class="w-full xl:w-10/12 items-center">
            <template #content-title>Logs</template>
            <instant-datatable :models="data['logs']" :columns="columns">
            </instant-datatable>
        </instant-content-card>
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'
    import { reactive } from 'vue'

    export default {
        components: {
            AuthenticatedLayout,
        },
        props: {
            auth: Object,
            errors: Object,
            data: Object,
            can: Object,
            columns: Object,
        },
        computed () {
        },
        data() {
            return {
                form: this.$inertia.form()
            }
        },
        methods: {
        },
    }
</script>
