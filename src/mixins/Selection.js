export default {
  methods: {
    updateCurrentSelection(object) {
      console.log(object);
      this.$emit('updateCurrentSelection', object);
      this.$emit('update', object.id);
    },
    replaceCurrentSelection(object) {
      console.log(object);
      this.$emit('replaceCurrentSelection', object);
      this.$emit('replace', object.id);
    }
  }
}