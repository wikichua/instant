<template>
    <div>
        <instant-show-link :href="route('failedjob.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
        <form @submit.prevent="onRetry(route('failedjob.retry',[model.id]))" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.retry">
            <button>
                <i class="fas fa-redo-alt h-6 w-6"></i>
            </button>
        </form>
    </div>
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
        onRetry(href) {
            this.form.post(href);
        }
    },
}
</script>

<style lang="css" scoped>
</style>
