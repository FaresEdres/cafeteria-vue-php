<template>
    <div v-if="show" class="modal-overlay">
      <div class="modal-container">
        <h2 class="text-xl font-bold mb-4">Edit Order</h2>
        <form @submit.prevent="submitEdit">
          <div class="mb-4">
            <label for="comment" class="block text-sm font-medium text-gray-700">Comment</label>
            <textarea
              id="comment"
              v-model="order.comment"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-coffee-500 focus:ring-coffee-500 sm:text-sm"
            ></textarea>
          </div>
          <div class="flex justify-end gap-4">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "OrderEditModal",
    props: {
      show: {
        type: Boolean,
        required: true,
      },
      order: {
        type: Object,
        required: true,
      },
    },
    methods: {
      async submitEdit() {
        try {
          // Emit the updated order to the parent component
          this.$emit("update", this.order);
        } catch (error) {
          console.error("Failed to update order:", error.message);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 50;
  }
  
  .modal-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
  }
  </style>