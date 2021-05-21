<template>
  <div class="flex flex-wrap">
    <div class="w-full">
      <ul class="flex mb-0 list-none flex-wrap pt-3 pb-4 flex-row">
        <li class="-mb-px mr-2 last:mr-0 flex-auto text-center" v-for="(model, index) in models" :key="index">
          <a class="text-xs font-bold uppercase px-5 py-3 shadow-lg rounded block leading-normal" v-on:click="toggleTabs(index)" v-bind:class="{'text-pink-600 bg-white': openTab !== index, 'text-white bg-pink-600': openTab === index}">
            Query {{ index + 1 }}
          </a>
        </li>
      </ul>
      <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded">
        <div class="px-4 py-5 flex-auto">
          <div class="tab-content tab-space" v-for="(model, index) in models" :key="index">
            <div v-bind:class="{'hidden': openTab !== index, 'block': openTab === index}">
                <div class="text-gray-500 p-2 text-center">
                    <i class="fas fa-quote-left"></i>
                    <span class="mx-5">{{ model.sql }}</span>
                    <i class="fas fa-quote-right"></i>
                </div>
                <instant-datatable :models="model" :columns="model.columns"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    models: Object,
  },
  data() {
    return {
      openTab: 0
    }
  },
  methods: {
    toggleTabs: function(tabNumber){
      this.openTab = tabNumber
    }
  }
}
</script>

<style lang="css" scoped>
</style>
