<template>
    <div>
        <div v-if="hasErrors">
            <div class="font-medium text-red-600 p-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Whoops! Something went wrong.

                <ul class="mt-3 list-disc list-inside text-sm">
                    <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            errors() {
                return this.$page.props.errors;
            },

            hasErrors() {
                return Object.keys(this.errors).length > 0;
            },
        },

        data() {
        },
    }
</script>
