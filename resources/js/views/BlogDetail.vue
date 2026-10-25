<template>
  <div class="blog-single-page">
    <section class="page-title bg-1 py-5" style="background: url('/images/bg/22.jpg') 50% 50% / cover no-repeat;">
      <div class="overlay"></div>
      <div class="container position-relative">
        <div class="row">
          <div class="col-md-12 text-center text-white py-3">
            <span class="text-uppercase letter-spacing font-weight-bold text-white-50">Article</span>
            <h1 class="text-capitalize mb-2 text-white">{{ post?.title || 'Blog Post' }}</h1>
            <p class="mb-0 text-white-50">By {{ post?.author_name }} &bull; {{ post?.category }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section blog-single py-5">
      <div class="container">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
        </div>

        <div v-else-if="post" class="row">
          <div class="col-lg-8">
            <div class="single-blog-item bg-white p-4 rounded shadow-sm mb-4">
              <img :src="'/' + post.image" :alt="post.title" class="img-fluid rounded w-100 mb-4" style="max-height: 420px; object-fit: cover;">
              
              <div class="d-flex align-items-center mb-3">
                <span class="badge badge-primary mr-2">{{ post.category }}</span>
                <span class="text-muted small mr-3"><i class="icofont-calendar"></i> {{ new Date(post.created_at).toLocaleDateString() }}</span>
                <span class="text-muted small"><i class="icofont-eye"></i> {{ post.views_count }} Views</span>
              </div>

              <h2 class="font-weight-bold mb-3">{{ post.title }}</h2>
              <div class="divider my-4"></div>

              <div class="blog-content text-dark lh-35" style="white-space: pre-line;">
                {{ post.content }}
              </div>

              <div class="border-top pt-4 mt-5 d-flex justify-content-between align-items-center">
                <router-link to="/blog" class="btn btn-outline-dark btn-round-full btn-sm">
                  &larr; Back to All Articles
                </router-link>
                <router-link to="/appoinment" class="btn btn-main-2 btn-round-full btn-sm">
                  Schedule Consultation &rarr;
                </router-link>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bg-white p-4 rounded shadow-sm mb-4">
              <h5 class="font-weight-bold mb-3">Related Articles</h5>
              <div v-for="rp in recentPosts" :key="rp.id" class="d-flex align-items-center mb-3 pb-2 border-bottom">
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
  name: 'BlogDetail',
  props: ['slug'],
  data() {
    return {
      post: null,
      recentPosts: [],
      loading: true,
    };
  },
  watch: {
    slug() {
      this.fetchPost();
    }
  },
  mounted() {
    this.fetchPost();
  },
  methods: {
    async fetchPost() {
      this.loading = true;
      try {
        const res = await axios.get(`/blog/${this.slug}`);
        this.post = res.data.post;
        this.recentPosts = res.data.recent_posts || [];
      } catch (err) {
        console.error('Error fetching post:', err);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
