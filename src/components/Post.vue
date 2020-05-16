<template>
  <article class="eight29-post" :class="{'eight29-post-card' : settings.postStyle === 'PostCard', 'eight29-post-list' : settings.postStyle === 'PostList'}">
    <a  v-if="post._embedded['wp:featuredmedia'] && settings.displayFeaturedImage" :href="post.link" class="eight29-featured-image">
      <FeaturedImage 
        :image="post._embedded['wp:featuredmedia']"
        :srcset="post.featured_image_srcset"
        :settings="settings"
      ></FeaturedImage>
    </a>

    <div class="eight29-post-body">
      <div v-if="settings.displayDate || settings.displayAuthor" class="eight29-post-detail">
        <time v-if="settings.displayDate">{{post.formatted_date}}</time>
        <span v-if="settings.displayAuthor" class="author">{{ post._embedded.author[0].name }}</span>
      </div>
      <h4 class="eight29-post-title"><a :href="post.link" v-html="post.title.rendered"></a></h4>
      <div v-if="settings.displayCategories" class="eight29-post-categories">
        <a 
          v-for="category in post._embedded['wp:term'][0]" 
          :key="category.id"
          :data-attribute-selected="currentCategoryIds.includes(category.id)"
          v-on:click.prevent="replaceCurrentSelection({ category: category.slug, id: category.id, selected: currentCategoryIds.includes(category.id) }); clearSearchTerm();"
        >{{ category.name }}</a>
      </div>

      <div v-if="post.excerpt" class="eight29-post-excerpt" v-html="theExcerpt"></div>
    </div>
  </article>
</template>

<script>
import FeaturedImage from './FeaturedImage.vue';
import Selection from '../mixins/Selection';

export default {
  name: 'Post',
  computed: {
    theExcerpt() {
      const content = this.post.excerpt.rendered;
      return content.replace('[&hellip;]', `... <a href="${this.post.link}">Read More</a>`);
    }
  },
  props: {
    post: Object,
    currentCategoryIds: Array,
    settings: Object
  },
  components: {
    FeaturedImage
  },
  mixins: [Selection],
  methods: {
    clearSearchTerm() {
      this.$emit('clearSearchTerm');
    }
  }
}
</script>