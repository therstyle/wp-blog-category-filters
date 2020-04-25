<template>
  <section class="eight29-posts">
    <template v-if="posts && posts.length > 0">
      <article v-for="post in posts" :key="post.id">
        <h4 v-html="post.title.rendered"></h4>
        <div class="excerpt" v-html="post.excerpt.rendered"></div>
      </article>

      <ul class="pagination">
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
            :disabled="currentPage + 1 >= maxPages"
          >Next</button>
        </li>
      </ul>
    </template>

    <div v-else>
      Sorry, no results. <button v-on:click="resetSelected">Clear Filters</button>
    </div>
  </section>
</template>

<script>
export default {
  name: 'Posts',
  props: {
    posts: Array,
    currentPage: Number,
    maxPages: Number,
    results: Number
  },
  methods: {
    resetSelected() {
      console.log('reset');
      this.$emit('reset');
    },
    pagePrev() {
      this.$emit('pagePrev');
    },
    pageNext() {
      this.$emit('pageNext');
    }
  }
}
</script>