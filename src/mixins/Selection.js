export default {
  methods: {
    updateSelected(object) {
      this.$emit('updateSelected', object);
    },
    replaceSelected(object) {
      this.$emit('replaceSelected', object);
    }
  }
}