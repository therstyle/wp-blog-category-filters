<template>
  <article class="eight29-post eight29-post-card">
    <a :href="post.link" v-if="post._embedded['wp:featuredmedia'] && displayFeaturedImage"><FeaturedImage :image="post._embedded['wp:featuredmedia']"></FeaturedImage></a>

    <div v-if="displayDate || displayAuthor" class="eight29-post-detail">
      <time v-if="displayDate">{{post.formatted_date}}</time>
      <span v-if="displayAuthor" class="author">{{ post._embedded.author[0].name }}</span>
    </div>
    <h4 class="eight29-post-title"><a :href="post.link" v-html="post.title.rendered"></a></h4>
    <div v-if="displayCategories" class="eight29-post-categories">
      <a 
        v-for="category in post._embedded['wp:term'][0]" 
        :key="category.id"
        :data-attribute-selected="currentCategoryIds.includes(category.id)"
        v-on:click.prevent="replaceCurrentSelection({ category: category.slug, id: category.id, selected: currentCategoryIds.includes(category.id) })"
      >{{ category.name }}</a>
    </div>

    <div v-if="post.excerpt" class="eight29-post-excerpt" v-html="post.excerpt.rendered"></div>
  </article>
</template>

<script>
import FeaturedImage from '../FeaturedImage.vue';
import Selection from '../../mixins/Selection';

export default {
  name: 'PostCard',
  props: {
    post: Object,
    displayFeaturedImage: Boolean,
    displayAuthor: Boolean,
    displayDate: Boolean,
    displayCategories: Boolean,
    currentCategoryIds: Array
  },
  components: {
    FeaturedImage
  },
  mixins: [Selection]
}
</script>