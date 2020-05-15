export default {
  methods: {
    updateCurrentSelection(object) {
      this.$emit('updateCurrentSelection', object);
      this.$emit('update', object.id);
    },
    replaceCurrentSelection(object) {
      this.$emit('replaceCurrentSelection', object);
      this.$emit('replace', object.id);
    }
  }
}