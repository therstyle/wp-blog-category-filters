<template>
  <figure>
    <img :src="image[0].media_details.sizes[settings.displayFeaturedImageSize].source_url" :srcset="srcset" :alt="image[0].title.rendered">
  </figure>
</template>

<script>
export default {
  name:"FeaturedImage",
  computed: {
    // srcset() {
    //   const sizes = this.image[0].media_details.sizes;
    //   const srcset = [];

    //   Object.keys(sizes).forEach(size => {
    //     srcset.push(`${sizes[size].source_url} ${sizes[size].width}w`);
    //   });

    //   srcset.reverse();

    //   return srcset.join();
    // }
  },
  props: {
    image: Array,
    srcset: String,
    settings: Object
  },
  mounted() {
    
  }
}
</script>