<template>
  <section class="eight29-posts">
    <template v-if="posts && posts.length > 0">
      <article v-for="post in posts" :key="post.id">
        <h4><a :href="post.link" v-html="post.title.rendered"></a></h4>
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
            :disabled="currentPage >= maxPages"
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
      console.log('pagePrev');
      this.$emit('pagePrev');
    },
    pageNext() {
      console.log('pageNext');
      this.$emit('pageNext');
    }
  }
}
</script>