<template>
  <section class="eight29-posts-container">
    <div v-if="postData.posts && postData.posts.length > 0" class="eight29-posts">
      <Post
        v-for="post in postData.posts"
        :key="post.id"
        :post="post"
        :currentCategoryIds="postData.currentCategoryIds"
        :settings="settings"
        v-on:replaceSelected="replaceSelected"
        v-on:clearSearchTerm="clearSearchTerm"
      ></Post>
    </div>

    <ul v-if="postData.posts && postData.posts.length > 0" class="eight29-pagination">
      <li class="eight29-pagination-prev">
        <AppButton v-on:clickEvent="pagePrev" :disabled="postData.currentPage <= 1">Previous</AppButton>
      </li>

      <li class="eight29-pagination-current">
        <input ref="pageInput" class="eight29-current-page" v-on:click="selectText(currentPageDisplayed)" type="number" v-model="currentPageDisplayed" :max="postData.maxPages">
        <span>/ {{ postData.maxPages }}</span>
      </li>

      <li class="eight29-pagination-next">
        <AppButton v-on:clickEvent="pageNext" :disabled="postData.currentPage >= postData.maxPages">Next</AppButton>
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
  computed: {
    currentPageDisplayed: {
      get() {
        return this.postData.currentPage;
      },
      set(value) {
        this.$emit('pageUpdate', parseInt(value));
        return value;
      }
    }
  },
  props: {
    postData: Object,
    settings: Object
  },
  components: {
    Post,
    AppButton
  },
  methods: {
    resetSelected() {
      this.$emit('reset');
    },
    pagePrev() {
      this.$emit('pagePrev');
    },
    pageNext() {
      this.$emit('pageNext');
    },
    replaceSelected(object) {
      this.$emit('replaceSelected', object);
    },
    selectText(value) {
      this.$refs.pageInput.select();
    },
    searchFieldChange(value) {
      this.$emit('searchFieldChange', value);
    },
    clearSearchTerm() {
      this.$emit('clearSearchTerm');
    }
  }
}
</script>