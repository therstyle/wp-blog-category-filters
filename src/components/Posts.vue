<template>
  <section class="eight29-post eight29-posts-container">
    <div v-if="posts && posts.length > 0" class="eight29-posts">
      <Post
        v-for="post in posts"
        :key="post.id"
        :post="post"
        :currentCategoryIds="currentCategoryIds"
        :settings="settings"
        v-on:updateCurrentSelection="updateCurrentSelection"
        v-on:update="updateSelected"
        v-on:replaceCurrentSelection="replaceCurrentSelection"
        v-on:replace="replaceSelected"
      ></Post>
    </div>

    <ul v-if="posts && posts.length > 0" class="eight29-pagination">
      <li class="eight29-pagination-prev">
        <AppButton v-on:clickEvent="pagePrev" :disabled="currentPage <= 1">Previous</AppButton>
      </li>

      <li class="eight29-pagination-current">
        <input class="eight29-current-page" v-on:change="checkValidPageNumber" type="number" v-model="currentPageDisplayed" :max="maxPages">
        <span>/ {{ maxPages }}</span>
      </li>

      <li class="eight29-pagination-next">
        <AppButton v-on:clickEvent="pageNext" :disabled="currentPage >= maxPages">Next</AppButton>
      </li>
    </ul>

    <div v-else>
      Sorry, no results. <button v-on:click="resetSelected">Clear Filters</button>
    </div>
  </section>
</template>

<script>
import Post from './Post.vue';
import AppButton from './layout/AppButton.vue';

export default {
  name: 'Posts',
  data() {
    return {
      currentPageDisplayed: 0
    }
  },
  props: {
    posts: Array,
    currentPage: Number,
    maxPages: Number,
    results: Number,
    currentCategoryIds: Array,
    settings: Object
  },
  components: {
    Post,
    AppButton
  },
  methods: {
    resetSelected() {
      console.log('reset');
      this.$emit('reset');
    },
    pagePrev() {
      console.log('pagePrev');
      this.$emit('pagePrev');
    },
    pageNext() {
      console.log('pageNext');
      this.$emit('pageNext');
    },
    updateCurrentSelection(object) {
      this.$emit('updateCurrentSelection', object);
    },
    updateSelected(id) {
      this.$emit('update', id);
    },
    replaceCurrentSelection(object) {
      this.$emit('replaceCurrentSelection', object);
    },
    replaceSelected(id) {
      this.$emit('replace', id);
    },
    checkValidPageNumber() {
      console.log('checking page');
      if (this.currentPageDisplayed > this.maxPages) {
        this.currentPageDisplayed = this.maxPages;
      }
      else if (this.currentPageDisplayed < 1) {
        this.currentPageDisplayed = 1;
      }

      this.$emit('pageUpdate', parseInt(this.currentPageDisplayed));
    }
  },
  mounted() {
    this.currentPageDisplayed = this.currentPage;
  }
}
</script>