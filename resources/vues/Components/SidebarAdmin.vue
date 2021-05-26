<template>
    <a href="#" @click="onTrigger" :class="classes" >
        <svg xmlns="http://www.w3.org/2000/svg" class="inline-flex h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
        Administrative
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
                    { label:'User',href:route('user'),active:route().current('user') },
                    { label:'Report',href:route('report'),active:route().current('report') },
                    { label:'Cron Job',href:route('cronjob'),active:route().current('cronjob') },
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
