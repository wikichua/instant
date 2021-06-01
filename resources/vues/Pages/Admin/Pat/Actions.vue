<template>
    <instant-delete-link :href="route('pat.destroy',[$page.props.user_id, model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete"/>
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
}
</script>

<style lang="css" scoped>
</style>
