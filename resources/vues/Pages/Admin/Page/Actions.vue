<template>
    <instant-show-link :href="route('page.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
    <instant-edit-link :href="route('page.edit',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.update" />
    <instant-delete-link :href="route('page.destroy',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete"/>
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
