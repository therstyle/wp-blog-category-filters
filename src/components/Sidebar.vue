<template>
  <form class="eight29-sidebar">
    <ul class="categories">
      <template v-for="category in categories">
        <li v-if="category.parent === 0" :key="category.id">
          <div class="category">
            <input type="checkbox" 
              :value="category.slug" 
              :id="category.slug" 
              :checked="currentCategoryIds.includes(category.id)"
              v-on:change="updateCurrentSelection({category: category.slug, id: category.id, children: category.children, selected: currentCategoryIds.includes(category.id)})"
            >
            <label :for="category.slug">{{ category.name }}</label>
          </div>

          <ul v-if="category.children" class="category-children">
            <li v-for="child in category.children" :key="child.id">
              <div class="category">
                <input type="checkbox" 
                  :value="child.slug" 
                  :id="child.slug" 
                  :checked="currentCategoryIds.includes(child.id)"
                  v-on:change="updateCurrentSelection({category: child.slug, id: child.id, selected: currentCategoryIds.includes(child.id)})"
                >
                <label :for="child.slug">{{ child.name }}</label>
              </div>
            </li>
          </ul>
        </li>
      </template>
    </ul>
  </form>
</template>

<script>
import Selection from '../mixins/Selection';

export default {
  name: 'Sidebar',
  props: {
    categories: Array,
    currentCategory: String,
    currentCategoryIds: Array,
    results: Number
  },
  mixins: [Selection]
}
</script>