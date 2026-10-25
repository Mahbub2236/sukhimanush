<template>
  <div class="blog-page">
    <section class="page-title bg-1 py-5" style="background: url('/images/bg/22.jpg') 50% 50% / cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative">
        <div class="row">
          <div class="col-md-12 text-center text-white py-3">
            <span class="text-uppercase letter-spacing font-weight-bold text-white-50">Health & Wellness Insights</span>
            <h1 class="text-capitalize mb-2 text-white">Medical News & Articles</h1>
            <p class="mb-0 text-white-50">Read practical health advice and articles curated by our physician team.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section blog-wrap py-5 gray-bg">
      <div class="container">
        <div class="row">
          <!-- Main Blog Posts List -->
          <div class="col-lg-8">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-danger" role="status"></div>
            </div>

            <div v-else>
              <div v-for="post in posts" :key="post.id" class="card border-0 shadow-sm rounded overflow-hidden mb-4">
                <img :src="'/' + post.image" :alt="post.title" class="card-img-top" style="max-height: 350px; object-fit: cover;">
                <div class="card-body p-4">
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge badge-primary mr-2">{{ post.category }}</span>
                    <small class="text-muted"><i class="icofont-calendar mr-1"></i> {{ new Date(post.created_at).toLocaleDateString() }}</small>
                    <small class="text-muted ml-3"><i class="icofont-user-alt-4 mr-1"></i> {{ post.author_name }}</small>
                  </div>
                  <h3 class="card-title font-weight-bold mb-3">
                    <router-link :to="'/blog/' + post.slug" class="text-dark text-decoration-none">
                      {{ post.title }}
                    </router-link>
                  </h3>
                  <p class="card-text text-muted">{{ post.excerpt }}</p>
                  <router-link :to="'/blog/' + post.slug" class="btn btn-main-2 btn-round-full btn-sm">
                    Read Full Article <i class="icofont-simple-right ml-1"></i>
                  </router-link>
                </div>
              </div>

              <div v-if="posts.length === 0" class="alert alert-info text-center">
                No blog articles found matching your criteria.
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <!-- Search Widget -->
            <div class="bg-white p-4 rounded shadow-sm mb-4">
              <h5 class="font-weight-bold mb-3">Search Articles</h5>
              <div class="input-group">
                <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." @keyup.enter="fetchPosts">
                <div class="input-group-append">
                  <button class="btn btn-main-2" @click="fetchPosts"><i class="icofont-search"></i></button>
                </div>
              </div>
            </div>

            <!-- Categories Widget -->
            <div class="bg-white p-4 rounded shadow-sm mb-4">
              <h5 class="font-weight-bold mb-3">Categories</h5>
              <ul class="list-unstyled mb-0">
                <li class="mb-2">
                  <a href="#!" @click.prevent="filterCategory('')" :class="{ 'font-weight-bold text-danger': selectedCategory === '' }">
                    All Categories
                  </a>
                </li>
                <li v-for="cat in categories" :key="cat" class="mb-2">
                  <a href="#!" @click.prevent="filterCategory(cat)" :class="{ 'font-weight-bold text-danger': selectedCategory === cat }">
                    {{ cat }}
                  </a>
                </li>
              </ul>
            </div>

            <!-- Recent Posts Widget -->
            <div class="bg-white p-4 rounded shadow-sm mb-4">
              <h5 class="font-weight-bold mb-3">Recent Posts</h5>
              <div v-for="rp in recentPosts" :key="rp.id" class="d-flex align-items-center mb-3">
                <img :src="'/' + rp.image" :alt="rp.title" class="rounded mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                <div>
                  <h6 class="mb-0">
                    <router-link :to="'/blog/' + rp.slug" class="text-dark small font-weight-bold">
                      {{ rp.title }}
                    </router-link>
                  </h6>
                  <small class="text-muted">{{ new Date(rp.created_at).toLocaleDateString() }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Blog',
  data() {
    return {
      posts: [],
      categories: [],
      recentPosts: [],
      searchQuery: '',
      selectedCategory: '',
      loading: true,
    };
  },
  mounted() {
    this.fetchPosts();
  },
  methods: {
    async fetchPosts() {
      this.loading = true;
      try {
        const res = await axios.get('/blog', {
          params: {
            category: this.selectedCategory,
            search: this.searchQuery,
          },
        });
        this.posts = res.data.posts?.data || res.data.posts || [];
        this.categories = res.data.categories || [];
        this.recentPosts = res.data.recent_posts || [];
      } catch (err) {
        console.error('Error fetching blogs:', err);
      } finally {
        this.loading = false;
      }
    },
    filterCategory(cat) {
      this.selectedCategory = cat;
      this.fetchPosts();
    },
  },
};
</script>
