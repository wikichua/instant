<template>
    <instant-content-card class="w-full xl:w-4/12 items-center">
        <template #content-title>
            Others
        </template>
        <slot name="prepend" />
        <instant-display-field :form="form" type="text" v-if="creator() !== false" :html="creator()" id="name" label="Created By"/>
        <instant-display-field :form="form" type="text" v-if="model.created_at !== undefined" :html="model.created_at" id="name" label="Created At"/>
        <instant-display-field :form="form" type="text" v-if="modifier() !== false" :html="modifier()" id="name" label="Updated By"/>
        <instant-display-field :form="form" type="text" v-if="model.updated_at !== undefined" :html="model.updated_at" id="name" label="Updated At"/>
        <slot name="append" />
    </instant-content-card>
</template>

<script>
    export default {
        props: {
            model: Object
        },
        data () {
            return {

            }
        },
        methods: {
            creator() {
                if (_.isUndefined(this.model)) {
                    return false;
                }
                if (_.isUndefined(this.model.created_by) || _.isNull(this.model.creator)) {
                    return false;
                }
                return _.isUndefined(this.model.creator) ? this.model.created_by : this.model.creator.name;
            },
            modifier() {
                if (_.isUndefined(this.model)) {
                    return false;
                }
                if (_.isUndefined(this.model.updated_by) || _.isNull(this.model.modifier)) {
                    return false;
                }
                return _.isUndefined(this.model.modifier) ? this.model.updated_by : this.model.modifier.name;
            },
        }
    }
</script>

<style lang="css" scoped>
</style>
