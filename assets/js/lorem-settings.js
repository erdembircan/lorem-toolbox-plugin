/**
 * capitalize first letter of strings
 * @param {string} val string to be capitalized
 * @returns {string} capitalized string
 */
function cap(val) {
  return val[0].toUpperCase() + val.slice(1);
}

// Vue filter - capitalize only first letter of a single/group of string(s)
Vue.filter('capF', cap);

// Vue filter - capitalize all words of a given string
Vue.filter('capAF', val =>
  val
    .split(' ')
    .map(cap)
    .join(' ')
);

// setting-row component
Vue.component('setting-row', {
  props: ['element', 'domProps'],
  render(h) {
    const options = {
      domProps: this.domProps,
    };
    return h(this.element, options);
  },
});

// settings-table component
Vue.component('settings-table', {
  template: '#loremSettingsTable',
  props: ['settings'],
});

// generate-posts component
Vue.component('generate-posts', {
  template: '#generatePosts',
  props: ['ajaxurl', 'ajaxactiongenerate', 'nonce', 'ajaxactiondelete'],
  data() {
    return {
      postCount: 5,
      fetching: false,
    };
  },
  watch: {
    fetching(status) {
      this.$refs.submitButton.disabled = status;
      this.$refs.deleteButton.disabled = status;
    },
  },
  methods: {
    handleForm() {
      this.fetching = true;
      return fetch(this.ajaxurl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `nonce=${this.nonce}&post_count=${this.postCount}&action=${this.ajaxactiongenerate}`,
      })
        .then(resp => {
          this.fetching = false;
          return resp.json();
        })
        .then(j => {
          this.$emit('fetched', j.data);
        });
    },
    deleteGenerated() {
      this.fetching = true;
      const url = `${this.ajaxurl}?action=${this.ajaxactiondelete}`;
      return fetch(url)
        .then(resp => resp.json())
        .then(j => {
          this.fetching = false;
          this.$emit('fetched', j.data);
        });
    },
  },
});

// main Vue instance
new Vue({
  data: loremSettings.data,
  components: ['settings-table', 'generate-posts'],
  methods: {
    updatePostCount(data) {
      if (data) {
        this.post_count = data.totalCount;
      }
    },
  },
}).$mount('#lorem_app');
