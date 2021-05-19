<template>
  <div>
    <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="relative w-full my-6 mx-auto max-w-3xl">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
            <h6 class="text-3xl font-semibold">
              Advanced Filter
            </h6>
            <button class="p-1 ml-auto border-0" @click="emitter.emit('toggleModal')">
              <span class="bg-transparent text-red-900 opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </span>
            </button>
          </div>
          <!--body-->
          <div class="relative p-6 flex-auto">
            <slot />
          </div>
        </div>
      </div>
    </div>
    <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>
  </div>
</template>

<script>
  import { getCurrentInstance } from 'vue'

  export default {
    data() {
      return {
        showModal: false
      }
    },
    mounted() {
      this.emitter.on('toggleModal', () => {
        this.showModal = !this.showModal;
      });
    },
    methods: {
      // toggleModal: function(){
      //   this.showModal = !this.showModal;
      // }
    }
  }
</script>
