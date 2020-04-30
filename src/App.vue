<template>
  <div class="eight29-app" :class="{'no-sidebar' : !settings.displaySideBar}" ref="root">
    <sidebar v-if="settings.displaySideBar" 
      :postData="postData"
      :settings="settings"
      v-on:updateCurrentSelection="updateCurrent"
      v-on:update="updateSelected"
      v-on:reset="resetSelected"
    ></sidebar>

    <posts ref="posts_root"
      :postData="postData"
      :settings="settings"
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
        '--posts-per-row': this.settings.postsPerRow
      }
    },
    categoryParamter() {
      if(this.postData.currentCategoryIds.length !== 0) {
        return `categories=${this.postData.currentCategoryIds}&`;
      }
      else {
        return '';
      }
    }
  },
  data() {
    return {
      postData: {
        categories: [],
        posts: [],
        currentCategory: 'uncategorized',
        currentId: 1,
        currentPage: 1,
        maxPages: 1,
        currentCategoryIds: [],
        results: 0,
        loading: false,
      },
      settings: {
        postsPerPage: parseInt(wp.post_per_page),
        postsPerRow: parseInt(wp.post_per_row),
        postStyle: wp.post_style ? wp.post_style : 'PostCard',
        displayFeaturedImage: wp.display_featured_image === '1' ? true : false,
        displayFeaturedImageSize: wp.display_featured_image_size ? wp.display_featured_image_size : 'medium',
        displaySideBar: wp.display_sidebar === '1' ? true : false,
        displayAuthor: wp.display_author === '1' ? true : false,
        displayDate: wp.display_date === '1' ? true : false,
        displayCategories: wp.display_categories === '1' ? true : false,
        displayPostCounts: wp.display_post_counts === '1' ? true : false
      },
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
      this.postData.categories = data;
    },
    loadPosts: async function() {
      this.postData.loading = true;
      
      console.log(`${wp.home_url}/wp-json/wp/v2/posts?${this.categoryParamter}page=${this.postData.currentPage}&per_page=${this.settings.postsPerPage}&_embed`);

      const response = await fetch(`${wp.home_url}/wp-json/wp/v2/posts?${this.categoryParamter}page=${this.postData.currentPage}&per_page=${this.settings.postsPerPage}&_embed`);
      const data = await response.json();
      this.postData.posts = data;
      this.postData.maxPages = parseInt(response.headers.get('X-WP-TotalPages'));
      this.postData.results = parseInt(response.headers.get('X-WP-Total'));
      this.postData.loading = false;
    },
    resetSelected() {
      this.postData.currentCategoryIds = [];
      this.loadPosts();
    },
    addToSelected(id) {
      console.log('addToSelected');

      if (!this.postData.currentCategoryIds.includes(id)) {
        this.postData.currentCategoryIds = [...this.postData.currentCategoryIds, id];
      }
  
      console.log(this.postData.currentCategoryIds);
      this.loadPosts();
    },
    removeFromSelected(id) {
      console.log('removeFromSelected');
      let selectedCategories = [...this.postData.currentCategoryIds];
      selectedCategories = selectedCategories.filter(categoryId => categoryId !== id);

      this.postData.currentCategoryIds = selectedCategories;
      this.loadPosts();
    },
    updateSelected(id) {
      console.log('replaceSelected');

      if (this.postData.currentCategoryIds.includes(id)) {
        console.log('remove from array');
        this.postData.categories.forEach(category => {
          if (category.id === id) {
            this.removeFromSelected(category.id);

            if (category.children && category.parent === 0) { //top level
              category.children.forEach(child => {
                this.removeFromSelected(child.id)
              });
            }

            if (category.parent !== 0) {
              const childIds = [];
              
              this.postData.categories.forEach(category => { //search for children remove top level parent from selected
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
        this.postData.categories.forEach(category => {
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

      this.postData.categories.forEach(category => {
        if (category.id === id && category.children) {
          category.children.forEach(child => {
            ids.push(child.id);
          })
        }
      });

      this.postData.currentCategoryIds = ids;
      this.loadPosts();
    },
    updateCurrent(object) {
      this.postData.currentCategory = object.category;
      this.postData.currentId = object.id;

      console.log('current IDs')
      console.log(this.postData.currentCategoryIds);
    },
    pagePrev() {
      if (!this.postData.currentPage <= 1) {
        this.postData.currentPage--;
        this.loadPosts();
        this.scrollUp();
      }
    },
    pageNext() {
      if (!(this.postData.currentPage >= this.postData.maxPages)){
        this.postData.currentPage++;
        this.loadPosts();
        this.scrollUp();
      }
    },
    pageUpdate(pageNumber) {
      this.postData.currentPage = pageNumber;
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