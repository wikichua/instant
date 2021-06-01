<template>
    <instant-show-link :href="route('report.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
    <instant-edit-link :href="route('report.edit',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.update" />
    <form @submit.prevent="onExport(model.id,$event)" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.export">
        <button>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </button>
    </form>
    <instant-delete-link :href="route('report.destroy',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete"/>
</template>

<script>
export default {
    props: {
        model: Object,
    },
    data() {
        return {
            form: this.$inertia.form()
        }
    },
    methods: {
        onExport(id,event) {
            var input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", "_token");
            input.setAttribute("value", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            event.target.appendChild(input);
            event.target.method = 'post';
            event.target.action = route('report.export',[id]);
            event.target.submit();
        }
    },
}
</script>

<style lang="css" scoped>
</style>
