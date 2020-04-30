<template>
  <article class="eight29-post eight29-post-list">
    <a :href="post.link" v-if="post._embedded['wp:featuredmedia'] && settings.displayFeaturedImage">
      <FeaturedImage 
        :image="post._embedded['wp:featuredmedia']"
        :srcset="post.featured_image_srcset"
        :settings="settings"
      ></FeaturedImage>
    </a>
    
    <h4 class="eight29-post-title"><a :href="post.link" v-html="post.title.rendered"></a></h4>
    <div v-if="post.excerpt" class="eight29-post-excerpt" v-html="post.excerpt.rendered"></div>
  </article>
</template>

<script>
import FeaturedImage from '../FeaturedImage.vue';

export default {
  name: 'PostList',
  props: {
    post: Object,
    currentCategoryIds: Array,
    settings: Object
  },
  components: {
    FeaturedImage
  }
}
</script>