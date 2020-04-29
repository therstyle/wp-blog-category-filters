<template>
  <div class="c-btn-wrapper" :class="alignment">
    <button v-if="disabled" v-on:click="clickEvent" class="c-btn" :class="buttonStyle" disabled>
      <span>
        <slot></slot>
      </span>
    </button>

    <button v-else v-on:click="clickEvent" class="c-btn" :class="buttonStyle">
      <span>
        <slot></slot>
      </span>
    </button>
  </div>
</template>

<script>
export default {
  name: "AppButton",
  props: {
    alignment: String,
    buttonStyle: String,
    disabled: Boolean
  },
  methods: {
    clickEvent() {
      this.$emit('clickEvent');
    }
  }
}
</script>