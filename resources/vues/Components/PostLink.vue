<template>
    <form @submit.prevent="onSubmit()" :class="class">
        <button>
            <slot />
        </button>
    </form>
</template>

<script>
    export default {
        props: {
            class: {
                default: '',
            },
            href: {
                default: '',
            },
            confirmed: {
                default: false,
            },
            confirmMessage: {
                default: 'Do you wish to continue?',
            }
        },
        data() {
            return {
                form: this.$inertia.form()
            }
        },
        methods: {
            onSubmit() {
                let confirmed = true;
                if (this.confirmed) {
                    confirmed = confirm(this.confirmMessage);
                }
                if (confirmed) {
                    this.form.post(this.href);
                }
            },
        }
    }
</script>
