<template>
  <section class="eight29-posts">
    <template v-if="posts && posts.length > 0">
      <article v-for="post in posts" :key="post.id">
        <h4 v-html="post.title.rendered"></h4>
        <div class="excerpt" v-html="post.excerpt.rendered"></div>
      </article>

      <ul class="pagination"></ul>
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
    posts: Array
  },
  methods: {
    resetSelected() {
      console.log('reset');
      this.$emit('reset');
    }
  }
}
</script>