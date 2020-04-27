<template>
  <figure class="featured-image">
    <img :src="image[0].media_details.sizes.full.source_url" :alt="image[0].title.rendered">
  </figure>
</template>

<script>
export default {
  name:"FeaturedImage",
  props: {
    image: Array
  }
}
</script>