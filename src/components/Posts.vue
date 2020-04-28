<template>
  <section class="eight29-post eight29-posts-container">
    <div v-if="posts && posts.length > 0" class="eight29-posts">
      <component
        v-for="post in posts"
        :key="post.id" 
        :is="postStyle"
        :post="post"
        :displayFeaturedImage="displayFeaturedImage"
        :displayAuthor="displayAuthor"
        :displayDate="displayDate"
        :displayCategories="displayCategories"
        :currentCategoryIds="currentCategoryIds"
        v-on:updateCurrentSelection="updateCurrentSelection"
        v-on:remove="removeFromSelected"
        v-on:add="addToSelected"
        v-on:replaceCurrentSelection="replaceCurrentSelection"
        v-on:replace="replaceSelected"
      ></component>
    </div>

    <ul v-if="posts && posts.length > 0" class="pagination">
      <li class="page-prev">
        <button 
          v-on:click="pagePrev"
          :disabled="currentPage <= 1"
        >Previous</button>
      </li>

      <li class="page-current">{{ currentPage }}</li>

      <li class="page-next">
        <button 
          v-on:click="pageNext"
          :disabled="currentPage >= maxPages"
        >Next</button>
      </li>
    </ul>

    <div v-else>
      Sorry, no results. <button v-on:click="resetSelected">Clear Filters</button>
    </div>
  </section>
</template>

<script>
import PostCard from './post/PostCard';
import PostList from './post/PostList';

export default {
  name: 'Posts',
  props: {
    posts: Array,
    currentPage: Number,
    maxPages: Number,
    results: Number,
    postStyle: String,
    currentCategoryIds: Array,
    displayFeaturedImage: Boolean,
    displayAuthor: Boolean,
    displayDate: Boolean,
    displayCategories: Boolean
  },
  components: {
    PostCard,
    PostList
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
    removeFromSelected(id) {
      this.$emit('remove', id);
    },
    addToSelected(id) {
      this.$emit('add', id);
    },
    replaceCurrentSelection(object) {
      this.$emit('replaceCurrentSelection', object);
    },
    replaceSelected(id) {
      this.$emit('replace', id);
    }
  }
}
</script>