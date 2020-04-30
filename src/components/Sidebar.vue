<template>
  <form class="eight29-sidebar">
    <ul class="eight29-sidebar-detail">
      <li>
        <span>Posts ({{ results }})</span>
      </li>
      
      <li>
        <a class="eight29-reset" v-on:click.prevent="reset">
          <span>Reset</span>
          <ResetIcon></ResetIcon>
        </a>
      </li>
    </ul>
    
    <ul class="eight29-categories">
      <template v-for="category in categories">
        <li v-if="category.parent === 0" :key="category.id">
          <div class="eight29-category">
            <input type="checkbox" 
              :value="category.slug" 
              :id="category.slug" 
              :checked="currentCategoryIds.includes(category.id)"
              v-on:change="updateCurrentSelection({category: category.slug, id: category.id, children: category.children, selected: currentCategoryIds.includes(category.id)})"
            >
            <label :for="category.slug">{{ category.name }} 
              <span v-if="settings.displayPostCounts" class="eight29-category-count">({{ category.count }})</span>
            </label>
          </div>

          <ul v-if="category.children" class="eight29-category-children">
            <li v-for="child in category.children" :key="child.id">
              <div class="eight29-category">
                <input type="checkbox" 
                  :value="child.slug" 
                  :id="child.slug" 
                  :checked="currentCategoryIds.includes(child.id)"
                  v-on:change="updateCurrentSelection({category: child.slug, id: child.id, selected: currentCategoryIds.includes(child.id)})"
                >
                <label :for="child.slug">{{ child.name }}
                  <span v-if="settings.displayPostCounts" class="eight29-category-count">({{ child.count }})</span>
                </label>
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
import ResetIcon from '../components/icons/ResetIcon.vue';

export default {
  name: 'Sidebar',
  components: {
    ResetIcon
  },
  props: {
    categories: Array,
    currentCategory: String,
    currentCategoryIds: Array,
    results: Number,
    settings: Object
  },
  mixins: [Selection],
  methods: {
    reset() {
      this.$emit('reset');
    }
  }
}
</script>