<template>
    <instant-show-link :href="route('failedjob.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
    <instant-post-link :href="route('failedjob.retry',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.retry" :confirmed="true" confirm-message="Do your wish to continue retry this?">
        <i class="fas fa-redo-alt h-6 w-6"></i>
    </instant-post-link>
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
    methods: {    },
}
</script>

<style lang="css" scoped>
</style>
