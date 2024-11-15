<template>
  <span class="label-required justify-content-start gap-1">
    <label :for="$props.for">{{ $props.label }}</label>
    <span class="required" v-if="required">*</span>
  </span>
</template>

<script setup lang="ts">
const props = defineProps({
  for: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  required: {
    type: Boolean,
    default: true,
  },
});
</script>
<style lang="scss">
.label-required {
  display: block;
  .required {
    margin-left: 0.3rem;
    color: red;
  }
}
</style>
