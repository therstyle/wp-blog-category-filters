<template>
  <div class="eight29-app">
    <sidebar 
      :categories="categories"
      :currentCategory="currentCategory"
      v-on:updateCurrent="updateCurrent"
    ></sidebar>

    <posts
      :posts="posts"
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
      currentCategoryIds: [],
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
      console.log(data);
      this.categories = data;
    },
    loadPosts: async function(id) {
      const response = await fetch(`${wp.home_url}/wp-json/wp/v2/posts?categories=${id}&page=${this.currentPage}`);
      const data = await response.json();
      console.log(data);
      this.posts = data;
    },
    updateCurrent(object) {
      this.currentCategory = object.category;
      this.currentId = object.id;
      this.categories.map(category => {
        if(category.id === this.currentId) {
          category.selected = object.selected;
        }
      });

      this.categories.map(category => {
        if (category.selected) {
          this.currentCategoryIds = [...this.currentCategoryIds, category.id];
        }
        else {
          let selectedCategories = [...this.categories];
          let selectedIds = [];

          selectedCategories = selectedCategories.filter(category => category.selected);
          selectedCategories.map(category => {
            selectedIds.push(category.id);
          });

          this.currentCategoryIds = selectedIds;
        }
      });

      console.log(this.currentCategoryIds);
      this.loadPosts(this.currentCategoryIds);
    }
  },
  mounted() {
    this.loadCats();
    this.loadPosts(this.currentId);
  }
}
</script>