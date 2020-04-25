<template>
  <form class="eight29-sidebar">
    <ul class="categories">
      <template v-for="category in categories">
        <li v-if="category.parent === 0" :key="category.id">
          <input type="checkbox" 
          :value="category.slug" 
          :id="category.slug" 
          :checked="category.selected"
          v-on:change="updateCurrent({category: category.slug, id: category.id, selected: !category.selected})"
        >
        <label :for="category.slug">{{ category.name }}</label>

          <ul v-if="category.children" class="category-children">
            <li v-for="child in category.children" :key="child.id">
              <input type="checkbox" 
                :value="child.slug" 
                :id="child.slug" 
                :checked="child.selected"
                v-on:change="updateCurrent({category: child.slug, id: child.id, selected: !child.selected})"
              >
              <label :for="category.slug">{{ child.name }}</label>
            </li>
          </ul>
        </li>
      </template>
    </ul>
  </form>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    categories: Array,
    currentCategory: String
  },
  methods: {
    updateCurrent(obj) {
      this.$emit('updateCurrent', obj);
    }
  }
}
</script>