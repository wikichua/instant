<template>
    <div>
        <instant-show-link :href="route('audit.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
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

    },
}
</script>

<style lang="css" scoped>
</style>
