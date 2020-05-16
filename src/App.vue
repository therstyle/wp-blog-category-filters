<template>
  <div class="eight29-app" :class="{'no-sidebar' : !settings.displaySideBar}" ref="root">
    <sidebar v-if="settings.displaySideBar" 
      :postData="postData"
      :settings="settings"
      v-on:updateCurrentSelection="updateCurrent"
      v-on:update="updateSelected"
      v-on:searchFieldChange="searchFieldChange"
      v-on:searchRequest="loadPosts"
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
      v-on:clearSearchTerm="clearSearchTerm"
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
    },
    searchParamter() {
      if(this.postData.currentSearchTerm.length !== 0) {
        return `search=${this.postData.currentSearchTerm}&`;
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
        currentSearchTerm: '',
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
      const requestURL = `${wp.home_url}/wp-json/wp/v2/posts?${this.categoryParamter}${this.searchParamter}page=${this.postData.currentPage}&per_page=${this.settings.postsPerPage}&_embed`;

      this.postData.loading = true;
      console.log(requestURL);

      const response = await fetch(requestURL);
      const data = await response.json();
      this.postData.posts = data;
      this.postData.maxPages = parseInt(response.headers.get('X-WP-TotalPages'));
      this.postData.results = parseInt(response.headers.get('X-WP-Total'));
      this.postData.loading = false;
      this.setLocalStorage();
    },
    resetSelected() {
      this.postData.currentCategoryIds = [];
      this.postData.currentPage = 1;
      this.clearSearchTerm();
    },
    addToSelected(id) {
      if (!this.postData.currentCategoryIds.includes(id)) {
        this.postData.currentCategoryIds = [...this.postData.currentCategoryIds, id];
        this.postData.currentPage = 1;
      }
  
      this.loadPosts();
    },
    removeFromSelected(id) {
      let selectedCategories = [...this.postData.currentCategoryIds];
      selectedCategories = selectedCategories.filter(categoryId => categoryId !== id);

      this.postData.currentCategoryIds = selectedCategories;
      this.postData.currentPage = 1;

      this.loadPosts();
    },
    updateSelected(id) {
      if (this.postData.currentCategoryIds.includes(id)) {
        this.postData.categories.forEach(category => { //remove from array
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
        this.postData.categories.forEach(category => { //add to array
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
      this.postData.currentPage = 1;
      this.loadPosts();
    },
    updateCurrent(object) {
      this.postData.currentCategory = object.category;
      this.postData.currentId = object.id;
    },
    setLocalStorage() {
      const selected = JSON.stringify(this.postData.currentCategoryIds);
      localStorage.setItem('selected', selected);
    },
    getLocalStorage() {
      const selected = JSON.parse(localStorage.getItem('selected'));
      return selected;
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
      const currentPage = this.postData.currentPage;
      pageNumber = parseInt(pageNumber);

      if (pageNumber > this.postData.maxPages) { //too large
        this.postData.currentPage = this.postData.maxPages;
        this.loadPosts();
        this.scrollUp();
      }
      else if (pageNumber < 1) { //too small
        this.postData.currentPage = 1;
        this.loadPosts();
        this.scrollUp();
      }
      else if (isNaN(pageNumber) || pageNumber === undefined || pageNumber === '') { // not a number
        this.postData.currentPage = currentPage;
      }
      else { // data passed checks
        this.postData.currentPage = pageNumber;
        this.loadPosts();
        this.scrollUp();
      }
    },
    clearSearchTerm() {
      this.searchFieldChange('');
      this.loadPosts();
    },
    searchFieldChange(value) {
      this.postData.currentSearchTerm = value;
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
    if (this.getLocalStorage()) {
      this.postData.currentCategoryIds = this.getLocalStorage();
    }
    
    this.loadCats();
    this.loadPosts();
  }
}
</script>