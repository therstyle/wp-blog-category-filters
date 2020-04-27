<template>
  <article class="eight29-post eight29-post-list">
    <FeaturedImage v-if="post._embedded['wp:featuredmedia'] && displayFeaturedImage" :image="post._embedded['wp:featuredmedia']"></FeaturedImage>
    <h4><a :href="post.link" v-html="post.title.rendered"></a></h4>
    <div v-if="post.excerpt" class="excerpt" v-html="post.excerpt.rendered"></div>
  </article>
</template>

<script>
import FeaturedImage from '../FeaturedImage.vue';

export default {
  name: 'PostList',
  props: {
    post: Object,
    featuredImage: Boolean,
    displayFeaturedImage: Boolean,
    currentCategoryIds: Array
  },
  components: {
    FeaturedImage
  }
}
</script>