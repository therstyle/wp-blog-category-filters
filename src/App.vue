<template>
  <div class="eight29-app">
    <sidebar 
      :categories="categories"
      :currentCategory="currentCategory"
      :currentCategoryIds="currentCategoryIds"
      v-on:updateCurrent="updateCurrent"
      v-on:remove="removeFromSelected"
      v-on:add="addToSelected"
    ></sidebar>

    <posts
      :posts="posts"
      v-on:reset="resetSelected"
    ></posts>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar';
import Posts from './components/Posts';

export default {
  data() {
    return {
      test: 'something',
      categories: [],
      posts: [],
      currentCategory: 'uncategorized',
      currentId: 1,
      currentPage: 1,
      currentCategoryIds: [1],
      loading: false
    }
  },
  name: 'App',
  components: {
    Sidebar,
    Posts
  },
  methods: {
    loadCats: async function() {
      const response = await fetch(`${wp.home_url}/wp-json/eight29/v1/categories`);
      const data = await response.json();
      this.categories = data;
    },
    loadPosts: async function() {
      const response = await fetch(`${wp.home_url}/wp-json/wp/v2/posts?categories=${this.currentCategoryIds}&page=${this.currentPage}`);
      const data = await response.json();
      this.posts = data;
    },
    resetSelected() {
      this.currentCategoryIds = [1];
      this.loadPosts();
    },
    addToSelected(id) {
      console.log('addToSelected');

      if (!this.currentCategoryIds.includes(id)) {
        this.currentCategoryIds = [...this.currentCategoryIds, id];
      }
  
      console.log(this.currentCategoryIds);
      this.loadPosts();
    },
    removeFromSelected(id) {
      console.log('removeFromSelected');
      let selectedCategories = [...this.currentCategoryIds];
      selectedCategories = selectedCategories.filter(categoryId => !id);

      this.currentCategoryIds = selectedCategories.length === 0 ? this.currentCategoryIds = [0] : selectedCategories;

      this.loadPosts();
    },
    updateCurrent(object) {
      this.currentCategory = object.category;
      this.currentId = object.id;

      console.log('current IDs')
      console.log(this.currentCategoryIds);
    }
  },
  mounted() {
    this.loadCats();
    this.loadPosts();
  }
}
</script>