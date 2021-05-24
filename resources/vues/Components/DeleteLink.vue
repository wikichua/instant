<template>
    <form @submit.prevent="onDelete()" :class="class">
        <button>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
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
            }
        },
        data() {
            return {
                form: this.$inertia.form()
            }
        },
        methods: {
            onDelete() {
                if (confirm('Do you wish to continue remove this?')) {
                    // this.$inertia.delete(this.route('report.destroy',[id]));
                    this.form.delete(this.href, {
                        // onFinish: () => this.form.reset(['group','permissions']),
                    });
                }
            },
        }
    }
</script>
