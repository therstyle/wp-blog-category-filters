<template>
  <figure class="featured-image">
    <img :src="image[0].media_details.sizes['post-thumbnail']" :srcset="srcset" :alt="image[0].title.rendered">
  </figure>
</template>

<script>
export default {
  name:"FeaturedImage",
  computed: {
    srcset() {
      const sizes = this.image[0].media_details.sizes;
      const srcset = [];

      Object.keys(sizes).forEach(size => {
        srcset.push(`${sizes[size].source_url} ${sizes[size].width}w`);
      });

      srcset.reverse();

      return srcset.join();
    }
  },
  props: {
    image: Array
  },
  mounted() {
    
  }
}
</script>