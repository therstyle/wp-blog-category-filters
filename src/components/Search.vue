<template>
  <div class="eight29-search">
    <input type="search" placeholder="Search..." v-on:keyup.enter="searchRequest(searchTerm)" v-model="searchTerm">
  </div>
</template>

<script>
export default {
  name: 'Search',
  computed: {
    searchTerm: {
      get() {
        return this.currentSearchTerm;
      },
      set(value) {
        this.$emit('searchFieldChange', value);
        return value;
      }
    }
  },
  props: {
    currentSearchTerm: String
  },
  methods: {
    searchRequest(value) {
      if (this.currentSearchTerm.length > 0) {
        this.$emit('searchRequest', value);
        console.log('search request');
      }
    }
  }
}
</script>