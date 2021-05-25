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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                        </svg>
                        {{ folder }}
                    </inertia-link>
                    <ul v-if="folder == data['current_folder']" v-for="(file, key) in data['folder_files']" class="ml-5">
                        <li>
                            <inertia-link :href="route('logviewer',[folder,file])">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                {{ file }}
                            </inertia-link>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul v-for="(file, key) in data['files']" class="px-2">
                <li>
                    <inertia-link :href="route('logviewer',['root',file])">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        {{ file }}
                    </inertia-link>
                </li>
            </ul>
        </instant-content-card>
        <instant-content-card class="w-full xl:w-10/12 items-center">
            <template #content-title>Logs</template>
            <template #content-actions>
                <form @submit.prevent="onDownload($event)" class="justify-center rounded-sm text-sm mb-4 inline-flex" role="group" style="transition:all .15s ease" v-if="can.download && data['current_file'] != ''">
                    <button class="font-bold bg-indigo-500 text-white hover:bg-indigo-400 px-4 py-2 mx-0 outline-none focus:shadow-outline">Download</button>
                </form>
                <form @submit.prevent="onDelete($event)" class="justify-center rounded-sm text-sm mb-4 inline-flex" role="group" style="transition:all .15s ease" v-if="can.delete">
                    <button class="font-bold bg-indigo-500 text-white hover:bg-red-400 px-4 py-2 mx-0 outline-none focus:shadow-outline">Delete</button>
                </form>
            </template>
            <datatable :models="data['logs']" :columns="columns">
            </datatable>
        </instant-content-card>
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'
    import Datatable from '@/Pages/Admin/LogViewer/Datatable'
    import { reactive } from 'vue'

    export default {
        components: {
            AuthenticatedLayout,
            Datatable,
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
            onDownload(event) {
                var input = document.createElement("input");
                input.setAttribute("type", "hidden");
                input.setAttribute("name", "_token");
                input.setAttribute("value", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                event.target.appendChild(input);
                event.target.method = 'post';
                event.target.action = route('logviewer',[this.data['current_folder'],this.data['current_file']]);
                event.target.submit();
            },
            onDelete(event) {
                var input = document.createElement("input");
                input.setAttribute("type", "hidden");
                input.setAttribute("name", "_token");
                input.setAttribute("value", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                event.target.appendChild(input);
                var input = document.createElement("input");
                input.setAttribute("type", "hidden");
                input.setAttribute("name", "_method");
                input.setAttribute("value", 'delete');
                event.target.appendChild(input);
                event.target.method = 'post';
                event.target.action = route('logviewer',[this.data['current_folder'],this.data['current_file']]);
                event.target.submit();
            }
        },
    }
</script>
