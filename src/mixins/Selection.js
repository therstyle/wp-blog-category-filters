export default {
  methods: {
    updateCurrentSelection(object) {
      this.$emit('updateCurrentSelection', object);
      console.log(object);

      if(object.selected) {
        console.log('remove from array');
        this.$emit('remove', object.id);

        if(object.children) {
          object.children.forEach(child => {
            this.$emit('remove', child.id);
          })
        }
      }
      else {
        console.log('add to array');
        this.$emit('add', object.id);

        if(object.children) {
          object.children.forEach(child => {
            this.$emit('add', child.id);
          })
        }
      }
    },
    replaceCurrentSelection(object) {
      console.log(object);
      this.$emit('replaceCurrentSelection', object);
      this.$emit('replace', object.id);
    }
  }
}