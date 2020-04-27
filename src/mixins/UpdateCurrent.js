export default {
  methods: {
    updateCurrent(object) {
      this.$emit('updateCurrent', object);
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
    }
  }
}