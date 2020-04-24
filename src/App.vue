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
      currentCats: [1, 2],
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
      const response = await fetch(`${wp.home_url}/wp-json/wp/v2/posts?categories=${id}`);
      const data = await response.json();
      console.log(data);
      this.posts = data;
    },
    updateCurrent(object) {
      this.currentCategory = object.category;
      this.currentId = object.id;
      this.loadPosts(this.currentId);
    }
  },
  mounted() {
    this.loadCats();
    this.loadPosts(this.currentId);
  }
}
</script>