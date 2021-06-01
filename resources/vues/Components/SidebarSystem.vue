<template>
    <a href="#" @click="onTrigger" :class="classes" >
        <svg xmlns="http://www.w3.org/2000/svg" class="inline-flex h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
        System
    </a>
    <div v-if="show">
        <hr class="my-1 md:min-w-full" />
        <div class="md:flex-col md:min-w-full flex flex-col list-none justify-center pl-2" v-for="(menu) in menus">
            <instant-nav-link :href="menu.href" :active="menu.active">
                {{ menu.label }}
            </instant-nav-link>
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            classes() {
                let active = false;
                _.forEach(this.menus, function(menu) {
                    if (menu.active) {
                        active = true
                        return true;
                    }
                });
                this.show = active;
                return active
                            ? 'text-pink-500 hover:text-pink-600 text-sm uppercase py-2 font-bold block'
                            : 'text-blueGray-700 hover:text-blueGray-500 text-sm block py-2 no-underline font-semibold'
            }
        },
        data () {
            return {
                show: false,
                menus: [
                    { label:'Role',href:route('role'),active:route().current('role') || route().current('role.*') },
                    { label:'Permission',href:route('permission'),active:route().current('permission') || route().current('permission.*') },
                    { label:'Setting',href:route('setting'),active:route().current('setting') || route().current('setting.*') },
                    { label:'Audit',href:route('audit'),active:route().current('audit') || route().current('audit.*') },
                    { label:'Log Viewer',href:route('logviewer'),active:route().current('logviewer') || route().current('logviewer.*') },
                    { label:'Failed Job',href:route('failedjob'),active:route().current('failedjob') || route().current('failedjob.*') },
                    { label:'Versionizer',href:route('versionizer'),active:route().current('versionizer') || route().current('versionizer.*') },
                    { label:'Caches',href:route('cache'),active:route().current('cache') || route().current('cache.*') },
                ],
            }
        },
        methods: {
            onTrigger () {
                this.show = this.show ? false:true;
            },

        }
    }
</script>

<style lang="css" scoped>
</style>
