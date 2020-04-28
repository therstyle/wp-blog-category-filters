<template>
  <div class="eight29-app" :class="{'no-sidebar' : !displaySideBar}" ref="root">
    <sidebar v-if="displaySideBar" 
      :categories="categories"
      :currentCategory="currentCategory"
      :currentCategoryIds="currentCategoryIds"
      :results="results"
      v-on:updateCurrentSelection="updateCurrent"
      v-on:update="updateSelected"
      v-on:reset="resetSelected"
    ></sidebar>

    <posts ref="posts_root"
      :posts="posts"
      :currentPage="currentPage"
      :maxPages="maxPages"
      :results="results"
      :postStyle="postStyle"
      :displayFeaturedImage="displayFeaturedImage"
      :displayAuthor="displayAuthor"
      :displayDate="displayDate"
      :displayCategories="displayCategories"
      :currentCategoryIds="currentCategoryIds"
      :style="postsCssVars"
      v-on:updateCurrentSelection="updateCurrent"
      v-on:update="updateSelected"
      v-on:replaceCurrentSelection="updateCurrent"
      v-on:replace="replaceSelected"
      v-on:reset="resetSelected"
      v-on:pagePrev="pagePrev"
      v-on:pageNext="pageNext"
      v-on:pageUpdate="pageUpdate"
    ></posts>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar';
import Posts from './components/Posts';

export default {
  computed: {
    postsCssVars() {
      return {
        '--posts-per-row': this.postsPerRow
      }
    }
  },
  data() {
    return {
      test: 'something',
      categories: [],
      posts: [],
      currentCategory: 'uncategorized',
      currentId: 1,
      currentPage: 1,
      postsPerPage: parseInt(wp.post_per_page),
      postsPerRow: parseInt(wp.post_per_row),
      postStyle: wp.post_style ? wp.post_style : 'PostCard',
      displayFeaturedImage: wp.display_featured_image === "1" ? true : false,
      displaySideBar: wp.display_sidebar === "1" ? true : false,
      displayAuthor: wp.display_author === "1" ? true : false,
      displayDate: wp.display_date === "1" ? true : false,
      displayCategories: wp.display_categories === "1" ? true : false,
      maxPages: 1,
      currentCategoryIds: [1],
      results: 0,
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
      this.loading = true;
      const response = await fetch(`${wp.home_url}/wp-json/wp/v2/posts?categories=${this.currentCategoryIds}&page=${this.currentPage}&per_page=${this.postsPerPage}&_embed`);
      const data = await response.json();
      this.posts = data;
      this.maxPages = parseInt(response.headers.get('X-WP-TotalPages'));
      this.results = parseInt(response.headers.get('X-WP-Total'));
      this.loading = false;
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
      selectedCategories = selectedCategories.filter(categoryId => categoryId !== id);

      this.currentCategoryIds = selectedCategories.length === 0 ? this.currentCategoryIds = [0] : selectedCategories;

      this.loadPosts();
    },
    updateSelected(id) {
      console.log('replaceSelected');

      if (this.currentCategoryIds.includes(id)) {
        console.log('remove from array');
        this.categories.forEach(category => {
          if (category.id === id) {
            this.removeFromSelected(category.id);

            if (category.children && category.parent === 0) { //top level
              category.children.forEach(child => {
                this.removeFromSelected(child.id)
              });
            }

            if (category.parent !== 0) {
              const childIds = [];
              
              this.categories.forEach(category => { //search for children remove top level parent from selected
                if (category.children) {
                  category.children.forEach(child => {
                    childIds.push(child.id);
                  })

                  if(childIds.includes(id) && category.parent === 0) {
                    this.removeFromSelected(category.id);
                  }
                }
              });
            }
          }
        });
      }
      else {
        console.log('add to array');
        this.categories.forEach(category => {
          if (category.id === id) {
            this.addToSelected(id);

            if (category.children && category.parent === 0) {
              category.children.forEach(child => {
                this.addToSelected(child.id)
              });
            }
          }
        });
      }
    },
    replaceSelected(id) {
      console.log('replaceSelected');
      const ids = [];
      ids.push(id);

      this.categories.forEach(category => {
        if (category.id === id && category.children) {
          category.children.forEach(child => {
            ids.push(child.id);
          })
        }
      })

      this.currentCategoryIds = ids;
      this.loadPosts();
    },
    updateCurrent(object) {
      this.currentCategory = object.category;
      this.currentId = object.id;

      console.log('current IDs')
      console.log(this.currentCategoryIds);
    },
    pagePrev() {
      if (!this.currentPage <= 1) {
        this.currentPage--;
        this.loadPosts();
        this.scrollUp();
      }
    },
    pageNext() {
      if (!(this.currentPage >= this.maxPages)){
        this.currentPage++;
        this.loadPosts();
        this.scrollUp();
      }
    },
    pageUpdate(pageNumber) {
      this.currentPage = pageNumber;
      this.loadPosts();
      this.scrollUp();
    },
    scrollUp() {
      window.scroll({
        behavior: 'smooth',
        left: 0,
        top: this.$refs['root'].offsetTop
      });
    },
  },
  mounted() {
    this.loadCats();
    this.loadPosts();
  }
}
</script>