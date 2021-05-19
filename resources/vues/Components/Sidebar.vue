<template>
  <nav
    class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6"
  >
    <div
      class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto"
    >
      <!-- Toggler -->
      <button
        class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
        type="button"
        v-on:click="toggleCollapseShow('bg-white m-2 py-3 px-6')"
      >
        <i class="fas fa-bars"></i>
      </button>
      <!-- Brand -->
      <inertia-link class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0" :href="route('dashboard')" >
        <instant-application-logo class="w-10 h-10 inline-block" /> {{ $page.props.brand }}
      </inertia-link>
      <!-- User -->
      <ul class="md:hidden items-center flex flex-wrap list-none">
        <li class="inline-block relative">
          <instant-notification-dropdown></instant-notification-dropdown>
        </li>
        <li class="inline-block relative">
          <instant-user-dropdown></instant-user-dropdown>
        </li>
      </ul>
      <!-- Collapse -->
      <div class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded" v-bind:class="collapseShow">
        <!-- Collapse header -->
        <div class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-blueGray-200">
          <div class="flex flex-wrap">
            <div class="w-6/12">
              <inertia-link class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0" :href="route('dashboard')">
                <instant-application-logo class="w-10 h-10 inline-block" /> {{ $page.props.brand }}
              </inertia-link >
            </div>
            <div class="w-6/12 flex justify-end">
              <button type="button" class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent" v-on:click="toggleCollapseShow('hidden')">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
        <!-- Form -->
        <form class="mt-6 mb-4 md:hidden">
          <div class="mb-3 pt-0">
            <input type="text" placeholder="Search" class="border-0 px-3 py-2 h-12 border border-solid  border-blueGray-500 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-base leading-snug shadow-none outline-none focus:outline-none w-full font-normal"/>
          </div>
        </form>
        <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4 no-underline">
          Adminstrative
        </h6>
        <hr class="my-4 md:min-w-full" />
        <!-- Navigation -->
        <ul class="md:flex-col md:min-w-full flex flex-col list-none justify-center">
          <instant-nav-link :href="route('dashboard')" :active="route().current('admin')">
            Dashboard
          </instant-nav-link>
          <instant-nav-link :href="route('user')" :active="route().current('admin.user')">
            User
          </instant-nav-link>
          <instant-nav-link :href="route('role')" :active="route().current('admin.role')">
            Role
          </instant-nav-link>
          <instant-nav-link :href="route('permission')" :active="route().current('admin.permission')">
            Permission
          </instant-nav-link>
          <instant-nav-link :href="route('setting')" :active="route().current('admin.setting')">
            Setting
          </instant-nav-link>
          <instant-nav-link :href="route('audit')" :active="route().current('admin.audit')">
            Audit
          </instant-nav-link>
        </ul>
        <!-- <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4 no-underline">
          Documentation
        </h6> -->
        <!-- <hr class="my-4 md:min-w-full" /> -->
        <!-- Navigation -->
        <!-- <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
          <li class="inline-flex">
            <a
              class="text-blueGray-700 hover:text-blueGray-500 text-sm block mb-4 no-underline font-semibold"
              href="#/documentation/styles"
              ><i
                class="fas fa-paint-brush mr-2 text-blueGray-400 text-base"
              ></i>
              Styles</a
            >
          </li>
          <li class="inline-flex">
            <a
              class="text-blueGray-700 hover:text-blueGray-500 text-sm block mb-4 no-underline font-semibold"
              href="#/documentation/alerts"
              ><i class="fab fa-css3-alt mr-2 text-blueGray-400 text-base"></i>
              CSS Components</a
            >
          </li>
          <li class="inline-flex">
            <a
              class="text-blueGray-700 hover:text-blueGray-500 text-sm block mb-4 no-underline font-semibold"
              href="#/documentation/vue/alerts"
              ><i class="fab fa-vuejs mr-2 text-blueGray-400 text-base"></i>
              VueJS</a
            >
          </li>
          <li class="inline-flex">
            <a
              class="text-blueGray-700 hover:text-blueGray-500  text-sm block mb-4 no-underline font-semibold"
              href="#/documentation/react/alerts"
              ><i class="fab fa-react mr-2 text-blueGray-400 text-base"></i>
              React</a
            >
          </li>
          <li class="inline-flex">
            <a
              class="text-blueGray-700 hover:text-blueGray-500  text-sm block mb-4 no-underline font-semibold"
              href="#/documentation/angular/alerts"
              ><i class="fab fa-angular mr-2 text-blueGray-400 text-base"></i>
              Angular</a
            >
          </li>
          <li class="inline-flex">
            <a
              class="text-blueGray-700 hover:text-blueGray-500  text-sm block mb-4 no-underline font-semibold"
              href="#/documentation/javascript/alerts"
              ><i class="fab fa-js-square mr-2 text-blueGray-400 text-base"></i>
              Javascript</a
            >
          </li>
        </ul> -->
      </div>
    </div>
  </nav>
</template>
<script>
export default {
  data() {
    return {
      collapseShow: "hidden"
    };
  },
  methods: {
    toggleCollapseShow: function(classes) {
      this.collapseShow = classes;
    }
  },
};
</script>
